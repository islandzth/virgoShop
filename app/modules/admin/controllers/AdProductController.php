<?php

class AdProductController extends AdBaseController
{

    public function __construct()
    {
    }
    public function manage(){
        $viewData = [];
        $viewData['listCat'] = Category::all();
        // dd(Input::get('filter_cat'));   
        $filter_cat = Input::get('filter_cat');
        $filter_keyword = Input::get('filter_keyword');

        $viewData['listProduct'] = Product::where('product_id', '>', 1);
        if(!empty($filter_cat)){
            $viewData['listProduct'] = $viewData['listProduct']->where('category', Input::get('filter_cat'));
        }
        if(!empty($filter_keyword)){
            $viewData['listProduct'] = $viewData['listProduct']->where('name', 'LIKE', '%'.$filter_keyword.'%');
        }
        $viewData['listProduct'] = $viewData['listProduct']->paginate(10);
        $viewData['filter_cat'] = $filter_cat;
        $viewData['filter_keyword'] = $filter_keyword;
        return View::make('admin::product.list', $viewData);
    }
    public function create(){
        $viewData['msg'] = null;
        //load category
        $viewData['listCat'] = Category::all();
        if(Request::isMethod('post')){
            $role = array(
                    'nameProduct' => 'required',
                    'catProduct' => 'required|min:1',
                    'keyWordProduct' => 'max:150',
                    'descriptionProduct' => 'max:150',
                    'set_defualt_image' => 'required|min:10'
                );
            $validator = Validator::make(Input::all(), $role);
            if($validator->fails()){
                $viewData['msg'] = $validator->messages();
            }else{
                $arrColor = Input::get('colorProduct');
                $arrPrice = Input::get('priceProduct');
                $arrImage = Input::get('name_image');
                $arrColorObj = [];
                if(count($arrImage) > 0){
                    if(count($arrColor) > 0){
                        $countOkColor = 0;
                        for($i = 0; $i < count($arrColor); $i++){
                            if(!is_null($arrColor[$i])  && is_numeric($arrPrice[$i])){
                                $countOkColor++;
                                $colorObj = new ProductOption();
                                $colorObj->size = $arrColor[$i];
                                $colorObj->gia = $arrPrice[$i];
                                $colorObj->enable = 1;
                                array_push($arrColorObj, $colorObj);
                            }
                        }
                        if($countOkColor <= 0){
                            $viewData['msg'] = 'Tạo ít nhất một màu và có giá';
                        }else{
                            DB::beginTransaction();
                            try{
                                $productObj = Product::getInstance();
                                $productObj->name = Input::get('nameProduct');
                                $productObj->identity = Common::createStringSlug(Input::get('nameProduct'));
                                $productObj->category = Input::get('catProduct');
                                $productObj->meta_keyword = Input::get('keyWordProduct');
                                $productObj->description = Input::get('descriptionProduct');
                                $productObj->image = Input::get('set_defualt_image');
                                $productObj->save();
                                $productId = $productObj->product_id;
                                for($i = 0; $i < $countOkColor; $i++){
                                    $arrColorObj[$i]->product_id = $productId;
                                    $arrColorObj[$i]->save();
                                }
                                for ($i=0; $i < count($arrImage); $i++) { 
                                    $imgObj = new ProductImage();
                                    $imgObj->product_id = $productId;
                                    $imgObj->image_name = $arrImage[$i];
                                    $imgObj->save();
                                }
                                
                            }catch(\Exception $e){
                                $viewData['msg'] = $e->getMessage();
                                DB::rollback();
                            }
                            DB::commit();
                            $viewData['msg'] = 'Thêm sản phẩm: <b>'.Input::get('nameProduct').'</b> thành công';
                        }
                    }else{
                        $viewData['msg'] = 'Tạo ít nhất một màu và có giá';
                    }
                }else{
                    $viewData['msg'] = 'Chưa đăng hình sản phẩm';
                }
                
            }
        }
        return View::make('admin::product.add_product', $viewData);
    }
    public function edit($pId){
        $viewData['msg'] = null;
        $viewData['err'] = 0;
        //load category
        $viewData['listCat'] = Category::all();
        $productObj = Product::find($pId);
        if(is_null($productObj)){
            $viewData['msg'] = 'Sản phẩm không tồn tại';
            $viewData['err'] = 1;
            App::abort(404);
        }else{
            if(Request::isMethod('post')){
                $role = array(
                        'nameProduct' => 'required',
                        'catProduct' => 'required|min:1',
                        'keyWordProduct' => 'max:150',
                        'descriptionProduct' => 'max:150',
                        'set_defualt_image' => 'required|min:10'
                    );
                $validator = Validator::make(Input::all(), $role);
                if($validator->fails()){
                    $viewData['msg'] = $validator->messages();
                }else{
                    $arrColor = Input::get('colorNewProduct');
                    $arrPrice = Input::get('priceNewProduct');
                    $arrImage = Input::get('name_image');
                    $arrColorObj = [];
                    //update info product
                    DB::beginTransaction();
                    try{
                        $productObj->name = Input::get('nameProduct');
                        $productObj->identity = Common::createStringSlug(Input::get('nameProduct'));
                        $productObj->category = Input::get('catProduct');
                        $productObj->meta_keyword = Input::get('keyWordProduct');
                        $productObj->description = Input::get('descriptionProduct');
                        $productObj->image = Input::get('set_defualt_image');
                        $productObj->save();
                        //add new color
                        if(count($arrColor) > 0){
                            $countOkColor = 0;
                            for($i = 0; $i < count($arrColor); $i++){
                                if(!is_null($arrColor[$i])  && is_numeric($arrPrice[$i])){
                                    $countOkColor++;
                                    $colorObj = new ProductOption();
                                    $colorObj->size = $arrColor[$i];
                                    $colorObj->gia = $arrPrice[$i];
                                    $colorObj->enable = 1;
                                    array_push($arrColorObj, $colorObj);
                                }
                            }
                            if($countOkColor > 0){
                                
                                    $productId = $productObj->product_id;
                                    for($i = 0; $i < $countOkColor; $i++){
                                        $arrColorObj[$i]->product_id = $pId;
                                        $arrColorObj[$i]->save();
                                    }
                                    
                                    
                                
                            }
                        }
                        //handle store image
                        if(count($arrImage) > 0){
                            ProductImage::where('product_id', '=', $pId)->delete();
                            for ($i=0; $i < count($arrImage); $i++) { 
                                $imgObj = new ProductImage();
                                $imgObj->product_id = $pId;
                                $imgObj->image_name = $arrImage[$i];
                                $imgObj->save();
                            }
                        }else{
                            $viewData['msg'] = 'Chưa đăng hình sản phẩm';
                            throw new Exception('Chưa đăng hình sản phẩm');
                        }
                    }catch(\Exception $e){
                        $viewData['msg'] = $e->getMessage();
                        DB::rollback();
                    }
                    DB::commit();
                    
                }
            }   
            $productObj = Product::find($pId);
            $viewData['productObj'] = $productObj;
            $viewData['productImage'] = $productObj->images;
            $viewData['ProductOption'] = $productObj->option;

            return View::make('admin::product.edit_product', $viewData);
        }
    }
    public function changeStatusOption(){
        $arrRep = [];
        if(Input::get('status') == 1 || Input::get('status') == 0){
            if(!is_null(Input::get('pid'))){
                $product = Product::where('product_id', '=', Input::get('pid'))->first();
                if(count($product) > 0){
                    DB::beginTransaction();
                    try{
                        foreach ($product->option as $optionObj) {
                            $optionObj->enable = Input::get('status');
                            $optionObj->save();
                        }
                        //change stauts product
                        $product->enable = Input::get('status');
                        $product->save();
                        $arrRep['err'] = 0;
                    }catch(\Exception $e){
                        $viewData['msg'] = $e->getErrors();
                        DB::rollback();
                        $arrRep['err'] = 1;
                    }
                    DB::commit();
                    //reponse data
                    $arrRep['pid'] = Input::get('pid');
                }else{
                    $arrRep['err'] = 1;
                }
                $arrRep['pid'] = Input::get('pid');
                $arrRep['status'] = Input::get('status');
                $arrRep['all'] = 1;
                $arrRep['err'] = 0;
                echo json_encode($arrRep);
            }else if(!is_null(Input::get('vid'))){
                $option = ProductOption::where('id', '=', Input::get('vid'))->first();
                if(count($option) > 0){
                    $option->enable = Input::get('status');
                    $option->save();
                    $arrRep['err'] = 0;
                    $checkStatusOption = ProductOption::where('product_id', $option->product_id)->where('enable', 1)->count();
                    if($checkStatusOption <= 0){
                        //disable product
                        $proObj = Product::find($option->product_id);
                        if(count($proObj)>0){
                            $proObj->enable = 0;
                            $proObj->save();
                        }else{
                            $proObj->enable = 1;
                            $proObj->save();
                        }
                    }
                }else{
                    $arrRep['err'] = 1;
                }
                $arrRep['vid'] = Input::get('vid');
                $arrRep['pid'] = $option->product_id;
                $arrRep['status'] = Input::get('status');
                $arrRep['all'] = 0;
                $arrRep['err'] = 0;
                echo json_encode($arrRep);
            }else{
                $arrRep['err'] = 1;
                echo json_encode($arrRep);
            }
        }else{
            $arrRep['err'] = 1;
            echo json_encode($arrRep);
        }
        
    }
    public function disableAttr($pId){
        
    }
}
