<?php



Route::group(array('prefix'=>'admincp'), function () {
	// App::missing(function($exception)
	// {
	//     return Response::view('admin::error.missing', array(), 404);
	// });
	Route::get('/login', array('as' => 'loginView', 'uses' => 'AdAdminController@login'));

	Route::post('/checklogin', array('as' => 'checkLogin', 'uses' => 'AdAdminController@checkLogin'));


	Route::group(array('before' => 'isAdmin'),function(){
		Route::group(array('prefix' => 'userAdmin'), function(){
			Route::get('/', array('as' => 'manageAdminUser', 'uses' => 'AdAdminController@manage'));
			Route::get('/createUser', array('as' => 'regUser', 'uses' => 'AdAdminController@createUser'));
			Route::post('/createUser', array('as' => 'regUser', 'uses' => 'AdAdminController@createUser'));
		});
		Route::get('/', array('as' => 'adminPage', 'uses' => 'AdAdminController@index'));
		Route::get('/notfound', array('as' => 'notfound', 'uses' => 'AdErrorController@notfound'));
		Route::get('/ajax/products', array('as' => 'viewCProductsAjax', 'uses' => 'AdProductController@ajaxProducts'));

		Route::group(array('prefix' => 'categories'), function(){
			Route::get('/', array('as' => 'manageCategories', 'uses' => 'AdCategoryController@manage'));
			Route::get('/create', array('as' => 'createCategories', 'uses' => 'AdCategoryController@createCat'));
			Route::post('/create', array('as' => 'doCreateCategories', 'uses' => 'AdCategoryController@createCat'));
			Route::get('/edit/{id}', array('as' => 'editCategories', 'uses' => 'AdCategoryController@edit'));
			Route::post('/edit/{id}', array('as' => 'doEditCategories', 'uses' => 'AdCategoryController@edit'));
		});
		Route::group(array('prefix' => 'product'), function(){
			Route::get('/', array('as' => 'manageProduct', 'uses' => 'AdProductController@manage'));
			Route::get('/create', array('as' => 'createProduct', 'uses' => 'AdProductController@create'));
			Route::post('/create', array('as' => 'doCreateProduct', 'uses' => 'AdProductController@create'));
			Route::get('/edit/{id}', array('as' => 'editProduct', 'uses' => 'AdProductController@edit'));
			Route::post('/edit/{id}', array('as' => 'doEditProduct', 'uses' => 'AdProductController@edit'));
			Route::get('/disableattr/{id}', array('as' => 'disableProduct', 'uses' => 'AdProductController@disableAttr'));
			Route::get('/changeStatus', array('as' => 'disableProduct', 'uses' => 'AdProductController@changeStatusOption'));
		});
		Route::group(array('prefix' => 'ajax'), function(){
			Route::post('/uploadimagefile', array('as' => 'ajaxUploadFileImage', 'uses' => 'AdAjaxController@handleUploadImage'));
			Route::get('/changesttorder', array('as' => 'ajaxChangeSttOrder', 'uses' => 'AdAjaxController@changeSttOrder'));
			Route::get('/rmmetaorder', array('as' => 'ajaxRmMetaOrder', 'uses' => 'AdAjaxController@removeMetaOrder'));
		});
		Route::group(array('prefix' => 'order'), function(){
			Route::get('/manage', array('as' => 'orderManage', 'uses' => 'AdOrderController@manage'));
			Route::get('/edit/{id}', array('as' => 'orderEdit', 'uses' => 'AdOrderController@edit'));
			Route::post('/edit/{id}', array('as' => 'orderEdit', 'uses' => 'AdOrderController@edit'));
		});
		Route::get('/web-config', array('as' => 'webConfigs', 'uses' => 'AdWebConfig@manage'));
		Route::get('/web_config/remove', array('as' => 'removeWebConfig', 'uses' => 'AdWebConfig@remove'));
		Route::post('/web-config/add', array('as' => 'addWebConfig', 'uses' => 'AdWebConfig@add'));
		Route::post('/web-config/edit', array('as' => 'editWebConfig', 'uses' => 'AdWebConfig@edit'));
	});
});
/*Route::get('/admin', function () {
    return View::make('admin::index');
});*/