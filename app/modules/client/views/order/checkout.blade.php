@extends('client::layouts.default.layout')
@section('styles')
<style type="text/css">
	label.error{
		padding-top: 5px;
		color: red;
	}
</style>
@stop
@section('content')
<div class="col-sm-8">
  <div class="row">
    <div class="container">
      <div class="col-sm-7 col-sm-offset-1" style="color:#000">

      <div class="panel-heading" style="padding: 0;">
        <h4 class="panel-title text-center" style="text-align: left; font-size:16px">Thông tin khách hàng </h4>
      </div>
      <hr/>
      {{ Form::open(array('url' => URL::route("success"), 'class' => 'form-horizontal', 'method' => 'post', 'id' => 'formInfo')) }}
        <div class="form-group">
          <label for="customerName" class="col-sm-2 control-label">Tên khách hàng</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="customerName" id="customerName" >
          </div>
        </div>

        <div class="form-group">
          <label for="customerPhone" class="col-sm-2 control-label">Số điện thoại</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="customerPhone" id="customerPhone">
          </div>
        </div>

        <div class="form-group">
          <label for="customerEmail" class="col-sm-2 control-label">Email</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="customerEmail" id="customerEmail">
          </div>
        </div>

        <div class="form-group">
          <label for="customerAddress" class="col-sm-2 control-label">Địa chỉ</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" id="inputEmail3" name="customerAddress">
          </div>
        </div>

        <div class="form-group">
          <label for="customerProvince" class="col-sm-2 control-label">Tỉnh/thành</label>
          <div class="col-sm-4">
            <select class="form-control" name="customerProvince" id="selectProvince">
              @foreach($province as $provinceObj)
              	<option value="{{$provinceObj->provinceid}}">{{$provinceObj->name}}</option>
              @endforeach
            </select>
          </div>
          <label for="customerDistrict" class="col-sm-2 control-label">Quận/huyện</label>
          <div class="col-sm-4">
            <select class="form-control" name="customerDistrict" id="selectDistrict">
              @foreach($district as $districtObj)
              	<option value="{{$districtObj->districtid}}">{{$districtObj->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="customerAddress" class="col-sm-2 control-label">Ghi chú (nếu có)</label>
          <div class="col-sm-10">
          	<textarea class="form-control" rows="3" name="customerNote"></textarea>
          </div>
        </div>
        <hr/>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10" style="text-align: center;">
            <a href="{{URL::route('viewCart')}}" class="btn btn-default" style="margin-right:10px">Về giỏ hàng</a>
            <button type="submit" class="btn btn-primary">Đặt hàng</button>
          </div>
        </div>
      {{ Form::close() }}
      </div>
    </div>
  </div>
</div>

<div class="col-sm-2">
  <div class="col-sm-2 vr-left-sidebar ">
  <div class="sidebar-offcanvas" id="sidebar">
    <div>
    <div class="panel panel-default">
      <div class="panel-heading text-center"><h3>GIỎ HÀNG</h3></div>
        <div class="panel-body">
          <ul class="nav navbar">
            <span class="heading-title">SỐ TIỀN</span>
            <div class="vr-line"></div>
            <li><a href="#"><h4>{{number_format($totalPrice)}} VNĐ</h4></a></li>

            <span class="heading-title">PHÍ SHIP</span>
            <div class="vr-line"></div>
            <li><a href="#"><h4>0 VNĐ</h4></a></li>

            <span class="heading-title">THUẾ GTGT</span>
            <div class="vr-line"></div>
            <li><a href="#"><h4>0 VNĐ</h4></a></li>


          </ul>
      </div>
    </div>
    </div>
  </div>
  </div>
</div>

@stop
@section('scripts')
<script type="text/javascript" src="{{asset('static/virgo/js/jquery.validate.min.js')}}"></script>
<script type="text/javascript">
  var urlgd = '{{URL::route('ajaxGetDistrict')}}';
  $( document ).ready(function() {
    $('a[id="removeProduct"]').click(function(event){
      event.preventDefault()
        var vid = $(this).attr('vid');
        var divrm = $(this).parents('div[class="row"]');
        $.get( urlrm+"?vid="+vid, function( data ) {
          if(data == 1){
            alert('Đã xóa sản phẩm');
          }else{
            alert('Đã xóa sản phẩm thất bại');
          }
          divrm.remove();
        });
    })

    // validate signup form on keyup and submit
	$("#formInfo").validate({
		rules: {
			customerName: "required",
			customerPhone: {
				required: true,
				number: true,
				minlength: 9
			},
			customerEmail: {
				email: true
			},
			customerAddress: {
				required: true,
				minlength: 5
			},
			customerProvince: {
				required: true
			},
			customerDistrict: {
				required: true
			}
		},
		messages: {
			customerName: "Nhập tên",
			customerPhone: {
				required: "Nhập số điện thoại",
				number: "chỉ nhập số",
				minlength: "Số điện thoại lớn hơn 9 số"
			},
			customerEmail: {
				email: "Kiểm trả lại email"
			},
			customerAddress: {
				required: "Nhập đia chỉ",
				minlength: "Địa chỉ lớn hơn 5 kí tự"
			}
		}
	});
	$('#selectProvince').change(function(){
		var self = $(this);
		self.css('disable', 'true');
		$.get( urlgd+"?provinceid="+self.val(), function( data ) {
			data = jQuery.parseJSON(data);
			if(data['err'] == 0){
				var temp = '';
				var district = $('#selectDistrict');
				for(var i = 0; i < data['district'].length; i++){
					temp += '<option value="'+data['district'][i]['districtid']+'">'+data['district'][i]['name']+'</option>';
				}
				district.find('option').remove();
				district.append(temp);
			}else{
				alert('Đã xóa sản phẩm thất bại');
			}
			self.css('disable', 'false');
        });
	})


  });
</script>
  

@stop