<?php namespace Phonex\Http\Controllers\Api;

use Phonex\Http\Controllers\Controller;
use Phonex\Http\Requests;
use Phonex\Product;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller {

	public function __construct(){
	}

    public function index(){
        return Product::with('productPrices')->get()->toJson();
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
