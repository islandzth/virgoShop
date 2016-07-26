<?php
/**
 * Created by PhpStorm.
 * User: lequi
 * Date: 11/25/14
 * Time: 4:42 PM
 */

class GMJob
{
    private static $_instance;
    public static $_gmclient;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function __construct()
    {
        if (extension_loaded("GearmanClient")) {
            try {
                self::$_gmclient = new GearmanClient();
                self::$_gmclient->addServer(Config::get('app.gearman_server'), Config::get('app.gearman_port'));
            } catch (Exception $ex) {
                Log::error($ex->getMessage());
            }
        }
    }

    public function doBackground($func, $workload)
    {
        if (extension_loaded("GearmanClient")) {
            self::$_gmclient->doBackground($func, $workload);
        }
    }
} 