<?php

class TestController extends BaseController
{

    public function getIndex()
    {

        /*if (Auth::attempt(array('email' => 'lequivn@gmail.com', 'password' => '123456'), true)) {
            return Redirect::intended('/test/logined');
        } else {
            $user = User::where('email', 'lequivn@gmail.com')->first();
            if ($user && $user->password == md5('123456')) {
                $user->password = Hash::make('123456');
                $user->save();
            }
        }*/

        /*$products = Product::all();
        $defaultProductStatus = ProductStatus::getDefaultStatus();
        $defaultWarrantyProductStatus = ProductWarrantyStatus::getDefaultStatus();
        foreach ($products as $product) {
            // generate sku
            $product->status_id = $defaultProductStatus->product_status_id;
            $product->warranty_status_id = $defaultWarrantyProductStatus->product_warranty_status_id;
            $product->save();
        }*/

        $shopId = Auth::getShopId();
        $shop = Shop::find($shopId);

        $products = Product::all();
        foreach ($products as $product) {
            $product->generateSKU();

            $variants = $product->variants;
            foreach ($variants as $variant) {
                $variant->generateSKU();
            }
        }

        /*$categories = Category::all();
        foreach ($categories as $category) {
            $identity = $orgIdentity = StringUtils::rewriteUrl($category->category_name);
            $i = 1;
            while(true){
                $check = Category::where('identity','=',$identity)->get();
                if($check && count($check) != 0){
                    $identity = $orgIdentity.$i;
                    $i++;
                }else{
                    break;
                }
            }
            $category->identity = $identity;
            $category->save();
        }
        Category::getInstance()->regenRedis();*/

        // genere shop identity
        /*$shops = Shop::all();
        foreach ($shops as $shop) {
            $identity = $pureIdentity = StringUtils::rewriteUrl($shop->name);
            $count = 1;
            while(true){
                if(Shop::where('identity','=',$identity)->first()){
                    $identity = $pureIdentity.$count;
                    $count++;
                }else{
                    break;
                }
            }
            $shop->identity = $identity;
            $shop->save();
        }*/
    }

    public function getLogined()
    {
        if (Auth::check()) {
            echo \App\Utils\Auth::isAdmin();
        }
    }

}
