<?php

class IndexController extends BaseController
{

    public function index()
    {
        $viewsData['product'] = Product::where('enable', 1)->orderBy('created_at', 'DESC')->paginate(4);
        return View::make('client::index.index', $viewsData);
    }

}
