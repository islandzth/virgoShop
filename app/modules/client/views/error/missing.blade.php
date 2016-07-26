@extends('client::layouts.default.layout')

@section('content')
<div class="col-xs-12 col-sm-10" style="text-align: center;">
  <div>
    <img src="{{Config::get('app.static_url_client').'images/404.jpg'}}">
  </div>
  <div style="color: black; font-size: 14px; padding-top: 10px;">
    <p>Chúng tôi rất tiếc khi bạn phải nhìn thấy thông báo này.</p>
    <p>Trang bạn vừa yêu cầu không tồn tại.</p>
    <p>Liên kết này có thể đã bị xóa bởi người quản trị hoặc bạn đã có nhầm lẫn chăng?</p>
  </div>
</div><!--/.col-xs-12-->
@stop