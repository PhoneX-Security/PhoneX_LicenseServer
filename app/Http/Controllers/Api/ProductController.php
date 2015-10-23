<?php namespace Phonex\Http\Controllers\Api;

use Illuminate\Http\Request;
use Phonex\Http\Controllers\Controller;
use Phonex\Http\Middleware\MiddlewareAttributes;
use Phonex\Http\Requests;
use Phonex\Model\Product;
use Phonex\User;
use Phonex\Utils\ClientCertData;


class ProductController extends Controller {

    const VERSION = 1;

	public function __construct(){
	}

    public function getAppleProducts(Request $request)
    {
        return $this->getProducts($request, "apple");
    }

    public function getGoogleProducts(Request $request)
    {
        return $this->getProducts($request, "google");
    }

    private function getProducts(Request $request, $platform)
    {
        $query = Product::with(['appPermissions', 'permissionParent', 'permissionParent.appPermissions']);
        if ($platform){
            $query = $query->where(['platform' => $platform]);
        }

        $results = $query->get();
        foreach($results as $product){
            // for some products load permissions from their permission parent
            $product->loadPermissionsFromParentIfMissing();
        }

        $obj = new \stdClass();
        $obj->version = self::VERSION;
        $obj->products = $results;

        return json_encode($obj);
    }

    public function getAvailableProducts(Request $request)
    {
        $user = $request->attributes->get(MiddlewareAttributes::CLIENT_CERT_AUTH_USER);
        if (!$user){
            abort(401);
        }

        $obj = new \stdClass();
        $obj->version = self::VERSION;
        $obj->products = [];

        $licenses = $user->activeLicenseProducts;

        $subscriptionLicenses = $licenses->filter(function($lic){
            return !$lic->product->isConsumable();
        });

        $subBasic = Product::findByName("inapp.subs.basic.month");
        $subPremium = Product::findByName("inapp.subs.premium.month");

        $consCall30 = Product::findByName("inapp.cons.call30");
        $consCall60 = Product::findByName("inapp.cons.call60");

        $consFiles25 = Product::findByName("inapp.cons.files25");
        $consFiles50 = Product::findByName("inapp.cons.files50");

        // if no there are no active licenses, offer these two
        if ($subscriptionLicenses->count() == 0){
            $obj->products[] = $subBasic;
            $obj->products[] = $subPremium;
            return json_encode($obj);
        }

        $containsOnlyBasic = true;
        foreach ($subscriptionLicenses as $lic){
            if ($lic->product->name != $subBasic->name){
                $containsOnlyBasic = false;
            }
        }

        // only basic subscription - in this case, offer consumables
        if ($containsOnlyBasic){
            $obj->products[] = $consCall30;
            $obj->products[] = $consCall60;
            $obj->products[] = $consFiles25;
            $obj->products[] = $consFiles50;
            return json_encode($obj);
        } else {

            // all other cases (legacy cases), only offer premium
            $obj->products[] = $subPremium;
            return json_encode($obj);
        }
    }

    public function getPurchasedProducts(Request $request)
    {
        // user should be pushed from the middleware
        $user = $request->attributes->get(MiddlewareAttributes::CLIENT_CERT_AUTH_USER);
        if (!$user){
            abort(401);
        }

        $ret =  new \stdClass();
        $ret->version = 1;
        $ret->licenses = [];
        foreach($user->activeLicenseProducts as $lic){
            $obj = new \stdClass();

            $obj->license_id = $lic->id;
            $obj->product_name = $lic->product->name;
            $obj->product_type = $lic->product->license_type_id ? $lic->product->licenseType->name : null;

            if ($lic->starts_at) {$obj->starts_at = $lic->starts_at->timestamp;}
            if ($lic->expires_at) {$obj->expires_at = $lic->expires_at->timestamp;}

            $ret->licenses[] = $obj;
        }
        return json_encode($ret);
    }
}
