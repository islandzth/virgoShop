<?php

class AdCategoryController extends AdBaseController
{

    public function __construct()
    {
    }

    public function index($args = null)
    {

    }
    public function createCat(){
        $viewData['msg'] = null;
        if(Request::isMethod('post')){
            $role = array(
                    'nameCategory' => 'required',
                    'sexCategory' => 'required|min:1',
                    'descriptionCat' => 'max:150'
                );
            $validator = Validator::make(Input::all(), $role);
            if($validator->fails()){
                $viewData['msg'] = $validator->messages();
            }else{
                $catObj = Category::getInstance();
                $catObj->addCategory(Input::get('nameCategory'), Common::createStringSlug(Input::get('nameCategory')), Input::get('sexCategory'), Input::get('descriptionCat'), null);
                $viewData['msg'] = 'Tạo danh mục '.Input::get('nameCategory').' thành công!';
            }
        }
        return View::make('admin::category.add', $viewData);
    }
    public function view(){
        $id = Input::get('id');
        if($id){
            // get category
            $category = Category::find($id);
            return View::make('admin::category.view',array('category'=>$category, 'Category'=>Category::getInstance()));
        }
        return Redirect::guest('/notfound');
    }

    public function manage()
    {
        $viewData = [];
        $catList = Category::all();
        $viewData['listCat'] = $catList;
        return View::make('admin::category.list',$viewData);
    }

    public function edit($id)
    {
        // $id = Input::get('id');
        
        $categoryInc = Category::getInstance();
        $viewData = [];
        $category = Category::find($id);
        if(count($category) <= 0){
            App::abort(404);
        }
        $viewData['category'] = $category; 
        if (Request::isMethod('post')){
            $role = array(
                    'nameCategory' => 'required',
                    'sexCategory' => 'required|min:1',
                    'descriptionCat' => 'max:150'
                );
            $validator = Validator::make(Input::all(), $role);
            if($validator->fails()){
                $viewData['msg'] = $validator->messages();
            }else{
                $category->name = Input::get('nameCategory');
                $category->sex = Input::get('sexCategory');
                $category->enable = Input::get('enableCategory');
                $category->discription = Input::get('descriptionCat');
                $category->identity = Common::createStringSlug(Input::get('nameCategory'));
                $category->save();
            }

            $category = Category::find($id);
            $viewData['category'] = $category;
            $viewData['msg'] = 'Thay đổi thông tin thành công!';
        }

        return View::make('admin::category.edit',$viewData);
    }


    // ajax for load attributes not belongto category
    function remainAttrs(){
        $categoryId = Input::get('id');

        $category = Category::find($categoryId);
        // select all attributes of category
        $catAttrs = $category->attributes->toArray();
        $catAttrIds = __::pluck($catAttrs,'attribute_id');
        
        // find remain attributes
        $remainAttrs = [];
        if(empty($catAttrIds)){
            $remainAttrs = Attribute::all()->toArray();
        }else{
            $remainAttrs = Attribute::whereNotIn('attribute_id',$catAttrIds)->get()->toArray();
        }
        $remainAttrs = __::map($remainAttrs,function($n){
            return array('attribute_id'=>$n['attribute_id'],'name'=>$n['name']);
        });
        return Response::json(array('attrs' => $remainAttrs ));
    }

    // function for add attribute for category
    public function addCategoryAttribute(){
        if (Request::isMethod('post')){
            $catId = Input::get('category_id');
            $attrId = Input::get('attribute_id');
            $required = Input::get('required',0);
            
            if(empty($catId) || empty($attrId)){
                return Response::json(array('errs' => array('Data invalid.'))); 
            }
            // find category
            $category = Category::find($catId);

            // attach  new attributes
            $category->attributes()->attach([$attrId=>['required'=>$required]]);

            return Response::json(array('succeed' => true));
        }else{
            return Response::json(array('errs' => array('Method doesn\'t accept.')));
        }
    }

    // function for delete attribute for category
    public function deleteAttrs(){
        $catId = Input::get('catid');
        $attrId = Input::get('attrid');

        if(empty($catId) | empty($attrId)){
            return Response::json(array('errs' => array('Data invalid.'))); 
        }
        // get category
        $category = Category::find($catId);
        // detach attribute for category
        $category->attributes()->detach($attrId);
        
        return Response::json(array('succeed' => true));
    }
}
