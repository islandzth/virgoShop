<?php
class ProductAttribute extends BaseModel{
	protected $table = 'product_attr';
	protected $primaryKey = 'product_attr_id';

	protected $guarded = array('product_attr_id','created_at','updated_at');
}