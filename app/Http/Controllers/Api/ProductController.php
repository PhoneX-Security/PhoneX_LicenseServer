<?php namespace Phonex\Http\Controllers\Api;

use Illuminate\Http\Request;
use Phonex\Http\Controllers\Controller;
use Phonex\Http\Requests;
use Phonex\Model\Product;


class ProductController extends Controller {

	public function __construct(){
	}

    public function index(Request $request){
        $platform = $request->has('platform') ? $request->get('platform') : null;

//        $query = Product::with('productPrices')->with('appPermissions');
        $query = Product::with(['appPermissions', 'permissionParent', 'permissionParent.appPermissions']);
        if ($platform){
            $query = $query->where(['platform' => $platform]);
        }

        $results = $query->get();
        foreach($results as $result){
            // for some products load permissions from their permission parent
            if (!$result->appPermission && $result->permissionParent){
                // rewrite originally loaded relation
                $result->setRelation('appPermissions', $result->permissionParent->appPermissions);
            }
        }
        return $results->toJson();
    }

//    public function show($id)
//    {
//        $product = Product::with('productPrices')->find($id);
//        if ($product == null){
//            throw new NotFoundHttpException;
//        }
//
//        return $product->toJson();
//    }
}
