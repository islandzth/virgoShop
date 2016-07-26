<?php
/**
*1 chua xl
*2 da xac nhan
*3 chuyen hang
*4 hoan thanh
*5 huy
*/
class AdOrderController extends AdBaseController
{

    public function __construct()
    {
    }

    public function manage(){
    	$dataView['arrStatus'] = array(
    		'1' => array('text' => 'Chưa xử lý', 'label' => 'label-info'),
    		'2' => array('text' => 'Chờ soạn hàng', 'label' => 'label-info'),
    		'3' => array('text' => 'Chuyển hàng', 'label' => 'label-warning'),
    		'4' => array('text' => 'Thành công', 'label' => 'label-success'),
    		'5' => array('text' => 'Hủy', 'label' => 'label-default')
    		);
		if(Input::get('stt') !== null){
			if(Input::get('stt') >=1 && Input::get('stt') <=5){
				$dataView['arrOrder'] = Order::where('status', Input::get('stt'))->orderBy('created_at', 'DESC')->paginate(20);
			}else{
				$dataView['arrOrder'] = Order::orderBy('created_at', 'DESC')->paginate(20);
			}
		}else{
			$dataView['arrOrder'] = Order::orderBy('created_at', 'DESC')->paginate(20);
		}
		return View::make('admin::order.manage', $dataView);
    }

    public function edit($oid){
    	$orderObj = Order::find($oid);
    	$dataView = [];

    	if(count($orderObj) > 0){
    		if(Request::isMethod('post')){
    			$role =  array(
		            'customerName' => 'required',
		            'customerPhone' => 'required|min:9',
		            'customerEmail' => 'email',
		            'customerAddress' => 'required|min:5',
		            'customerProvince' => 'required|min:2',
		            'customerDistrict' => 'required|min:2'
		        );
		        $validator = Validator::make(Input::all(), $role);
		        if($validator->fails()){
		            $dataView['err'] = 1;
		            $dataView['msg'] = 'Kiểm tra lại dự liệu';
		        }else{
		        	$flag = 0;
		            DB::beginTransaction();
		            try{
		                //add user
		                $userObj = User::where('phone', Input::get('customerPhone'))->first();
		                if($userObj->count() > 0){
		                    $userId = $userObj->user_id;
		                }else{
		                    $userObj = new User();
		                    $userObj->phone = Input::get('customerPhone');
		                    $userObj->save();
		                    $userId = $userObj->user_id;
		                }
		                //create order
		                $orderObj->user_id = $userId;
		                $orderObj->customer_name = Input::get('customerName');
		                $orderObj->customer_phone = Input::get('customerPhone');
		                $orderObj->customer_email = Input::get('customerEmail');
		                $orderObj->customer_address = Input::get('customerAddress');
		                $orderObj->customer_district = Input::get('customerDistrict');
		                $orderObj->customer_province = Input::get('customerProvince');
		                $orderObj->note = Input::get('customerNote');
		                $orderObj->save();

		            }catch(\Exception $e){
		                $dataView['err'] = 1;
		                $dataView['msg'] = 'Thay đổi dự liệu không thành công';
		                $flag = 1;
		                DB::rollback();
		            }
		            DB::commit();
	        	}
	        	if($flag == 0){
	        		$dataView['err'] = 0;
    				$dataView['msg'] = 'Thay đổi thành công';
	        	}
	        	
    		}//end post
    		$orderObj = Order::find($oid);
    		
    		//load metaOrder
    		$metaOrder = OrderMeta::where('order_id', $oid)->get();
    		$dataView['totalPay'] = 0;
    		foreach($metaOrder as $val){
    			$metaPro = ProductOption::find($val->product_meta_id);
    			$val->name = Product::where('product_id', $metaPro->product_id)->pluck('name');
    			$val->image = Product::where('product_id', $metaPro->product_id)->pluck('image');
    			$val->size = $metaPro->size;
    			$val->gia = $metaPro->gia;
    			$dataView['totalPay'] += $val->quantity * $val->gia;
    		}
    		$dataView['metaOrder'] = $metaOrder;
    		$dataView['orderObj'] = $orderObj;
    		$dataView['province'] = Province::all();
            $dataView['district'] = District::where('provinceid', $orderObj->customer_province)->get();
            $dataView['url_img'] = Config::get('app.static_image_product_url');
            
    	}else{
    		$dataView['err'] = 1;
    		$dataView['msg'] = 'Không tìm thấy đơn hàng';
    	}
    	return View::make('admin::order.edit', $dataView);
    }
}
?>