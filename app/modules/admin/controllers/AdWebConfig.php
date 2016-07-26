<?php

class AdWebConfig extends AdBaseController
{

    public function __construct()
    {
    }

    public function index($args = null)
    {

    }

    public function view(){
        $viewData = [];

        $id = Input::get('id');
        if($id){
            // get category
            $attribute = Attribute::find($id);
            if(!$attribute){
                return Redirect::guest('/notfound');
            }
            return View::make('admin::attribute.view',array('attribute'=>$attribute));
        }
        return Redirect::guest('/notfound');
    }

    public function manage()
    {
        $viewData = [];
        $webConfigs = WebConfig::all();
        $viewData['webConfigs'] = $webConfigs;
        return View::make('admin::web_config.list',$viewData);
    }

    public function add(){
        if (Request::isMethod('post'))
        {
            $name = Input::get('name');
            $identity = Input::get('identity');
            $value = Input::get('value');

            if(!$name || !$value){
                return Response::json(array('errors' => array('Dữ liệu không chính xác.')));
            }
            if(!$identity){
                $identity = StringUtils::rewriteUrl($name);
            }

            // check if identity exist
            $check = WebConfig::where('identity','=',$identity)->first();
            if($check){
                return Response::json(array('errors' => array('Cấu hình này đã tồn tại.')));
            }

            // save data
            $webConfig = new WebConfig;
            $webConfig->fill(array('name'=>$name,'identity'=>$identity,'value'=>$value));

            $webConfig->save();
            // get all config

            $webConfigs = WebConfig::all();

            return Response::json(array('success' =>true,'webConfig'=>$webConfig,'webConfigs'=>$webConfigs));
        }else{
            return Response::json(array('errs' => array('Method doesn\'t accept.')));
        }
    }

    public function edit(){
        if (Request::isMethod('post'))
        {
            $id = Input::get('id');
            $name = Input::get('name');
            $identity = Input::get('identity');
            $value = Input::get('value');

            $webConfig = WebConfig::find($id);

            if(!$webConfig || !$name || !$value){
                return Response::json(array('errors' => array('Dữ liệu không chính xác.')));
            }
            if(!$identity){
                $identity = StringUtils::rewriteUrl($name);
            }

            // check if identity exist
            $check = WebConfig::where('identity','=',$identity)->first();
            if($webConfig->identity != $identity && $check){
                return Response::json(array('errors' => array('Cấu hình này đã tồn tại.')));
            }


            $webConfig->fill(array('name'=>$name,'identity'=>$identity,'value'=>$value));

            $webConfig->save();
            // get all config
            
            $webConfigs = WebConfig::all();

            return Response::json(array('success' =>true,'webConfig'=>$webConfig,'webConfigs'=>$webConfigs));
        }else{
            return Response::json(array('errs' => array('Method doesn\'t accept.')));
        }
    }

    public function remove(){
        $id = Input::get('id');
        if($id){
            $webConfig = WebConfig::find($id);
            if($webConfig){
                $webConfig->delete();
            }
        }

    }


}
