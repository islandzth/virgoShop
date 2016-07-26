<?php

class OrderController extends BaseController
{
    public function viewCart()
    {
        $viewData = [];
        $arrVid = Session::get('arrVid');
        if(isset($arrVid)){
            $arrVid = json_decode($arrVid);
            if(count($arrVid) <=0){
                $viewData['err'] = 2;//chua co sp
                $viewData['msg'] = 'Chưa có sản phẩm nào!';
            }else{
                $viewData['arrkq'] = [];
                $viewData['err'] = 1;
                $viewData['pathImg'] = Config::get('app.static_image_product_url');
                for ($i=0; $i < count($arrVid); $i++) { 
                    $proMetaObj = ProductOption::find($arrVid[$i]);
                    if(count($proMetaObj) > 0){
                        $proObj = Product::find($proMetaObj->product_id);
                        if(count($proObj) > 0){
                            $arrKq['pid'] = $proMetaObj->product_id;
                            $arrKq['vid'] = $proMetaObj->id;
                            $arrKq['productName'] = $proObj->name;
                            $arrKq['productImage'] = $proObj->image;
                            $arrKq['productOption'] = $proMetaObj->size;
                            $arrKq['productPrice'] = $proMetaObj->gia;
                            array_push($viewData['arrkq'], $arrKq);
                        }
                    }
                }
            }
        }else{
            $viewData['err'] = 2;//chua co sp
            $viewData['msg'] = 'Chưa có sản phẩm nào!';
        }
        return View::make('client::order.viewCart', $viewData);
    }

    public function checkout(){
        $viewData = [];
        $arrVid = Session::get('arrVid');
        $arrVid = json_decode($arrVid);
        if(count($arrVid) <= 0){
            return Redirect::to('viewCart');
        }else{
            $viewData['arrkq'] = [];
            $viewData['err'] = 1;
            $arrKq = [];
            $viewData['totalPrice'] = 0;
            $viewData['province'] = Province::all();
            $districtFrist = $viewData['province'][0];
            $viewData['district'] = District::where('provinceid', $districtFrist->provinceid)->get();
            for ($i=0; $i < count($arrVid); $i++) { 
                $proMetaObj = ProductOption::find($arrVid[$i]);
                if(count($proMetaObj) > 0){
                    $proObj = Product::find($proMetaObj->product_id);
                    if(count($proObj) > 0){
                        array_push($arrKq, $arrVid[$i]);
                        $viewData['totalPrice'] += $proMetaObj->gia;
                    }
                }
            }
            Session::put('arrVid', json_encode($arrKq));
        }

        return View::make('client::order.checkout', $viewData);
    }
    public function success(){
        if(Request::isMethod('get')){
            return Redirect::route('index');
        }
        $viewData['msg'] = 'Đặt hàng thành cộng. Shop sẻ liên lạc với bạn sớm nhất';
        $viewData['url_return'] = URL::route('index');
        $viewData['err'] = 0;
        $role =  array(
            'customerName' => 'required',
            'customerPhone' => 'required|min:9',
            'customerEmail' => 'email|unique:users',
            'customerAddress' => 'required|min:5',
            'customerProvince' => 'required|min:2',
            'customerDistrict' => 'required|min:2'
        );
        $validator = Validator::make(Input::all(), $role);
        if($validator->fails()){
            return Redirect::route('checkout');
        }else{
            DB::beginTransaction();
            try{
                //add user
                $userObj = User::where('phone', Input::get('customerPhone'));
                if($userObj->count() > 0){
                    $userId = $userObj->user_id;
                }else{
                    $userObj = new User();
                    $userObj->phone = Input::get('customerPhone');
                    $userObj->save();
                    $userId = $userObj->user_id;
                }
                //create order
                $orderObj = new Order();
                $orderObj->user_id = $userId;
                $orderObj->customer_name = Input::get('customerName');
                $orderObj->customer_phone = Input::get('customerPhone');
                $orderObj->customer_email = Input::get('customerEmail');
                $orderObj->customer_address = Input::get('customerAddress');
                $orderObj->customer_district = Input::get('customerDistrict');
                $orderObj->customer_province = Input::get('customerProvince');
                $orderObj->note = Input::get('customerAddress');
                $orderObj->save();
                $orderId = $orderObj->order_id;

                //add meta order
                $arrVid = Session::get('arrVid');
                if(isset($arrVid)){
                    $arrVid = json_decode($arrVid);
                    $arrkq = [];
                    $countSP = 0;
                    $totalPay = 0;
                    for($i = 0; $i < count($arrVid); $i++){
                        $metaObj = ProductOption::find($arrVid[$i]);
                        if(count($metaObj) > 0){
                            $metaOrder = new OrderMeta();
                            $metaOrder->order_id = $orderId;
                            $metaOrder->product_meta_id = $arrVid[$i];
                            $metaOrder->gia = $metaObj->gia;
                            $metaOrder->quantity = 1;
                            $metaOrder->total_price = $metaObj->gia * $metaOrder->quantity;
                            $metaOrder->save();
                            $countSP++;
                            $totalPay += $metaObj->gia * $metaOrder->quantity;
                        }else{
                            throw new Exception('meta product fail');
                            break;
                        }
                    }
                    //update sl 
                    $userObj->sl_buy = $userObj->sl_buy + $countSP;
                    $userObj->save();
                    //update order total pay
                    $orderObj->total_pay = $totalPay;
                    $orderObj->save();
                }else{
                    throw new Exception('cart empty');
                }

            }catch(\Exception $e){
                if($e->getMessage() == 'meta product fail'){
                    $viewData['msg'] = 'Đặt hàng thất bại. Kiểm trả lại giỏ hàng';
                    $viewData['url_return'] = URL::route('viewCart');
                }else if($e->getMessage() == 'cart empty'){
                    $viewData['msg'] = 'Đặt hàng thất bại. Kiểm trả lại giỏ hàng của bạn';
                    $viewData['url_return'] = URL::route('viewCart');
                }else{
                    $viewData['msg'] = $e->getMessage();
                    $viewData['url_return'] = URL::route('viewCart');
                }
                $viewData['err'] = 1;
                DB::rollback();
            }
            DB::commit();
            if($viewData['err'] == 0){
                Session::forget('arrVid');
            }
            return View::make('client::order.success', $viewData);
        }
    }
}