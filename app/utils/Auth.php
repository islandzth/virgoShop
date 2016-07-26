<?php
/**
 * Created by PhpStorm.
 * User: lequi
 * Date: 11/24/14
 * Time: 1:28 PM
 */

namespace App\Utils;

use Illuminate\Support\Facades\Session;
use Shop, User, ShopWarranty, ShopStatus;

class Auth extends \Illuminate\Support\Facades\Auth
{
    private static $_isShopInstance = null;
    private static $_isShop = false;

    private static $_accountTypeInstance = null;
    private static $_accountType = false;

    public static function isAdmin()
    {
        return Auth::check() && Auth::user()->hasRole('admin');
    }

    public static function isMod()
    {
        return Auth::check() && Auth::user()->hasRole('mod');
    }

    public static function isShopNew()
    {
        return Auth::check() && Auth::user()->hasRole('shop');
    }

    public static function isShop()
    {
        if (null === self::$_isShopInstance) {
            if (self::check()) {
                $user_obj = User::getInstance();
                self::$_isShop = $user_obj->isShop(self::getUserId());
            }
            self::$_isShopInstance = 1;
        }
        return self::$_isShop;
    }

    public static function isShopOwner($shopId)
    {
        if (self::getShopId() == $shopId) {
            return true;
        } else {
            return false;
        }
    }

    public static function isRetailer()
    {
        return !Auth::isShop();
    }

    public static function getRetailerId()
    {
        return Session::get('retailerid');
    }

    public static function getShopId($full = null)
    {
        if (self::check()) {
            $shopObj = Shop::getInstance();
            $shop = $shopObj->getShopByUserId(self::getUserId());
            if ($full) {
                return $shop;
            } else {
                return $shop['id'];
            }
        } else {
            return false;
        }
    }

    public static function getUserId()
    {
        if (self::check()) {
            return Auth::id();
        } else {
            return false;
        }
    }

    public static function getUserCode()
    {
        if (self::check()) {
            return Auth::user()->user_code;
        } else {
            return false;
        }
    }

    public static function isShopVip()
    {
        if (self::check()) {
            $shopObj = Shop::getInstance();
            $shop = $shopObj->getShopByUserId(self::getUserId());
            if (isset($shop['vip']) && $shop['vip'] == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function isShopWarranty(){
        if (self::check()) {
            $shopObj = Shop::getInstance();

            $shop = Shop::where('user_id','=',self::getUserId())->first();

            $shopStatus = ShopStatus::find($shop->status_id);
            if(!$shopStatus){
                return false;
            }
            return $shopStatus->is_warranty;
        } else {
            return false;
        }
    }

    public static function accountType()
    {
        if (null === self::$_accountTypeInstance) {
            if (self::check()) {
                $userObj = User::getInstance();
                self::$_accountType = $userObj->getAccountType(self::getUserId());
            }
            self::$_accountTypeInstance = 1;
        }
        return self::$_accountType;

//        if (self::check()) {
//            if (Auth::isShop()) {
//                return 1;
//            } else {
//                return 0;
//            }
////            $userObj = User::getInstance();
////            return $userObj->getAccountType(self::getUserId());
//        }
    }

    public static function shopIsLock()
    {
        $user_obj = User::getInstance();
        $shopStatus = $user_obj->isShop(self::getUserId());

        if (intval($shopStatus) == -1) {
            return true;
        } else {
            return false;
        }
    }

    public static function getEmail()
    {
        if (Auth::check()) {
            return Auth::user()->email;
        }
    }

    public static function getGroupId()
    {
        if (Auth::check()) {
            return Auth::user()->groupid;
        }
    }

    public static function getLastName()
    {
        if (Auth::check()) {
            return Auth::user()->last_name;
        }
    }
}