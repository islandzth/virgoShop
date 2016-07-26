<?php

class SearchController extends BaseController
{

    public function __construct()
    {
        View::share('repagetype', 'Search');
    }

    public function index($args = null)
    {

    }

    public function product()
    {
        $q = urldecode(Input::get('q'));
        $q = trim(strip_tags($q));
        $product_obj = Product::getInstance();
        $searchObj = Search::getInstance();
        $province_obj = Province::getInstance();
        $category_obj = Category::getInstance();
        $userObj = User::getInstance();

        $shop_obj = Shop::getInstance();
        $shopcity = null;

        if (Input::get('do') == 'clearfilter') {
            Cookie::forget('filter_sc');
        }

        $rowsPerPage = 28;

        if (Common::isMobile()) {
            $rowsPerPage = 30;
        }

        //get page
        $page = StringUtils::notEmpty(Input::get('p')) ? intval(Input::get('p')) : 1;

        $offset = ($page - 1) * $rowsPerPage;
        if ($offset < 0)
            $offset = 0;

        $sort = Input::get('sort');

        $loc = (StringUtils::notEmpty(Input::get('loc')) ? Input::get('loc') : null);
        $viewsData['loc'] = $loc;

        $sc = (StringUtils::notEmpty(Input::get('sc')) ? Input::get('sc') : null);


        if (!Input::get('pricefrom') && !Input::get('priceto')) {
            $re = "/(\\d{1,})k/i";

            preg_match_all($re, $q, $matches);

            $price = $matches[1];

            if (count($price) == 1) {
                $pricefrom = Input::get('pricefrom');
                $pricefrom = $price[0] . '000';
                if (!isset($sort)) {
                    $sort = 'priceasc';
                }
            } else if (count($price) > 1) {
                $pricefrom = Input::get('pricefrom');
                $pricefrom = min($price) . '000';
                $priceto = Input::get('priceto');
                $priceto = max($price) . '000';
            }
        }
        $priceFromF = Input::get('pricefrom');

        if (isset($priceFromF)) {
            $pricefrom = intval(str_replace(',', '', $priceFromF));
        }

        $priceToF = Input::get('priceto');
        if (isset($priceToF)) {
            $priceto = intval(str_replace(',', '', $priceToF));
        }
        $priceToGet = Input::get('priceto');
        if (isset($pricefrom) && $pricefrom < 1 && isset($priceToGet)) {
            $pricefrom = 1;
        }

        if (isset($priceto) && isset($pricefrom) && $priceto < $pricefrom) {
            $priceto = null;
        }

        if (isset($pricefrom) && isset($priceto) && (StringUtils::notEmpty($pricefrom) || StringUtils::notEmpty($priceto))) {
            $viewsData['filter_price'] = true;
        }


        //check cookie
        if (!$sc) {
            $ckSc = Cookie::get('filter_sc');
            $sc = (isset($ckSc) ? Cookie::get('filter_sc') : false);
        }

        $location = $province_obj->getIdByCode($loc);

        if ($sc) {
            $shopcity = $province_obj->getIdByCode($sc);
        }

        if ($sc && $shopcity) {
            $viewsData['sc'] = $sc;
            $scInfo = $province_obj->getDistrictCityById($shopcity);
            $viewsData['sc_name'] = $scInfo['name'];
            Cookie::queue('filter_sc', $sc, 50000);
        }

        if ($location || $shopcity) {
            $viewsData['hasparam'] = 'true';
        }

        $result = $product_obj->searchProduct($q, $offset, $rowsPerPage, $sort, $location, $shopcity, isset($pricefrom) ? $pricefrom : null, isset($priceto) ? $priceto : null);

        $totalProduct = $result->response->numFound;
        if (Input::get('ref') != 'notfound') {
            $searchObj->insertQuery($q, $totalProduct);
        }
        if ($totalProduct > 0) {
            $locQuery = '';
            //count num page
            $maxPage = ceil($totalProduct / $rowsPerPage);


            $viewsData['found'] = $totalProduct;

            if (isset($loc)) {
                if (isset($sort)) {
                    $locQuery = '&loc=' . $loc;
                } else {
                    $locQuery = '?loc=' . $loc;
                }
            }

            if (isset($pricefrom)) {
                if (isset($sort)) {
                    $locQuery .= '&pricefrom=' . $pricefrom;
                } else {
                    $locQuery .= '?pricefrom=' . $pricefrom;
                }
            }

            if (isset($priceto)) {
                if (isset($sort)) {
                    $locQuery .= '&priceto=' . $priceto;
                } else {
                    $locQuery .= '?priceto=' . $priceto;
                }
            }

            if (isset($shopcity)) {
                if (isset($sort)) {
                    $locQuery .= '&sc=' . $sc;
                } else {
                    $locQuery .= '?sc=' . $sc;
                }
            }

            //next page
            if ($page < $maxPage) {
                $nextpage = $page + 1;
                $viewsData['indicate_next'] = Config::get('app.url') . 'search/product?q=' . StringUtils::kqRewrite($q) . '&sort=' . $sort . '&p=' . $nextpage . $locQuery;
            }

            //prev page
            if ($page > 1) {
                $prevpage = $page - 1;

                $viewsData['indicate_prev'] = Config::get('app.url') . 'search/product?q=' . StringUtils::kqRewrite($q) . '&sort=' . $sort . '&p=' . $prevpage . $locQuery;
            }


            $viewsData['pagination'] = Common::pagination($page, $maxPage, Config::get('app.url') . 'search/product?q=' . StringUtils::kqRewrite($q) . '&sort=' . $sort . $locQuery, true);

        }

        if ($totalProduct > 0) {

            $responseResult = $result->response->docs;

            for ($i = 0; $i < $totalProduct; $i++) {
                if (isset($responseResult[$i])) {
                    $item = $product_obj->getProductById($responseResult[$i]->id);
                    if ($item) {

                        $item['shop'] = $shop_obj->getShopById($item['shop_id']);
                        $item['shop']['online'] = $userObj->isOnline($item['shop']['user_id']);
                        if ($item['shop']['city']) {
                            $provinceName = $province_obj->getProvinceById($item['shop']['city']);
                            $item['product_name'] = StringUtils::mb_ucfirst($item['product_name']);
                            $item['province_text'] = $provinceName['name'];
                        }
                        $productList[] = $item;
                    }
                }
            }
            $viewsData['hasresult'] = true;
        }
        $viewsData['query'] = $q;
        if (isset($productList)) {
            $viewsData['searchproductlist'] = $productList;
        }
        //search shop
        if ($page == 1) {
            $shopResultTemp = $shop_obj->getShopList(null, 0, 4, $q, true, 'vip', null, $shopcity);
        }
        if (isset($shopResultTemp)) {
            $followObj = Follow::getInstance();
            foreach ($shopResultTemp as $shop) {
                $shop['city'] = $province_obj->getDistrictCityById($shop['city'])['name'];
                $shop['productlist'] = $product_obj->getProductByShop($shop['id'], 1, 'new', 0, 18);
                if (count($shop['productlist'])) {
                    //using category
                    $categoryUsing = $shop_obj->getCategoryUsing($shop['id']);
                    $tempCatExists = array();
                    $categoryUsingList = array();

                    foreach ($categoryUsing as $c) {
                        if ($category_obj->getLevel($c['category_id']) == '1' && !in_array($c['category_id'], $tempCatExists)) {
                            $categoryUsingList[] = $category_obj->getCategoryById($c['category_id']);
                        }
                        $tempCatExists[] = $c['category_id'];
                    }

                    $shop['follow'] = $followObj->count($shop['id'], 2);

                    $shop['categoryusing'] = $categoryUsingList;
                    $shopResult[] = $shop;
                }
            }

            if (isset($shopResult) && count($shopResult) > 0) {
                $viewsData['shoplist'] = $shopResult;
                $viewsData['hasresult'] = true;
                $viewsData['shopfound'] = count($shopResult);
            }
        }

        //meta data
        $viewsData['page_url'] = Config::get('app.url') . 'search/product?q=' . urlencode($q);
        $viewsData['meta_canonical'] = $viewsData['page_url'];
        $viewsData['meta_title'] = $q . ' - tìm kiếm giá sỉ giá buôn trên Thị Trường Sỉ';
        $viewsData['meta_description'] = 'Các sản phẩm ' . $q . ' giá sỉ, giá buôn tại thitruongsi.com';
        if (Input::get('ref') == 'notfound') {
            $viewsData['meta_robots'] = 'noindex, follow';
        } else {
            $viewsData['meta_robots'] = 'index, follow';
        }
        if (Common::isMobile()) {
            return View::make('client::oldmobile.search.product', $viewsData);
        } else {
            return View::make('client::old.search.product', $viewsData);
        }
    }

}
