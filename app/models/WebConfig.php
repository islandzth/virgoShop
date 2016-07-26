<?php
class WebConfig extends BaseModel{
	protected $table = 'web_config';
	protected $primaryKey = 'web_config_id';

	protected $guarded = array('web_config_id','created_at','updated_at');
	

	public static function getConfigByKey($key){
		return self::where('identity','=',$key)->first();
	}
    
    public function validate(){
        $rules = array(
                'identity'=>'required',
                'name'=>'required',
                'value'=>'required'
            );
        $validator = Validator::make($this->toArray(), $rules);
        return $validator;
    }
}