<?php
class Order extends BaseModel{
	protected $table = 'order';
	protected $primaryKey = 'order_id';

	protected $guarded = array('order_id','created_at','updated_at');

	public function addOrder($userId, $customerName, $customerPhone, $customerEmail, $customerAddress, $customerDistrict, $cutomerProvice, $totalPay, $status, $note){
		$order = new Order;
        $order->user_id = $userId;
        $order->customer_name = $customerName;
        $order->customer_phone = $customerPhone;
        $order->customer_address = $customerAddress;
        $order->customer_district = $customerDistrict;
        $order->customer_province = $cutomerProvice;
        $order->total_pay = $totalPay;
        $order->status = $status;
        $order->note = $note;
        $order->save();
        return $order->order_id;
        
    }

    public function editOrder($orderId, $userId, $customerName, $customerPhone, $customerEmail, $customerAddress, $customerDistrict, $cutomerProvice, $totalPay, $status, $note){
		$order = Order::find($orderId);
        $order->user_id = $userId;
        $order->customer_name = $customerName;
        $order->customer_phone = $customerPhone;
        $order->customer_address = $customerAddress;
        $order->customer_district = $customerDistrict;
        $order->customer_province = $cutomerProvice;
        $order->total_pay = $totalPay;
        $order->status = $status;
        $order->note = $note;
        $order->save();
        return $order->order_id;    
    }
    
    
}