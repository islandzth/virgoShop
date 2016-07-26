<?php
class ProductImage extends BaseModel{
	protected $table = 'product_image';
	protected $primaryKey = 'id';

	protected $guarded = array('id','created_at','updated_at');
}