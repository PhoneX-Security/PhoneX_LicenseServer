<?php namespace Phonex\Http\Controllers\Api;

use Bus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;
use Phonex\Http\Controllers\Controller;
use Phonex\Http\Requests;
use Phonex\Jobs\IssueProductLicense;
use Phonex\Model\OrderInapp;
use Phonex\Model\OrderInappState;
use Phonex\Model\Product;
use Phonex\Model\ProductPlatformTypes;
use Phonex\User;
use Phonex\Utils\ClientCertData;


class PurchaseController extends Controller {

    const VERSION = 1;

    const RESULT_OK = 0; // appstore also returns 0 when successful
    const RESULT_ERR_JSON_PARSING = 1;
    const RESULT_ERR_MISSING_FIELDS = 2;
    const RESULT_ERR_INVALID_USER = 3;
    const RESULT_ERR_INVALID_PRODUCT = 4;


    /*These codes are coming from apple itunes store*/
    // The App Store could not read the JSON object you provided.
    const RESULT_APPSTORE_CANNOT_READ = 21000;
    // The data in the receipt-data property was malformed or missing.
    const RESULT_DATA_MALFORMED = 21002;
    // The receipt could not be authenticated.
    const RESULT_RECEIPT_NOT_AUTHENTICATED = 21003;
    // The shared secret you provided does not match the shared secret on file for your account.
    // Only returned for iOS 6 style transaction receipts for auto-renewable subscriptions.
    const RESULT_SHARED_SECRET_NOT_MATCH = 21004;
    // The receipt server is not currently available.
    const RESULT_RECEIPT_SERVER_UNAVAILABLE = 21005;
    // This receipt is valid but the subscription has expired. When this status code is returned to your server, the receipt data is also decoded and returned as part of the response.
    // Only returned for iOS 6 style transaction receipts for auto-renewable subscriptions.
    const RESULT_RECEIPT_VALID_BUT_SUB_EXPIRED = 21006;
    // This receipt is from the test environment, but it was sent to the production environment for verification. Send it to the test environment instead.
    // special case for app review handling - forward any request that is intended for the Sandbox but was sent to Production, this is what the app review team does
    const RESULT_SANDBOX_RECEIPT_SENT_TO_PRODUCTION = 21007;
    // This receipt is from the production environment, but it was sent to the test environment for verification. Send it to the production environment instead.
    const RESULT_PRODUCTION_RECEIPT_SENT_TO_SANDBOX = 21008;

	public function __construct(){
	}

    public function postApplePaymentVerification(Request $request)
    {
        $response = new \stdClass();

        // Retrieve user from client certificate
        $clientCertData = null;
        try {
            $clientCertData = ClientCertData::parseFromRequest($request);
        } catch (\Exception $e){
            $response->responseCode = self::RESULT_ERR_INVALID_USER;
            return json_encode($response);
        }

        $user = User::where(['email' => $clientCertData->sip])->first();
        if (!$user){
            $response->responseCode = self::RESULT_ERR_INVALID_USER;
            return json_encode($response);
        }

        // Parse json
        $json = json_decode($request->get('request'));
        if ($json === null){
            $response->responseCode = self::RESULT_ERR_JSON_PARSING;
            return json_encode($response);
        }

        // Create order
        try {
            $payment = $json->payment;
            $appVersion = isset($payment->appVersion) ? json_encode($payment->appVersion) : null;

            $order = new OrderInapp();
            $order->tsx_id = $payment->product->tsxId;
            $order->original_tsx_id = $payment->product->originalTsxId;
            $order->purchase_date = Carbon::createFromTimestampUTC($payment->product->originalPurchaseDate);
            $order->state = OrderInappState::PURCHASE_CREATED;
            $order->platform = $payment->p;
            $order->product_name = $payment->product->productId;
            $order->receipt = $payment->receipt;
            if (isset($payment->guuid)){
                $order->guuid = $payment->guuid;
            }
            $order->app_version = $appVersion;
            $order->user_id = $user->id;
            $order->save();

        } catch (\Exception $e){
            Log::error("Error during payment verification", [$e]);
            $response->responseCode = self::RESULT_ERR_MISSING_FIELDS;
            return json_encode($response);
        }

        // TODO verification for later
//        $validator = new Validator(Validator::ENDPOINT_SANDBOX);
//        $validator->setReceiptData($order->receipt);
//        $validator->validate();

        $order->update(['state' => OrderInappState::PURCHASE_VERIFIED]);

        Log::info("In app purchase made", [$order->id]);

        $product = Product::where(["name" => $order->product_name, 'platform' => ProductPlatformTypes::APPLE])->wherefirst();
        if (!$product){
            $response->responseCode = self::RESULT_ERR_INVALID_PRODUCT;
            return json_encode($response);
        }

        // Issue license - product on specific user for given time period, update policies in subscribers table
        $c = new IssueProductLicense($user, $product);
        $lic = Bus::dispatch($c);
        $order->update(['license_id' => $lic->id]);

        $response->responseCode = self::RESULT_OK;
        return json_encode($response);
    }
}