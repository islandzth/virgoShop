<?php

/**
 * Description of Email
 *
 * @author NhatTieu
 */
class EmailUtils
{

    var $smtp_host = '';
    var $smtp_port = '';
    var $stmp_type = '';

    private static $_instance;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public static function send($from, $to, $subject, $body, $name = '')
    {
//        $workload = ['from' => $from, 'to' => $to, 'subject' => $subject, 'body' => $body];
//        $jobObj = \App\Utils\GMJob::getInstance();
//        $jobObj->doBackground("sendmail", json_encode($workload));
        Mail::queue('email.default', array('body' => $body), function ($message) use ($to, $subject, $name) {
            $message->to($to, $name)->subject($subject);
        });
    }

    public static function sendDefault($from, $to, $subject, $body)
    {
        $headers = "From: =?UTF-8?B?" . base64_encode("Thị Trường Sỉ") . "?= <" . $from . ">\r\n";
        $headers .= "Reply-To: " . $from . "\r\n";
        $headers .= "Return-Path: " . $from . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n";

        $body = rtrim(chunk_split(base64_encode($body)));

        mail($to, $subject, $body, $headers, '-f' . $from);
    }

    public function emailRegister($to, $verifyCode, $lastname)
    {
        $emailContent = 'Chào bạn <strong>' . $lastname . '</strong>,<br />
            Bạn vừa đăng ký tài khoản tại <a href="' . Config::get('app.url') . '">' . Config::get('app.url') . '</a> bằng địa chỉ email: <strong>' . $to . '</strong><br />
                Nếu các thông tin trên là chính xác, vui lòng xác nhận đăng ký tài khoản bằng cách bấm vào địa chỉ: <a href="' . Config::get('app.url') . 'user/verify/' . $verifyCode . '">' . Config::get('app.url') . 'user/verify/' . $verifyCode . '</a><br />
                Nếu không, vui lòng bỏ qua email này.';
        //$emailContent = self::emailTemplate($emailContent, $to);
        self::send('account@thitruongsi.com', $to, 'Chào ' . $lastname . ', vui lòng xác nhận tài khoản', $emailContent, $lastname);
    }

    public function emailShopInfoChange($to, $suplierName)
    {
        $emailContent = 'Chào bạn,<br />
            Bạn vừa thay đổi thông tin nhà bán sỉ: <strong>' . $suplierName . '</strong><br />
            Hãy chú ý bảo vệ thông tin của mình.<br /><br />
            <strong>Thị Trường Sỉ</strong>.';
        //$emailContent = self::emailTemplate($emailContent, $to);
        self::send('account@thitruongsi.com', $to, 'Thay đổi thông tin nhà bán sỉ ' . $suplierName, $emailContent, $suplierName);
    }

    final public static function forgotPassword($uid, $email, $code)
    {
        $emailContent = 'Chào bạn<br />
		Bạn vừa yêu cầu lấy lại mật khẩu cho tài khoản trên Thị Trường Sỉ, vui lòng click vào đường dẫn bên dưới để nhập mật khẩu mới cho tài khoản với email: ' . $email . '<br />
		<br />
		<a href="' . Config::get('app.url') . 'user/forgotverify?uid=' . $uid . '&code=' . $code . '">' . Config::get('app.url') . 'user/forgotverify?uid=' . $uid . '&code=' . $code . '</a>
		<br />
		Nếu bạn cần bất kì sự hỗ trợ nào, vui lòng trả lời lại email này để được giúp đỡ.<br /><br />
		Thị Trường Sỉ.';
        self::send('account@thitruongsi.com', $email, 'Yêu cầu lấy lại mật khẩu trên Thị Trường Sỉ, tài khoản: ' . $email, $emailContent);
    }

    /**
     * 1 = new shop
     * @param string $to
     * @param int $type
     */
    public function alertEmail($to, $type = 1)
    {
        $emailContent = 'Vừa có nhà cung cấp mới đăng ký [' . date('h:i:s d-m', time()) . '], check ngay: <a href="https://thitruongsi.com/admincp/shop/index/0/">https://thitruongsi.com/admincp/shop/index/0/</a>';
        self::send('alert@thitruongsi.com', $to, 'Có nhà cung cấp mới', $emailContent, '');
    }

    public function shopTemplate($to, $type = 'approve', $shopName)
    {
        if ($type == 'approve') {
            $emailTitle = 'Tài khoản nhà bán sỉ ' . $shopName . ' của bạn đã được duyệt';
            $emailContent = 'Chào bạn,<br />'
                . 'Tài khoản nhà bán sỉ <strong>' . $shopName . '</strong> bạn tạo ở Thị Trường Sỉ đã được duyệt.<br />'
                . 'Ngay bây giờ bạn đã có thể truy cập vào <a href="' . Config::get('app.url') . 'shop/addproduct?utm_medium=email&utm_source=email&utm_campaign=shop-approve">' . Config::get('app.url') . 'shop/addproduct/</a> để đăng bán sản phẩm.<br />'
                . 'Và<br />'
                . '+ Viết thông báo: <a href="' . Config::get('app.url') . 'shop/editnotice">' . Config::get('app.url') . 'shop/editnotice</a><br />'
                . '+ Viết giới thiệu: <a href="' . Config::get('app.url') . 'shop/introduce">' . Config::get('app.url') . 'shop/introduce</a><br />'
                . 'Cám ơn bạn đã tham gia vào Thị Trường Sỉ.';
        }
        //$emailContent = self::emailTemplate($emailContent, $to);
        self::send('account@thitruongsi.com', $to, $emailTitle, $emailContent, $shopName);
    }

    public function scheduleShopApprove($to)
    {
        $emailObj = new Email_Model();
        $emailSubject = 'Bạn đã sử dụng Thị Trường Sỉ chưa?';
        $emailContent = 'Chào bạn,<br />
		Tài khoản nhà bán sỉ của bạn trên Thị Trường Sỉ đã được duyệt trước đó.<br />
		Hãy xem các bài viết trong mục hỗ trợ để sử dụng Thị Trường Sỉ tốt hơn: <a href="http://trogiup.thitruongsi.com?utm_medium=email&utm_source=email&utm_campaign=email-support">http://trogiup.thitruongsi.com/</a>
		Nếu bạn gặp khó khăn trong việc sử dụng hoặc muốn đóng góp ý kiến, xin đừng ngần ngại email cho chúng tôi qua: <a href="mailto:support@thitruongsi.com">support@thitruongsi.com</a> hoặc trả lời email này.<br />
		
		Cám ơn bạn đã tham gia Thị Trường Sỉ và chúc bạn thành công!<br /><br />
		ThiTruongSi.Com
		';
        //$emailContent = self::emailTemplate($emailContent, $to);
        $emailId = $emailObj->addEmail($emailSubject, 'support@thitruongsi.com', $to, $emailContent);
        if ($emailId) {
            $emailObj->addScheduler($emailId, strtotime("+2 day"));
        }
    }

    public static function emailTemplate($body, $to)
    {
        $html = '<div style="width: 500px">
<div style="background: #333;height: 20px;color: #FFF; padding: 8px">
<strong><a href="http://thitruongsi.com?ref=email" style="color: #FFF">Thị Trường Sỉ</a></strong>
</div>
<div style="padding: 10px">
' . $body . '
</div>
<div style="height: 25px;color: #999; padding: 10px;font-size: 9pt">
Gửi đến ' . $to . ' từ thitruongsi.com
</div>
</div>';
        return $html;
    }

}
