<?php

class UserController extends BaseController
{


    public function __construct()
    {
        parent::__construct();
        View::share('repagetype', 'User');

    }

    public function index($args = null)
    {

    }

    public function contacted()
    {
        $followObj = Follow::getInstance();
        $followingListId = $followObj->getFollowing(Auth::getUserId(), 4);
        if (isset($followingListId) && !empty($followingListId)) {
            $shopObj = Shop::getInstance();
            for ($i = 0; $i < count($followingListId); $i++) {
                $followDetail = $shopObj->getShopById($followingListId[$i]);
                $following[] = $followDetail;
            }
            if (isset($following)) {
                $viewsData['following'] = $following;
            }
            $viewsData['meta_title'] = "Nhà bán sỉ đã liên hệ";
            return View::make('client::old.user.follow.contacted', $viewsData);
        } else {
            $viewsData['msg'] = 'Bạn chưa liên hệ với ai trên Thị Trường Sỉ';
            return View::make('client::old.alert', $viewsData);
        }
    }

    public function following()
    {
        $followObj = Follow::getInstance();
        $followingListId = $followObj->getFollowing(Auth::getUserId(), 2);
        $viewsData = [];
        if (isset($followingListId) && !empty($followingListId)) {
            $shopObj = Shop::getInstance();
            for ($i = 0; $i < count($followingListId); $i++) {
                $followDetail = $shopObj->getShopById($followingListId[$i]);
                $following[] = $followDetail;
            }
            if (isset($following)) {
                $viewsData['following'] = $following;
            }
            return View::make('client::old.user.follow.following', $viewsData);
        } else {
            $viewsData['msg'] = 'Bạn chưa theo dõi ai cả. Hãy theo dõi nhà bán sỉ/lẻ để nhận được các thông tin mới nhất từ nhà bán sỉ/nhà bán lẻ bạn quan tâm';
            return View::make('client::old.alert', $viewsData);
        }
    }

    public function verify($verifyCode)
    {
        $userObj = User::getInstance();
        if (StringUtils::notEmpty($verifyCode)) {
            $exec = $userObj->verifyUser($verifyCode);
            if ($exec) {
                return View::make('client::old.user.verifystatus');
            } else {
                return Redirect::to('/');
            }
        } else {
            return Redirect::to('/');
        }
    }

    public function docreate()
    {
        $userObj = User::getInstance();
        $msg = '';
        $name = '';
        $email = strip_tags(Input::get('email'));
        $password = Input::get('password');
        $repassword = Input::get('repassword');
        $account_type = Input::get('account_type');
        $firstname = strip_tags(Input::get('firstname'));
        $lastname = strip_tags(Input::get('lastname'));


        $shop_name = strip_tags(Input::get('shop_name'));
        $shop_office = strip_tags(Input::get('shop_address'));

        $phone = strip_tags(Input::get('shop_phone'));
        $shop_email = strip_tags(Input::get('shop_email'));
        $city = strip_tags(Input::get('shop_city'));
        $district = strip_tags(Input::get('shop_district'));

        $viewsData = array();
        $viewsData['msg'] = '';

        $flag = true;

        if (StringUtils::notEmpty($email) && StringUtils::notEmpty($password) && StringUtils::notEmpty($repassword)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $viewsData['msg'] .= "+ Email không hợp lệ<br />";
                $flag = false;
            }

            if ($password != $repassword) {
                $viewsData['msg'] .= "+ Mật khẩu nhập lại không khớp<br />";
                $flag = false;
            }

            if ($userObj->isEmailExist($email)) {
                $viewsData['msg'] .= "+ Email đã có người sử dụng<br />";
                $flag = false;
            }
        } else {
            $viewsData['msg'] .= "+ Vui lòng nhập đầy đủ thông tin<br />";
            $flag = false;
        }

        if (!StringUtils::notEmpty($firstname)) {
            $flag = false;
            $viewsData['msg'] .= 'Bạn chưa nhập họ<br />';
        }

        if (!StringUtils::notEmpty($lastname)) {
            $flag = false;
            $viewsData['msg'] .= 'Bạn chưa nhập tên<br />';
        }

