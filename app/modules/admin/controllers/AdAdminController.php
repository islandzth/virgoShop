<?php

class AdAdminController extends AdBaseController
{

    public function index($args = null)
    {
        return View::make('admin::index');
    }
    public function manage(){
        $viewData['listUser'] = UserAdmin::all();
        return View::make('admin::admin.list', $viewData);
    }
    public function createUser(){
        $userObj = new UserAdmin();
        $viewData['msg'] = null;
        if(Request::isMethod('post')){
            if (StringUtils::notEmpty(Input::get('username')) && StringUtils::notEmpty(Input::get('password'))){
                if(count($userObj->getByUserName(Input::get('username'))) > 0){
                    $viewData['msg'] = 'Tên đăng nhập đã tồn tại';
                }else{
                    $userObj->createUser(Input::get('username'),  Hash::make(Input::get('password')), 1);  
                    $viewData['msg'] = 'Tạo tài khoản thành công';
                }
            }else{
                $viewData['msg'] = 'Tài khoản hoặc mật khẩu trống!';
            }
        }
        return View::make('admin::admin.reg', $viewData);
    }
    public function login()
    {
        return View::make('admin::admin.login');
    }

    public function checkLogin()
    {   
        if (StringUtils::notEmpty($_POST['username']) && StringUtils::notEmpty($_POST['password'])) {
            //verify
            $User = UserAdmin::getInstance();

            // get user
            $user = $User->getByUserName($_POST['username']);

            if($user){
                if (Hash::check($_POST['password'], $user->password)){
                    if($user->lv == 0){
                        $viewData['msg'] = 'Tài khoản vô hiệu.';
                        return View::make('admin::admin.login', $viewData);
                    }
                    Session::put('user_id', $user->id);
                    return Redirect::route('adminPage');
                }else{
                    Session::forget('user_id');
                    $viewData['msg'] = 'Thông tin đăng nhập không chính xác.';
                    return View::make('admin::admin.login', $viewData);
                }
            }else{
                Session::forget('user_id');
                $viewData['msg'] = 'Thông tin đăng nhập không chính xác.';
                return View::make('admin::admin.login', $viewData);
            }

            
        } else {
            Session::forget('user_id');
            $viewData['msg'] = 'Bạn chưa nhập đầy đủ thông tin đăng nhập';
            return View::make('admin::admin.login', $viewData);
            // $this->view->render(__TEMP_DIR . '/user/login');
        }
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::to('/');
    }

    public function test()
    {
        echo "string";
        // $username = 'sponsor';
        // $password = 'statioreny';
        // $powers = 3;

        // if ($powers == 3) //nha tai tro
        //     $sponsor_id = 1;
        // else {
        //     $sponsor_id = '';
        // }

        // $user_id = $this->model->createAccount($username, $password, $powers, $sponsor_id);
    }

}

?>
