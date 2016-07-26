<?php

class ContactController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (isset($_POST) && isset($_POST['messagecontent']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $from = "thitruongsicom@gmail.com";
            $to = "lequivn@gmail.com";
            $subject = "Đóng góp ý kiến";
            $body = 'Từ email: ' . $_POST['email'] . '<br /><br />' . $_POST['messagecontent'];
            EmailUtils::send($from, $to, $subject, $body);
            $viewsData['msg'] = 'Cám ơn bạn đã gửi ý kiến đóng góp đến Thị Trường Sỉ.<br />Chúng tôi sẽ phản hồi lại cho bạn trong thời gian sớm nhất.';
            return View::make('client::old.contact.status', $viewsData);
        } else {
            return View::make('client::old.contact.index');
        }
    }
}