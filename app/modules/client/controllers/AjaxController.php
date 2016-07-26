<?php

class AjaxController extends BaseController
{

    public function __construct()
    {
        parent::__construct("ajax");
        header('Access-Control-Allow-Origin: http://vip.thitruongsi.com');
    }

    public function index($args = null)
    {

    }

    public function addVidToOrder()
    {
        if(is_null(Input::get('vid'))){
            echo '0';
        }else{
            $count = ProductOption::where('id', '=', Input::get('vid'))->count();
            if($count >= 1){
                $arrVid = Session::get('arrVid');
                if(isset($arrVid)){
                    $arrVid = json_decode($arrVid);
                }else{
                    $arrVid = [];
                }
                array_push($arrVid, Input::get('vid'));
                Session::put('arrVid', json_encode($arrVid));
                echo 1;
            }else{
                echo '0';
            }
        }
        
    }
    public function rmProductToCart(){
        if(is_null(Input::get('vid'))){
            echo '0';
        }else{
            $arrVid = Session::get('arrVid');
            if(isset($arrVid)){
                $arrVid = json_decode($arrVid);
                $arrkq = [];
                $flag = 0;
                for($i = 0; $i < count($arrVid); $i++){
                    if($arrVid[$i] == Input::get('vid') && $flag == 0){
                        $flag = 1;
                    }else{
                        array_push($arrkq, $arrVid[$i]);
                    }
                }
                Session::put('arrVid', json_encode($arrkq));
            
                echo '1';
            }else{
                echo '0';
            }
        }
    }

    public function getDistict(){
        $countProvince = Province::where('provinceid', Input::get('provinceid'))->count();
        $arrkq = [];
        if($countProvince > 0){
            $district = District::where('provinceid', Input::get('provinceid'))->get();
            if(count($district) > 0){
                $arrkq['district'] = $district->toArray();
                $arrkq['err'] = 0;
            }else{
                $arrkq['err'] = 1;
            }
        }else{
            $arrkq['err'] = 1;
        }
        echo json_encode($arrkq);
    }
    
}
