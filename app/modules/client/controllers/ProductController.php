<?php

class ProductController extends BaseController
{

    public function detail($slug, $pid)
    {
        $productObj  = Product::findOrFail($pid);
        $dataView['productObj'] = $productObj;
        $dataView['images'] = $productObj->images;
        $dataView['options'] = $productObj->option()->where('enable', '1')->get();
        return View::make('client::product.detail', $dataView);
        // if(!is_null($productObj)){
        // 	$dataView['productObj'] = $productObj;
	       //  $dataView['images'] = $productObj->images;
	       //  $dataView['options'] = $productObj->option;
	       //  return View::make('client::product.detail', $dataView);
        // }else{
        // 	App::abort(404);
        // }
        
    }
}