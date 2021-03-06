<?php namespace Phonex\Http\Controllers\Api;

use Bus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;
use Mail;
use Phonex\Http\Controllers\Controller;
use Phonex\Http\Middleware\MiddlewareAttributes;
use Phonex\Http\Requests;
use Phonex\Jobs\IssueProductLicense;
use Phonex\License;
use Phonex\Model\ApiRequest;
use Phonex\Model\OrderInapp;
use Phonex\Model\OrderInappState;
use Phonex\Model\OrderPlayInapp;
use Phonex\Model\Product;
use Phonex\Purchase\PlayPurchase;
use Phonex\User;
use Phonex\Utils\ClientCertData;


class PurchaseController extends Controller {

    const VERSION = 1;

    const RESULT_OK = 0; // appstore also returns 0 when successful
    const RESULT_ERR_JSON_PARSING = 1;
    const RESULT_ERR_MISSING_FIELDS = 2;
    const RESULT_ERR_INVALID_USER = 3;
    const RESULT_ERR_INVALID_PRODUCT = 4;
    const RESULT_ERR_TEST = 5;
    const RESULT_ERR_INVALID_SIGNATURE = 6;
    const RESULT_ERR_EXISTING_ORDER_ID = 7;
    const RESULT_MULTIPLE_PURCHASES = 8; // when saving multiple purchases

    /* These codes are coming from apple itunes store */
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

    /**
     * Main processing method for Google Play store transactions
     * @param Request $request
     * @return string
     */
    public function postAndroidPaymentVerification(Request $request)
    {
        $jsonReq = $request->get('request');
        Log::info("postAndroidPaymentVerification", [$request, $jsonReq]);


        $user = $request->attributes->get(MiddlewareAttributes::CLIENT_CERT_AUTH_USER);
        if (!$user){
            return $this->getResponse(self::RESULT_ERR_INVALID_USER);
        }

        // Parse json
//        $jsonReq = $request->get('request');
        $jsonObject = json_decode($jsonReq, true);
        $playPurchase = PlayPurchase::fromJsonObject($jsonObject);

        if (!Str::startsWith($playPurchase->orderId, 'testorder')){
            $this->notifyUsByEmail($playPurchase->productId, 'Android', $user->username);
        }

        // Process Ticket
        $returnCode = $this->processPlayPurchase($playPurchase, $user);
        if ($returnCode == self::RESULT_OK){
            Log::info("received google payment verification", [$returnCode, $jsonReq]);
        } else {
            Log::error("received google payment verification", [$returnCode, $jsonReq]);
        }

        return $this->getResponse($returnCode);
    }

    /**
     * Processes array of payments
     * @param Request $request
     * @return string
     */
    public function postAndroidPaymentsVerification(Request $request)
    {
        $user = $request->attributes->get(MiddlewareAttributes::CLIENT_CERT_AUTH_USER);
        if (!$user){
            return $this->getResponse(self::RESULT_ERR_INVALID_USER);
        }

        $jsonReq = $request->get('request');
        $jsonObjects = json_decode($jsonReq, true);
        if ($jsonObjects == null || !is_array($jsonObjects)){
            return $this->getResponse(self::RESULT_ERR_JSON_PARSING);
        }

        $responseArray = [];
        // Process purchases one by one
        foreach($jsonObjects as $jsonObject){
            $playPurchase = PlayPurchase::fromJsonObject($jsonObject);
            $errCode = $this->processPlayPurchase($playPurchase, $user);
            $responseArray[] = $errCode;
        }
        Log::info("received google payment verifications", [$responseArray, $jsonReq]);
        return $this->getResponseForMultiplePurchases($responseArray);
    }

