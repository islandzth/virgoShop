<?php
class Prediction
{

    private static $_pio_appkey = 'JN08jwqVhH4S9cscmJQazDRsJeCTiCTOmemZhSNG6usmY4PbxycKhBNJ5CJOZFGw';
    private static $_pio_appurl = 'http://42.117.7.177:8001';
    private static $_isConnect = true;

    private static $_instance;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    public function __construct()
    {
    }


    public function addProductItem($item, $category, $price, $shopId, $shopCity)
    {
        if (self::$_isConnect) {
            $_curl = new \Curl();
            $postdata = array(
                'pio_appkey' => self::$_pio_appkey,
                'pio_iid' => $item,
                'pio_itypes' => 'product',
                'pio_price' => $price,
                'category' => $category,
                'shopid' => $shopId,
                'shopcity' => $shopCity
            );
            $resp = json_decode($_curl->send_post_data(self::$_pio_appurl . '/items.json', $postdata));

            return $resp;
        } else {
            return false;
        }
    }

    public function addShopItem($shopId)
    {
        $_curl = new \Curl();
        $postdata = array(
            'pio_appkey' => self::$_pio_appkey,
            'pio_iid' => 'shop_' . $shopId,
            'pio_itypes' => 'shop'
        );
        $resp = json_decode($_curl->send_post_data(self::$_pio_appurl . '/items.json', $postdata));
        return $resp;
    }

    public function addUser($user)
    {
        if (self::$_isConnect) {
            $_curl = new \Curl();
            $postdata = array(
                'pio_appkey' => self::$_pio_appkey,
                'pio_uid' => $user,
            );
            $resp = json_decode($_curl->send_post_data(self::$_pio_appurl . '/users.json', $postdata));

            return $resp;
        } else {
            return false;
        }
    }

    public function actionShop($user, $shopId, $action = 'like')
    {
        $_curl = new \Curl();
        $postdata = array(
            'pio_appkey' => self::$_pio_appkey,
            'pio_uid' => $user,
            'pio_iid' => 'shop_' . $shopId,
            'pio_action' => $action,
        );

        $resp = json_decode($_curl->send_post_data(self::$_pio_appurl . '/actions/u2i.json', $postdata));
        return $resp;
    }

    public function action($user, $item, $action = 'view', $rate = null)
    {
        if (self::$_isConnect) {
            $_curl = new \Curl();
            if ($action == 'rate') {
                $postdata = array(
                    'pio_appkey' => self::$_pio_appkey,
                    'pio_uid' => $user,
                    'pio_iid' => $item,
                    'pio_action' => $action,
                    'pio_rate' => $rate
                );
            } else {
                $postdata = array(
                    'pio_appkey' => self::$_pio_appkey,
                    'pio_uid' => $user,
                    'pio_iid' => $item,
                    'pio_action' => $action,
                );
            }
            $resp = json_decode($_curl->send_post_data(self::$_pio_appurl . '/actions/u2i.json', $postdata));

            return $resp;
        } else {
            return false;
        }
    }

    public function getSimResult($itemId, $engine, $limit)
    {
        $curl = new \Curl();
        $url = self::$_pio_appurl . '/engines/itemsim/' . $engine . '/topn.json?pio_engine=sim&pio_n=' . $limit . '&pio_iid=' . $itemId . '&pio_attributes=category&pio_appkey=' . self::$_pio_appkey;

        $resp = $curl->fetch_url($url);
        $data = json_decode($resp);
        return $data;
    }

    public function getProductRec($userId, $engine, $limit)
    {
        $curl = new \Curl();
        $url = self::$_pio_appurl . '/engines/itemrec/' . $engine . '/topn.json?pio_engine=sim&pio_n=' . $limit . '&pio_uid=' . $userId . '&pio_appkey=' . self::$_pio_appkey;

        $resp = $curl->fetch_url($url);
        $data = json_decode($resp);
        return $data;
    }

    public function getShopRec($userId, $engine, $limit) {
        $curl = new \Curl();
        $url = self::$_pio_appurl . '/engines/itemrec/' . $engine . '/topn.json?pio_n=' . $limit . '&pio_uid=' . $userId . '&pio_appkey=' . self::$_pio_appkey;

        $resp = $curl->fetch_url($url);
        $data = json_decode($resp);
        return $data;
    }
}
