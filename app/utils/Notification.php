<?php

/**
 * Description of notification
 * object_type
 * 1: category new product
 * 2: shop new product
 * 3: user follow shop
 * 4: user add product to collection
 *
 *
 * @author NhatTieu
 */
class NotificationUtils
{
    private static $_instance;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function push($recipient_id, $sender_id, $object_id, $object_type, $href = null)
    {
        $notificationObj = Notification::getInstance();
        $body = $this->genBody($object_id, $object_type);
        if ($body) {
            $notificationData = $notificationObj->addNotification($recipient_id, $sender_id, $object_id, $object_type, $body, $href);
            //pub redis
            if ($notificationData) {
                $channel = $this->genChannel($recipient_id);
                $notificationObj->publish($channel, $notificationData);
            }
        }
    }

    public function genChannel($recipient_id, $channelType = 'user')
    {
        return 'channel:' . md5($channelType . '_tts2421_' . $recipient_id);
    }

    public function genBody($object_id, $object_type)
    {
        $body = '';
        switch ($object_type) {
            case 1:
                //is category new product
                $categoryObj = Category::getInstance();
                $categoryInfo = $categoryObj->getCategoryById($object_id);
                if ($categoryInfo) {
                    $body = 'Danh mục <span class="object">' . $categoryInfo['category_name'] . '</span> vừa có sản phẩm mới';
                }
                break;
            case 2:
                //is shop new product
                $shopObj = Shop::getInstance();
                $shopInfo = $shopObj->getShopById($object_id);
                $productObj = Shop::getInstance();
                $newProduct = $productObj->getProductByShop($object_id, 1, 'new', 0, 1)[0];

                if ($shopInfo) {
                    $body = 'Nhà cung cấp <span class="object">' . $shopInfo['name'] . '</span> vừa có thêm sản phẩm mới <span class="object">' . $newProduct['product_name'] . '</span>';
                }
                break;
            case 3:
                //is user follow
                $userObj = User::getInstance();
                $userInfo = $userObj->getUserById($object_id);
                if ($userInfo) {
                    $body = '<span class="object">' . $userInfo['first_name'] . ' ' . $userInfo['last_name'] . '</span> vừa theo dõi shop của bạn';
                }
                break;
            case 4:
                //user add to collection
                $userObj = User::getInstance();
                $userInfo = $userObj->getUserById($object_id);
                if ($userInfo) {
                    $body = '<span class="object">' . $userInfo['first_name'] . ' ' . $userInfo['last_name'] . '</span> vừa thêm sản phẩm của bạn vào bộ sưu tập';
                }
                break;
        }
        return $body;
    }
}