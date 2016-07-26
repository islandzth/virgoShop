<?php
class CategoryAttribute extends BaseModel{
	protected $table = 'category_attr';
	protected $primaryKey = 'category_attr_id';

	protected $guarded = array('category_attr_id','created_at','updated_at');
}