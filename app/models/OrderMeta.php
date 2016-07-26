<?php
class OrderMeta extends BaseModel{
	protected $table = 'order_meta';
	protected $primaryKey = 'id';

	protected $guarded = array('id','created_at','updated_at');

	public function addOrderMeta($OrderId, $productMeta, $price, $quantity, $totalPrice){
		$OrderMeta = new OrderMeta;
        $OrderMeta->order_id = $OrderId;
        $OrderMeta->product_meta_id = $productMeta;
        $OrderMeta->gia = $price;
        $OrderMeta->quantity = $quantity;
        $OrderMeta->total_price = $totalPrice;
        $OrderMeta->save();
        return $OrderMeta->id;
        
    }

    
    
}