        //validate retailer
        if ($account_type == '0') {
            if (!StringUtils::notEmpty($shop_name)) {
                $flag = false;
                $viewsData['msg'] .= 'Vui lòng nhập tên nhà bán lẻ<br />';
            }

            if (!StringUtils::notEmpty($shop_office[0])) {
                $flag = false;
                $viewsData['msg'] .= 'Vui lòng nhập văn phòng, shop<br />';
            }
        } else if ($account_type == '1') {
            $shopObj = Shop::getInstance();
            $msg = '';
            if (!StringUtils::notEmpty($shop_name)) {
                $msg .= '<li>Chưa nhập tên nhà bán sỉ</li>';
                $flag = false;
            }

            if ($shopObj->isexists($shop_name)) {
                $msg .= '<li>Đã tồn tại nhà bán sỉ này</li>';
                $flag = false;
            }

            if (!StringUtils::notEmpty($shop_office)) {
                $msg .= '<li>Chưa nhập địa chỉ</li>';
                $flag = false;
            }

            if (!StringUtils::notEmpty($city)) {
                $msg .= '<li>Chưa chọn tỉnh/thành phố</li>';
                $flag = false;
            }

            if (!StringUtils::notEmpty($district)) {
                $msg .= '<li>Chưa chọn quận/huyện</li>';
                $flag = false;
            }


            if (!StringUtils::notEmpty($phone)) {
                $msg .= '<li>Chưa nhập số điện thoại</li>';
                $flag = false;
            }
            if (!StringUtils::notEmpty($shop_email)) {
                $msg .= '<li>Chưa nhập email</li>';
                $flag = false;
            }
        }

