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
