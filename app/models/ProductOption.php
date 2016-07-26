<?php
class ProductOption extends BaseModel{
	protected $table = 'product_meta';
	protected $primaryKey = 'id';

	protected $guarded = array('id','created_at','updated_at');
}