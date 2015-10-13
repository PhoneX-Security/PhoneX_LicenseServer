<?php namespace Phonex\Http\Controllers\Api;

use Phonex\Http\Controllers\Controller;
use Phonex\Http\Requests;
use Phonex\Model\Product;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Old api, not authenticated
class ProductControllerLegacy extends Controller {

    const PRODUCT_INDIVIDUAL_MONTH_NAME = "individual_full_month";
    const PRODUCT_INDIVIDUAL_YEAR_NAME = "individual_full_year";

	public function __construct(){
	}

    public function index(){
        $products = Product::with('productPrices')->where('platform', 'direct')->get();

        // legacy naming
        $mapping = [
            "full_month" => "individual_full_month",
            "full_year" => "individual_full_year"
        ];

//        dd($products);
        foreach($products as $product){
            if (array_key_exists($product->name, $mapping)){
                $product->name = $mapping[$product->name];
            }
        }

        return  $products->toJson();
    }

    public function show($id)
    {
        $product = Product::with('productPrices')->find($id);
        if ($product == null){
            throw new NotFoundHttpException;
        }

        return $product->toJson();
    }
}
