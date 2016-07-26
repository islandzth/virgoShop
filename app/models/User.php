<?php

/**
 * Description of user
 *
 * @author NhatTieu
 */
class User extends BaseModel 
{

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    
}