    private function processPlayPurchase(PlayPurchase $playPurchase, User $user)
    {
        Log::info("processPlayPurchase");
        // TODO make this as a transaction

        if ($playPurchase === null){
            return self::RESULT_ERR_JSON_PARSING;
        }
        if (!$playPurchase->verifySignature()){
            return self::RESULT_ERR_INVALID_SIGNATURE;
        }
        $renewalCount = 0;
        // Order id - if subscription, it can be non-unique - check existing subscriptions with the same
        // if they are expired. If so, issue new product (valid renewal)
        // January 2016: replaced by purchased token

        // use Purchase token for checking duplicities
        if ($playPurchase->itemType == 'subs'){
            $playOrders = OrderPlayInapp::getByPurchaseTokenHash($playPurchase->purchaseToken);
//            Log::debug("playOrders", [$playOrders]);
            if ($playOrders->count() > 0){
                $now = Carbon::now();
                $nonColliding  = true;
                $maxRenewalCount = 0;
                foreach ($playOrders as $playOrder){
                    if ($playOrder->license_id && $playOrder->license){
                        Log::debug("InApp: Checking if subscription ticket collides with lic", [$playOrder->license->id]);
                        $maxRenewalCount = max(intval($playOrder->renewal_count), $maxRenewalCount);
                        if ($playOrder->license->expires_at->gt($now) && $playOrder->license->starts_at->lt($now)){
                            // if colliding expiration, do not replay this ticket - otherwise it means that subscription is renewed
                            $nonColliding = false;
                            break;
                        }
                    }
                }
                if (!$nonColliding){
                    Log::info("InApp: Subscription ticket is colliding with existing license expiration, not issuing new license");
                    return self::RESULT_ERR_EXISTING_ORDER_ID;
                }

                $renewalCount = $maxRenewalCount + 1;
                Log::info("InApp: No collision found, renewing license", [$renewalCount]);
            }
        } else {
            // Otherwise it has to be unique
//            if (OrderPlayInapp::orderIdExists($playPurchase->orderId)){
            if (OrderPlayInapp::purchaseTokenHashExists($playPurchase->purchaseToken)){
                return self::RESULT_ERR_EXISTING_ORDER_ID;
            }
        }

        // First save the order
        $orderPlayInapp = $playPurchase->createDatabaseModel($user, $renewalCount);
        $orderPlayInapp->save();

        Log::info("received google payment verification");

        // Find the product
        // TODO temporary for testing retrieve product name from developer payload, later switch to productId
        $dp = json_decode($playPurchase->developerPayload);
        $productName = $dp->sku;
        $product = Product::where(["name" => $productName])->first();

//        $product = Product::where(["name" => $playPurchase->productId])->first();
        if (!$product){
            return self::RESULT_ERR_INVALID_PRODUCT;
        }

        // Issue product
        $issueProductCommand = new IssueProductLicense($user, $product);
        $license = Bus::dispatch($issueProductCommand);

        // Update order
        $orderPlayInapp->update(['license_id' => $license->id]);

        return self::RESULT_OK;
    }

    private function getResponse($code)
    {
        $response = new \stdClass();
        $response->responseCode = $code;
        return json_encode($response);
    }

    private function getResponseForMultiplePurchases(array $allResponseCodes)
    {
        $response = new \stdClass();
        $response->responseCode = self::RESULT_MULTIPLE_PURCHASES;
        $response->purchasesResponseCodes = $allResponseCodes;
        return json_encode($response);
    }

    private function notifyUsByEmail($productId, $platform, $username)
    {
        Log::info("New order has been purchased, notifying by email.");
        Mail::send('emails.order_has_landed', compact('productId', 'platform', 'username'),
            function($message)
            {
                $message->from('order@phone-x.net', 'PhoneX');
                $message->to(["vedeni@phone-x.net"])->subject("Nova objednavka v aplikacii");
            });
    }

    /***************************************/
    /*************  APPLE  *****************/
    /***************************************/

