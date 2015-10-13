<?php namespace Phonex\Http\Controllers\Api;

use Illuminate\Http\Request;
use Phonex\Http\Controllers\Controller;
use Phonex\Http\Requests;
use Phonex\Model\Product;


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
}
