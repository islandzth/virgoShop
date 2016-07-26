<?php

/**
 * Created by PhpStorm.
 * User: lequi
 * Date: 11/19/14
 * Time: 2:53 PM
 */
class BaseModel extends Eloquent
{

    protected $db;
    protected $cache;
    protected $redis;
     
    public function __construct()
    {
        parent::__construct();
        $this->db = DB::connection()->getPdo();

        // $this->cache = app()['cache'];
        // $this->redis = app()['redis'];
    }

    public static function getInstance()
    {
        static $_instance = null;
        if (null === $_instance) {
            $_instance = new static();
        }
        return $_instance;
    }
}