<?php

class CategoryController extends BaseController
{
    public function index($categoryId, $slug)
    {
        $viewsData['product'] = Product::where('enable', 1)->where('category', $categoryId)->orderBy('created_at', 'DESC')->paginate(4);
        return View::make('client::category.index', $viewsData);
    }    
}
