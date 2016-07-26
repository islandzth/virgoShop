<?php
Route::pattern('id', '[0-9]+');
Route::pattern('slug', '^(?!admincp(?:\/|$))[a-z0-9-]+$');
Route::pattern('product_id', '[a-z0-9]+');

App::missing(function($exception)
{
    return Response::view('client::error.missing', array(), 404);
});
Route::get('/', array('as' => 'index', 'uses' => 'IndexController@index'));
Route::get('/notfound', array('as' => 'notFound', 'uses' => 'NotfoundController@index'));

// Route::get('/user/login', array('as' => 'userlogin', 'uses' => 'UserController@login'));
// Route::post('/user/login', array('as' => 'userlogin', 'uses' => 'UserController@login'));
// Route::get('/user/logout', array('as' => 'userlogin', 'uses' => 'UserController@logout'));


//product detail
Route::get('/product/{slug}-{product_id}.html', array('as' => 'productdetail', 'uses' => 'ProductController@detail'));

//category
Route::get('/category/{id}-{slug}.html', array('as' => 'categoryDetail', 'uses' => 'CategoryController@index'));


Route::get('/ajax/addOrder', array('as' => 'ajaxAddVidToOrder', 'uses' => 'AjaxController@addVidToOrder'));
Route::get('/ajax/rmsptocart', array('as' => 'ajaxRmProducToCart', 'uses' => 'AjaxController@rmProductToCart'));
Route::get('/ajax/getdistrict', array('as' => 'ajaxGetDistrict', 'uses' => 'AjaxController@getDistict'));


Route::get('/order/viewcart', array('as' => 'viewCart', 'uses' => 'OrderController@viewCart'));
Route::get('/order/checkout', array('as' => 'checkout', 'uses' => 'OrderController@checkout'));
Route::get('/order/success', array('as' => 'success', 'uses' => 'OrderController@success'));
Route::post('/order/success', array('as' => 'success', 'uses' => 'OrderController@success'));


// Route::group(array('before' => 'auth'), function () {

 
// });
