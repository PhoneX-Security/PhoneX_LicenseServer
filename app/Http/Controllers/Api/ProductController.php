<?php namespace Phonex\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Phonex\Http\Controllers\Controller;
use Phonex\Http\Middleware\MiddlewareAttributes;
use Phonex\Http\Requests;
use Phonex\License;
use Phonex\Model\ApiRequest;
use Phonex\Model\Product;
use Phonex\User;
use Phonex\Utils\ClientCertData;


class ProductController extends Controller {

    const VERSION = 1;

	public function __construct(){
	}

    public function getTest(Request $request)
    {
        $user = $request->attributes->get(MiddlewareAttributes::CLIENT_CERT_AUTH_USER);
        if (!$user){
            abort(401);
        }
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

        // Differentiate products according to platform
        $platform = $request->get('platform', 'apple');
        $isApple = $platform === 'apple';

        // Set locale according to GET parameter to retrieve localized product names
        $locales = explode(',', $request->get('locales', 'en'));
        $locale = $locales[0];
        app()->setLocale($locale);

//        $user = User::findByUsername('test-internal3');

        $obj = new \stdClass();
        $obj->version = self::VERSION;
        $obj->products = [];

        $licenses = $user->activeLicenseProducts;

        $subscriptionLicenses = $licenses->filter(function($lic){
            return !$lic->product->isConsumable();
        });

        $subBasic = Product::findByNameWithPerms("inapp.subs.basic.month");
        $subBasic->loadPermissionsFromParentIfMissing();
        $subPremium = null;
        if ($isApple){
            $subPremium = Product::findByNameWithPerms("inapp.subs.premium.e20");
        } else {
            // New product had to be released for Google, price cannot be changed
            $subPremium = Product::findByNameWithPerms("inapp.subs.premium.e10");
        }

//
        $subPremium->loadPermissionsFromParentIfMissing();

        $consCall30 = Product::findByNameWithPerms("inapp.cons.call30");
        $consCall30->loadPermissionsFromParentIfMissing();
        $consCall60 = Product::findByNameWithPerms("inapp.cons.call60");
        $consCall60->loadPermissionsFromParentIfMissing();

//        $consFiles25 = Product::findByNameWithPerms("inapp.cons.files25");
//        $consFiles25->loadPermissionsFromParentIfMissing();
//        $consFiles50 = Product::findByNameWithPerms("inapp.cons.files50");
//        $consFiles50->loadPermissionsFromParentIfMissing();

        // if no there are no active licenses
        if ($subscriptionLicenses->count() == 0){
            // offer basic
            $obj->products[] = $subBasic;

            // if user had basic previously, offer premium as well
            $pastLicenses = $user->pastLicenseProducts;
            $hadBasic = false;
            foreach ($pastLicenses as $lic){
                if ($lic->product->name == $subBasic->name){
                    $hadBasic = true;
                    break;
                }
            }

            if ($hadBasic){
                $obj->products[] = $subPremium;
            }

            return json_encode($obj);
        }

        $containsOnlyBasic = true;
        foreach ($subscriptionLicenses as $lic){
            if ($lic->product->name != $subBasic->name){
                $containsOnlyBasic = false;
            }
        }

        // only basic subscription - in this case, offer consumables + premium
        if ($containsOnlyBasic){
//            $obj->products[] = $subBasic;
            $obj->products[] = $subPremium;
            $obj->products[] = $consCall30;
            $obj->products[] = $consCall60;
//            $obj->products[] = $consFiles25;
//            $obj->products[] = $consFiles50;
            return json_encode($obj);
        } else {
            // all other cases (legacy etc), only offer premium
//            $obj->products[] = $subPremium;
            return json_encode($obj);
        }
    }

    /**
     * List arbitrary user's products by providing license ids
     * @param Request $request
     * @return string
     */
    public function getLicensesProducts(Request $request)
    {
        $user = $request->attributes->get(MiddlewareAttributes::CLIENT_CERT_AUTH_USER);
        if (!$user){
            abort(401);
        }

        // Set locale according to GET parameter to retrieve localized product names
        $locales = explode(',', $request->get('locales', 'en'));
        $locale = $locales[0];
        app()->setLocale($locale);

        $license_products = [];
        $licenseIds = explode(',', $request->get('ids', ''));
        if ($licenseIds){
            foreach($licenseIds as $licenseId){
                // consider licenses that belong to that particular user
                $license = License::where(['id' => $licenseId, 'user_id' => $user->id])->first();
                if ($license && $license->product){
                    $license_products[$licenseId] = $license->product;
                }
            }
        }
        return json_encode($license_products);
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
            $obj->type = $lic->product->type;

            if ($lic->starts_at) {$obj->starts_at = $lic->starts_at->timestamp;}
            if ($lic->expires_at) {$obj->expires_at = $lic->expires_at->timestamp;}

            $ret->licenses[] = $obj;
        }
        return json_encode($ret);
    }
}