        if ($flag === true) {
            $orgPassword = $password;
            $password = md5($password);
            $verifyCode = md5(time() . rand(0, 10));
            $exec = $userObj->createUser($firstname, $lastname, $password, $email, $name, $verifyCode, $account_type, $city);
            if ($exec != false) {
                //login
                Auth::loginUsingId($exec);

                $emailObj = EmailUtils::getInstance();
                $emailObj->emailRegister($email, $verifyCode, $lastname);

                $wiliObj = Wili::getInstance();
                $wiliObj->addGroup($exec, 'Bộ sưu tập của tôi');

                if ($account_type == '1') {
                    //add supplier
                    $createShopExec = $this->createsupplier($exec);
                    $viewsData['createshop'] = $createShopExec;
                } else if ($account_type == '0') {
                    //add retailer
                    $this->createretailer($exec);
                }
                if (isset($_GET['redirecturl'])) {
                    $viewsData['redirecturl'] = $_GET['redirecturl'];
                }

                $jobData['url'] = 'http://message.thitruongsi.com/job/updateDataUser?uId=' . $exec;
                $jobObj = GMJob::getInstance();
                $jobObj->doBackground("httprequest", json_encode($jobData));

                if (Common::isWebApp()) {
                    header('Location: ' . Config::get('app.mobile_app_url') . 'dologin/' . $email . '/' . $orgPassword);
                }
                if (Common::isMobile()) {
                    return View::make('client::oldmobile.user.registersuccess', $viewsData);
                } else {
                    return View::make('client::old.user.registersuccess', $viewsData);
                }
            } else {
                $viewsData['msg'] = 'Có lỗi xảy ra. Vui lòng thử lại.';
                if (Common::isMobile()) {
                    return View::make('client::oldmobile.user.registerstatus', $viewsData);
                } else {
                    return View::make('client::old.user.registerstatus', $viewsData);
                }
            }
        } else {
            $viewsData['msg'] = $msg;
            if (Common::isMobile()) {
                return View::make('client::oldmobile.user.registerstatus', $viewsData);
            } else {
                return View::make('client::old.user.registerstatus', $viewsData);
            }
        }
    }

    private function createretailer($userId)
    {
        $name = strip_tags(Input::get('shop_name'));
        $address = strip_tags(Input::get('shop_address'));
        $officeCity = Input::get('shop_city');
        $officeDistrict = Input::get('shop_district');
        $website = strip_tags(Input::get('shop_website'));
        $email = strip_tags(Input::get('shop_email'));
        $phone = explode(',', Input::get('shop_phone'));
        $viewsData = array();

        $retailerObj = Retailer::getInstance();
        $retailerId = $retailerObj->add($name, $website, '', $userId);
        if ($retailerId != false) {
            $statObj = Statistics::getInstance();
            $statObj->incrUser('0');
            //add retailer phone
            if (StringUtils::notEmpty($phone[0])) {
                for ($i = 0; $i < count($phone); $i++) {
                    if (StringUtils::notEmpty($phone[$i])) {
                        $retailerObj->addPhone($retailerId, $phone[$i]);
                    }
                }
            }

            if (StringUtils::notEmpty($email[0])) {
                //add retailer email
                for ($i = 0; $i < count($email); $i++) {
                    if (StringUtils::notEmpty($email[$i])) {
                        $retailerObj->addEmail($retailerId, $email[$i]);
                    }
                }
            }

            //add retailer office
            $retailerObj->addOffice($retailerId, $address, $officeCity, $officeDistrict);
        } else {
            $viewsData['msg'] .= 'Có lỗi xảy ra, vui lòng thử lại<br />';
        }
    }

    private function createsupplier($userId)
    {
        $userObj = User::getInstance();
        $name = strip_tags(Input::get('shop_name'));
        $address = strip_tags(Input::get('shop_address'));
        $phone = strip_tags(Input::get('shop_phone'));
        $email = strip_tags(Input::get('shop_email'));
        $website = strip_tags(Input::get('shop_website'));
        $contacter = strip_tags(Input::get('shop_contacter'));
        $liabilities = strip_tags(Input::get('liabilities'));
        $liabilitiesmethod = strip_tags(Input::get('liabilitiesmethod'));
        $liabilities_methoddate = strip_tags(Input::get('liabilitiesmethoddate'));
        $buy_method = Input::get('buymethod');
        $introduce = strip_tags(Input::get('shop_introduce'));
        $province = strip_tags(Input::get('shop_province'));
        $city = Input::get('shop_city');
        $district = Input::get('shop_district');
        $contacter_phone = strip_tags(Input::get('contacter_phone'));
        $contacter_email = strip_tags(Input::get('contacter_email'));


        $shopObj = Shop::getInstance();

        $userCode = $userObj->getUserField($userId, 'user_code');

        // get default shop status
        $defaultShopStatus = ShopStatus::getDefaultStatus();

        

        //do add
        $id = $shopObj->addshop($name, $address, $city, $district, $province, $phone, $email, $website, $contacter, $liabilities, $liabilitiesmethod, $liabilities_methoddate, $buy_method, $introduce, $userId, $contacter_phone, $contacter_email, $userCode, $defaultShopStatus->shop_status_id);

        // add shop status history
        $shopStatus = new ShopStatus;
        $shopStatus->fill(array('shop_id'=>$id,'status_id'=>$defaultShopStatus->shop_status_id));
        $shopStatus->save();

        if (!$id) {
            $viewsData['msg'] = 'Có lỗi trong quá trình đăng ký, vui lòng thử lại sau.';
            $viewsData['openaddshop'] = 1;
            $viewsData['meta_title'] = 'Lỗi';
            return View::make('client::old.shop.addshopstatus', $viewsData);
        } else {
            //add role shop
            $user = User::find($userId);
            if ($user) {
                $user->attachRole(2);
            }


            $statObj = Statistics::getInstance();
            $statObj->incrUser('1');
            $emailObj = EmailUtils::getInstance();
            $emailObj->alertEmail('lequivn@gmail.com', 1);
            $emailObj->alertEmail('thaontb91@gmail.com', 1);
            return true;
        }
    }

    public function register()
    {
        if (Auth::check()) {
            return Redirect::to('/');
        } else {
            if (Common::isWebApp()) {
                Auth::logout();
            }

            $viewsData['disable_float_banner'] = true;
            $accountType = Input::get('type');
            if (isset($accountType)) {
                return $this->registerSelectedType();
            } else {
                $viewsData['meta_title'] = 'Đăng ký tài khoản trên Thị Trường Sỉ';
                if (Common::isWebApp()) {
                    return View::make('client::oldmobile.user.register_choose_type_webapp', $viewsData);
                } else {
                    if (Common::isMobile()) {
                        return View::make('client::oldmobile.user.register_choose_type', $viewsData);
                    } else {
                        return View::make('client::old.user.register_choose_type', $viewsData);
                    }
                }
            }
        }
    }

    public function registerSelectedType()
    {

        if (!Auth::check()) {
            $viewsData['meta_title'] = 'Đăng ký tài khoản';
            $viewsData['meta_robots'] = 'noindex, follow';

            //province
            $province_obj = Province::getInstance();
            $viewsData['provincelist'] = $province_obj->getCity();
            $statisticObj = Statistics::getInstance();

            $stat['total_user_1'] = $statisticObj->totalUser('1');
            $stat['total_user_0'] = $statisticObj->totalUser('0');
            $stat['total_product'] = $statisticObj->totalProduct();

            $viewsData['stat'] = $stat;

            if (isset($_GET['type']) && $_GET['type'] === "0") {
                if (Common::isMobile()) {
                    return View::make('client::oldmobile.user.register_retailer', $viewsData);
                } else {
                    return View::make('client::old.user.register_retailer', $viewsData);
                }
            } else {
                if (Common::isMobile()) {
                    return View::make('client::oldmobile.user.register_supplier', $viewsData);
                } else {
                    return View::make('client::old.user.register_supplier', $viewsData);
                }
            }
        } else {
            header("Location: " . Config::get('app.url'));
        }
    }

    public function isemailexists()
    {
        $userObj = User::getInstance();
        $email = Input::get('email');
        if (StringUtils::notEmpty($email)) {
            if ($userObj->isEmailExist($email)) {
                echo 'false';
            } else {
                echo 'true';
            }
        } else {
            echo 'false';
        }
    }

    public function login()
    {

        $viewData = [];

        if (Auth::check() == false) {
            $isPostLogin = Input::get('login');
            if (isset($isPostLogin) && $isPostLogin == 'true') {
                if (StringUtils::notEmpty(Input::get('email')) && StringUtils::notEmpty(Input::get('password'))) {
                    //verify
                    $User = User::getInstance();

                    $user = $User->verify(Input::get('email'), md5(Input::get('password')));
                    $rememberParam = Input::get('remember');
                    $remember = !empty($rememberParam);

                    if (Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')), $remember)) {
                        $User->setLastLogged($user['user_id'], time());
                        $User->setIpAddress($user['user_id'], Common::getClientIP());
                        $User->setUserAgent($user['user_id'], $_SERVER['HTTP_USER_AGENT']);


                        // store session
                        $USER_ID = Auth::id();
                        $USER_CODE = User::getInstance()->getUserField($USER_ID, 'user_code');
                        $EMAIL = User::getInstance()->getUserField($USER_ID, 'email');
                        $NAME = User::getInstance()->getUserField($USER_ID, 'name');

                        Session::put('user_id', $USER_ID);
                        Session::put('user_code', $USER_CODE);
                        Session::put('email', $EMAIL);
                        Session::put('name', $NAME);

                        
                        if (StringUtils::notEmpty($_GET['redirecturl'])) {
                            //parser redirect url
                            return Redirect::to(Common::parseRedirectUrl());
                        } else {
                            return Redirect::to(Config::get('app.url'));
                        }

                        return Redirect::intended('/');

                    } else if ($user && $user['user_id'] > 0) {
                        $user = User::where('email', Input::get('email'))->first();
                        if ($user && $user->password == md5(Input::get('password'))) {
                            // change password
                            $user->password = Hash::make(Input::get('password'));
                            $user->save();

                            Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')), $remember);
                            $User->setLastLogged($user['user_id'], time());
                            $User->setIpAddress($user['user_id'], Common::getClientIP());
                            $User->setUserAgent($user['user_id'], $_SERVER['HTTP_USER_AGENT']);

                            // store session
                            $USER_ID = Auth::id();
                            $USER_CODE = User::getInstance()->getUserField($USER_ID, 'user_code');
                            $EMAIL = User::getInstance()->getUserField($USER_ID, 'email');
                            $NAME = User::getInstance()->getUserField($USER_ID, 'name');

                            Session::put('user_id', $USER_ID);
                            Session::put('user_code', $USER_CODE);
                            Session::put('email', $EMAIL);
                            Session::put('name', $NAME);

                            if (StringUtils::notEmpty($_GET['redirecturl'])) {
                                //parser redirect url
                                return Redirect::to(Common::parseRedirectUrl());
                            } else {
                                return Redirect::to(Config::get('app.url'));
                            }
                        }
                    } else {
                        $viewData['msg'] = 'Thông tin đăng nhập không chính xác.<br />Vui lòng kiểm tra lại.';
                        $viewData['meta_title'] = 'Đăng nhập';
                        $viewData['flag'] = false;
                        $viewData['meta_robots'] = 'noindex, follow';
                        if (Common::isMobile()) {
                            return View::make('client::oldmobile.user.login', $viewData);
                        } else {
                            return View::make('client::old.user.login', $viewData);
                        }
                    }
                } else {
                    $viewData['msg'] = 'Bạn chưa nhập đầy đủ thông tin đăng nhập';
                    $viewData['meta_title'] = 'Đăng nhập';
                    $viewData['flag'] = false;
                    $viewData['meta_robots'] = 'noindex, follow';
                    if (Common::isMobile()) {
                        return View::make('client::oldmobile.user.login', $viewData);
                    } else {
                        return View::make('client::old.user.login', $viewData);
                    }
                }
            } else {
                $viewData['meta_title'] = 'Đăng nhập';
                $viewData['meta_robots'] = 'noindex, follow';
                if (Common::isMobile()) {
                    return View::make('client::oldmobile.user.login', $viewData);
                } else {
                    return View::make('client::old.user.login', $viewData);
                }
            }
        } else {
            if (StringUtils::notEmpty($_GET['redirecturl'])) {
                //parser redirect url
                return Redirect::to(Common::parseRedirectUrl());
            } else {
                return Redirect::to('/');
            }
        }
    }

    public function userinfo($usercode)
    {
        $userObj = User::getInstance();
        $userId = $userObj->getUserIdByUserCode($usercode);
        if ($userId) {
            $userInfo = $userObj->getUserById($userId);
            if ($userInfo) {
                $provinceObj = Province::getInstance();
                $retailerObj = Retailer::getInstance();
                $shopObj = Shop::getInstance();
                $productObj = Product::getInstance();
                $address = '';
                $phone = '';


                if ($userInfo['account_type'] != 1) {
                    $retailerInfo = $retailerObj->getRetailerByUserId($userId);
                    if ($retailerInfo) {
                        $retailerOffice = $retailerObj->getOffice($retailerInfo['id']);
                        $address = $retailerOffice[0]['address'] . ', ' . $provinceObj->getDistrictCityById($retailerOffice[0]['district'])['name'] . ', ' . $provinceObj->getDistrictCityById($retailerOffice[0]['city'])['name'];

                        $retailerPhone = $retailerObj->getPhone($retailerInfo['id']);
                        $phone = "";
                        foreach ($retailerPhone as $ph) {
                            $phone .= $ph['phone'] . ', ';
                        }

                    }
                } else {
                    $shopInfo = $shopObj->getShopByUserId($userId);
                    if ($shopInfo) {
                        $address = $shopInfo['address'] . ', ' . $provinceObj->getDistrictCityById($shopInfo['district'])['name'] . ', ' . $provinceObj->getDistrictCityById($shopInfo['city'])['name'];

                        $shopPhone = $shopObj->getPhone($shopInfo['id']);
                        $phone = "";
                        foreach ($shopPhone as $ph) {
                            $phone .= $ph['shop_phone'] . ', ';
                        }
                    }
                }

                //follow
                $followObj = Follow::getInstance();
                $userInfo['follow_total'] = $followObj->getFollowingTotal($userId, '2');

                //viewed product
                $productListViewedId = $userObj->getProductViewed($userId, 0, 14);
                $productListViewedTotal = count($productListViewedId);
                if ($productListViewedTotal > 0) {
                    for ($i = 0; $i < $productListViewedTotal; $i++) {
                        $p = $productObj->getProductById($productListViewedId[$i]);
                        if ($p['product_id']) {
                            $productListViewed[] = $p;
                        }
                    }
                    if (isset($productListViewed)) {
                        $viewsData['productlistviewed'] = $productListViewed;
                    }
                }

                //collection

                $wiliObj = Wili::getInstance();
                $collectionListId = $wiliObj->getGroupListByUserId($userId, 0, -1);

                $collectionSize = count($collectionListId);
                for ($i = 0; $i < $collectionSize; $i++) {
                    $collectionDetail = $wiliObj->getGroupById($collectionListId[$i]);
                    if ($collectionDetail) {
                        $collectionDetail['size'] = $wiliObj->countWishListByGroupId($collectionListId[$i], $userId);

                        //get preview 4 item
                        $productListId = $wiliObj->getWishListByUserGroupId($userId, $collectionListId[$i], 0, 3);
                        $productListCount = count($productListId);
                        $productList = array();
                        if ($productListCount > 0) {
                            for ($y = 0; $y < $productListCount; $y++) {
                                $w = $wiliObj->getWishListById($productListId[$y]);
                                if ($w) {
                                    $p = $productObj->getProductById($w['productid']);
                                    if ($p['product_id']) {
                                        $productList[] = $p;
                                    }
                                }

                            }
                            $collectionDetail['product'] = $productList;
                            $collectionList[] = $collectionDetail;
                        }
                    }
                }
                if (isset($collectionList)) {
                    $collectionSize = count($collectionList);

                    $viewsData['collectionlist'] = $collectionList;
                    $viewsData['collectionsize'] = $collectionSize;
                }

                $userInfo['address'] = $address;
                $userInfo['phone'] = trim($phone, ', ');
                $viewsData['user_profile_info'] = $userInfo;
                $viewsData['meta_title'] = $userInfo['first_name'] . ' ' . $userInfo['last_name'] . ' - Thị Trường Sỉ';
                $viewsData['meta_robots'] = 'index, follow';

                if (Common::isWebApp()) {
                    return View::make('client::oldmobile.user.profile_page_webapp', $viewsData);
                } else {
                    if (Common::isMobile()) {
                        return View::make('client::oldmobile.user.profile_page', $viewsData);
                    } else {
                        return View::make('client::old.user.profile_page', $viewsData);
                    }
                }
            } else {
                return Redirect::to('/');
            }
        } else {
            return Redirect::to('/');
        }
    }

    public function status()
    {
        //check user login
        Auth::check();
        $resp = array();
        $resp['login'] = Session::get('login');
        $resp['user_id'] = Auth::getUserId();

        echo json_encode($resp);
    }

    public function logout()
    {
        $token = Cache::get('sso-1:user:' . Auth::getUserId() . ':token');
        if ($token) {
            Cache::forget('sso-1:token:' . $token . ':content');
        }
        Auth::logout();

        return Redirect::to(Config::get('app.message_url') . 'online/logout?t=' . $_SERVER['REQUEST_TIME'] . '&redirect_uri=' . Config::get('app.url'));
    }

    public function forgot()
    {
        $userObj = User::getInstance();
        if (!Auth::check()) {
            if (Input::get('do') == 'forgot' && StringUtils::notEmpty(Input::get('email'))) {
                $email = Input::get('email');
                $userInfo = $userObj->getUserByEmail($email);
                if ($userInfo) {
                    $code = md5(time() . $email);
                    $userObj->setForgotCode($userInfo['user_id'], $code);
                    EmailUtils::forgotPassword($userInfo['user_id'], $email, $code);
                    $viewsData['success'] = true;
                    if (Common::isMobile()) {
                        return View::make('client::oldmobile.user.forgot', $viewsData);
                    } else {
                        return View::make('client::old.user.forgot', $viewsData);
                    }
                } else {
                    $viewsData['meta_title'] = 'Quên mật khẩu';
                    $viewsData['msg'] = 'Email này không tồn tại trong hệ thống của Thị Trường Sỉ';
                    if (Common::isMobile()) {
                        return View::make('client::oldmobile.user.forgot', $viewsData);
                    } else {
                        return View::make('client::old.user.forgot', $viewsData);
                    }
                }
            } else {
                $viewsData['meta_title'] = 'Quên mật khẩu';
                if (Common::isMobile()) {
                    return View::make('client::oldmobile.user.forgot', $viewsData);
                } else {
                    return View::make('client::old.user.forgot', $viewsData);
                }
            }
        }
    }

    public function forgotverify()
    {
        $userId = Input::get('uid');
        $code = Input::get('code');
        $userObj = User::getInstance();
        $viewsData = [];

        if (StringUtils::notEmpty($userId) && StringUtils::notEmpty($code)) {
            $check = $userObj->verifyForgotCode($userId, $code);
            if ($check) {
                if (Input::get('do') == 'forgot') {
                    $newpass = Input::get('password');
                    $renewpass = Input::get('password2');

                    if ($newpass == $renewpass) {
                        $userObj->setPassword($userId, md5($newpass));

                        $user = User::find($userId);
                        // change password
                        $user->password = Hash::make(Input::get('password'));
                        $user->save();

                        $userObj->setForgotCode($userId, '');
                        $viewsData['success'] = true;
                        $viewsData['msg'] = 'Chúc mừng bạn đã lấy lại mật khẩu thành công! Vui lòng <a href="' . Config::get('app.url') . 'user/login/">đăng nhập</a> để sử dụng.';
                    } else {
                        $viewsData['msg'] = 'Mật khẩu mới và mật khẩu nhập lại không khớp';
                    }
                }
                if (Common::isMobile()) {
                    return View::make('client::oldmobile.user.forgotsetpassword', $viewsData);
                } else {
                    return View::make('client::old.user.forgotsetpassword', $viewsData);
                }

            } else {
                $viewsData['msg'] = 'Địa chỉ để lấy lại mật khẩu không hợp lệ. Vui lòng <a href="' . Config::get('app.url') . 'user/forgot/">bấm vào đây</a> để lấy lại mật khẩu lần nữa.';
                if (Common::isMobile()) {
                    return View::make('client::oldmobile.alert', $viewsData);
                } else {
                    return View::make('client::old.alert', $viewsData);
                }
            }
        } else {
            return Redirect::to('/');
        }
    }

    public function regretaier()
    {
        if (Auth::check()) {
            //not exists retailer info
            $retailerObj = Retailer::getInstance();
            if (!$retailerObj->getRetailerByUserId(Auth::getUserId())) {

            } else {
                header('Location: /');
            }
        } else {
            echo 'not login';
        }
    }

    public function vipPlan()
    {
        $statObj = Statistics::getInstance();
        $statObj->viewSurvey('vip_pre', Auth::getShopId());
        $shopObj = Shop::getInstance();
        $shopCity = $shopObj->getShopById(Auth::getShopId())['city'];
        $viewsData['shop_city'] = $shopCity;
        $viewsData['meta_title'] = 'Đăng ký VIP';
        if (Common::isMobile()) {
            return View::make('client::oldmobile.user.vipplan', $viewsData);
        } else {
            return View::make('client::old.user.vipplan', $viewsData);
        }
    }


    public function changePassword()
    {
        $do = Input::get('do');
        $userObj = User::getInstance();
        if (isset($do) && $do == 'change') {
            $currentPassword = md5($_POST['password']);
            $newPassword = $_POST['newpassword'];
            $newPasswordConfirm = $_POST['newpassword_confirm'];

            if (StringUtils::notEmpty($currentPassword) && StringUtils::notEmpty($newPassword) && StringUtils::notEmpty($newPasswordConfirm)) {

                if (strlen($newPassword) < 6) {
                    $viewsData['flag'] = 4;
                } else {
                    if ($userObj->getUserField(Auth::getUserId(), 'password') === $currentPassword) {
                        if ($newPassword === $newPasswordConfirm) {
                            $userObj->setUserField(Auth::getUserId(), 'password', md5($newPassword));
                            $viewsData['flag'] = 1;


                            $emailObj = EmailUtils::getInstance();
                            $subject = 'Đổi mật khẩu trên Thị Trường Sỉ';
                            $body = 'Bạn vừa thay đổi mật khẩu cho tài khoản: ' . Auth::getEmail() . '<br />
                                Hãy gọi đến 1900 6074 hoặc gửi email đến support@thitruongsi.com để được hỗ trợ nếu cần.<br />
                                <br />
                                Cám ơn bạn đã sử dụng Thị Trường Sỉ,<br />
                                TTS Team.
                                ';
                            $emailObj::send('account@thitruongsi.com', Auth::getEmail(), $subject, $body);

                        } else {
                            $viewsData['flag'] = 3;
                        }
                    } else {
                        //wrong password
                        $viewsData['flag'] = 2;
                    }
                }
            } else {
                $viewsData['flag'] = 0;
            }
        }
        $viewsData['meta_title'] = 'Thay đổi mật khẩu';

        return View::make('client::old.user.changepassword', $viewsData);
    }

}
