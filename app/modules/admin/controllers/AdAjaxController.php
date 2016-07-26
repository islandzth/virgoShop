<?php

class AdAjaxController extends AdBaseController
{

    public function __construct()
    {
    }

    public function handleUploadImage(){
		$j = 0;     // Variable for indexing uploaded image.
		$target_path =  Config::get('app.UPLOAD_FOLDER').'images/product/'; 
		$arrKq['err'] = 0;
		$arrKq['msg'] = '';
		$arrKq['arrName'] = [];
		$validextensions = array("jpeg", "jpg", "png"); 
		$name = md5(time());
		for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
			
			$ext = explode('.', basename($_FILES['files']['name'][$i]));
			$file_extension = end($ext);
			$arrKq['arrName'][$i] = $name.$i.'.jpg';
			if (($_FILES["files"]["size"][$i] < 1000000)
				&& in_array($file_extension, $validextensions)) {
				if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $target_path.$arrKq['arrName'][$i])) {
					$j = $j + 1; 
				} else {    
					$arrKq['err'] = 1;
					$arrKq['msg'] = 'Lỗi upload ảnh, F5 và upload lại';
					break;
				}
			} else {     //   If File Size And File Type Was Incorrect.
				$arrKq['err'] = 1;
				$arrKq['msg'] = 'Kích thước ảnh không phù hợp';
				break;
			}
		}
		echo json_encode($arrKq);
    }

    public function changeSttOrder(){
    	$arrKq = [];
    	if(Input::get('oid') === null){
    		$arrKq['err'] = 1;
    	}else if(Input::get('stt') === null || (Input::get('stt') < 1 || Input::get('stt') > 5)){
    		$arrKq['err'] = 1;
    	}else{
    		$orderObj = Order::find(Input::get('oid'));
    		if(count($orderObj) <= 0){
    			$arrKq['err'] = 1;
    		}else{
    			if(Input::get('stt') == 4){
    				//success
    				$countMeta = OrderMeta::where('order_id', Input::get('stt'))->count();
    				$userObj = User::find($orderObj->user_id);
    				if(count($userObj) > 0){
    					$userObj->sl_success = $userObj->sl_success + $countMeta;
    					$userObj->save();
    				}
    			}
    			$orderObj->status = Input::get('stt');
    			$orderObj->save();
    			$arrKq['err'] = 0;
    			$arrKq['oid'] = Input::get('oid');
    			$arrKq['stt'] = Input::get('stt');
    		}
    	}
    	echo json_encode($arrKq);
    }

    public function removeMetaOrder(){
    	$role =  array(
            'oid' => 'required|integer',
            'metaid' => 'required|integer'
        );
        $validator = Validator::make(Input::all(), $role);
        if($validator->fails()){
            $dataView['err'] = 1;
            $dataView['msg'] = 'Không thê xóa sản phẩm';
        }else{
        	$metaOrder = OrderMeta::find(Input::get('metaid'));
        	if(count($metaOrder) > 0){
        		$metaOrder->delete();
        		$arrMetaOrder = OrderMeta::where('order_id', Input::get('oid'));
        		if($arrMetaOrder->count() > 0){
        			$totalPay = 0;
        			foreach ($arrMetaOrder->get() as $key => $value) {
        				$totalPay += $value->quantity * $value->gia;
        			}
        			$dataView['totalPay'] = $totalPay;
        			$dataView['err'] = 0;
            		$dataView['msg'] = 'Xóa thành công';
        		}else{
        			$dataView['err'] = 2;
        			$dataView['totalPay'] = 0;
        		}
        	}
        }
        echo json_encode($dataView);
    }
}
?>