    /**
     * Main processing method of Apple AppStore transactions
     * @param Request $request
     * @return string
     */
    public function postApplePaymentVerification(Request $request)
    {
        $response = new \stdClass();

        // Retrieve user from client certificate
        $clientCertData = null;
        try {
            $clientCertData = ClientCertData::parseFromRequest($request);
        } catch (\Exception $e){
            Log::error("ClientCertData::parseFromRequest", [$e]);
            $response->responseCode = self::RESULT_ERR_INVALID_USER;
            return json_encode($response);
        }
        Log::info("Apple payment verification initiated", [$clientCertData]);

        $user = User::where(['email' => $clientCertData->sip])->first();
        if (!$user){
            Log::error("cannot load user");
            $response->responseCode = self::RESULT_ERR_INVALID_USER;
            return json_encode($response);
        }

        // Parse json
        $jsonReq = $request->get('request');
        $json = json_decode($jsonReq);

        if ($json === null){
            Log::error("json_decode failed", [$jsonReq, $json]);
            $response->responseCode = self::RESULT_ERR_JSON_PARSING;
            return json_encode($response);
        }

        // Create order
        $purchaseDate = null;
        $originalPurchaseDate = null;
        $subscriptionExpirationDate = null;
        $cancellationDate = null;
        // for restored transactions (apple may replay all transactions when e.g. new switching to a new device)
        $transactionRestored = false;
        try {
            $payment = $json->payment;
            $appVersion = isset($payment->appVersion) ? json_encode($payment->appVersion) : null;
            $purchaseDate = Carbon::createFromTimestampUTC($payment->product->purchaseDate);
            $originalPurchaseDate = Carbon::createFromTimestampUTC($payment->product->originalPurchaseDate);

            if (isset($payment->product->cancellationDate)){
                $cancellationDate = Carbon::createFromTimestampUTC($payment->product->cancellationDate);
            }
            if (isset($payment->product->subscriptionExpirationDate)){
                $subscriptionExpirationDate = Carbon::createFromTimestampUTC($payment->product->subscriptionExpirationDate);
            }

            if (isset($payment->transaction) && isset($payment->transaction->transactionRestored)){
                $transactionRestored = $payment->transaction->transactionRestored == 1;
            }

            // check if this is already in DB with issued license
            if (OrderInapp::where(
                    ['original_tsx_id' => $payment->product->originalTsxId,
                        'tsx_id' => $payment->product->tsxId,
                        'original_purchase_date' => $originalPurchaseDate,
                        'purchase_date' => $purchaseDate])
                    ->whereNotNull('license_id')
                    ->count() > 0){
                Log::warning("Apple purchase - order was already stored in database, not issuing a new license.");
                $response->responseCode = self::RESULT_OK;
                return json_encode($response);
            }

            $order = new OrderInapp();
            $order->tsx_id = $payment->product->tsxId;
            $order->created_at = Carbon::now();
            $order->original_tsx_id = $payment->product->originalTsxId;
            $order->purchase_date = $purchaseDate;
            $order->original_purchase_date = $originalPurchaseDate;
            $order->subscription_expiration_date = $subscriptionExpirationDate;
            $order->cancellation_date = $cancellationDate;
            $order->state = OrderInappState::PURCHASE_CREATED;
            $order->platform = $payment->p;
            $order->product_name = $payment->product->productId;
//            $order->receipt = $payment->receipt;
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

        if ($transactionRestored){
            // in case of restoration, do not issue new license
            $order->update(['state' => OrderInappState::PURCHASE_RESTORED]);
            $response->responseCode = self::RESULT_OK;
            return json_encode($response);
        }

        $order->update(['state' => OrderInappState::PURCHASE_VERIFIED]);
        Log::info("APPLE In app purchase made", [$order->id]);

//        $product = Product::where(["name" => $order->product_name,
//            'platform' => ProductPlatformTypes::APPLE])->first();

        $product = Product::where(["name" => $order->product_name])->first();
        if (!$product){
            $response->responseCode = self::RESULT_ERR_INVALID_PRODUCT;
            return json_encode($response);
        }//

        // Issue license - product on specific user for given time period, update policies in subscribers table
        $c = new IssueProductLicense($user, $product);
        $c->startingAt($purchaseDate);
        if ($subscriptionExpirationDate){
            $c->setExpiration($subscriptionExpirationDate);
        }

        $this->notifyUsByEmail($order->product_name, 'ios', $user->username);

        $lic = Bus::dispatch($c);
        $order->update(['license_id' => $lic->id]);

        $response->responseCode = self::RESULT_OK;
        return json_encode($response);
    }
}