@extends('client::layouts.default.layout')
@section('styles')
<link rel="stylesheet" href="{{asset('static/virgo/css/styleDetails.css')}}">
<style type="text/css">
  .selectSize{
    background: #ebebeb !important;
  }
  .selectSize a:hover{
    background: #ebebeb !important;
  }
</style>
@stop
@section('content')
<div class="col-xs-12 col-sm-8">
  
  <div class="row">
    @foreach($images as $imageObj)
     <div class="col-sm-6">
        <div class="panel-delault">
          <div class="panel-body thumbnail">
            <img src="{{Config::get('app.static_image_product_url').$imageObj->image_name}}" class="img">
          </div><!--/panel-body-->
        </div><!--/panel-->
      </div><!--/col-sm-6-->
    @endforeach

  </div><!--/.row-->
  
</div><!--/.col-xs-12-->
<!--sidebar right-->
<div class="col-sm-2 vr-left-sidebar ">
  <div class="sidebar-offcanvas" id="sidebar">
    <div>
      <ul class="nav navbar">
        <li><a href="{{URL::route('productdetail', array($productObj->identity, $productObj->product_id))}}">{{$productObj->name}}</a></li>
        <li><a href="#"><h4 id="current_price"> </h4></a></li>
        </br>
        <span class="heading-title">Chọn SIZE</span>
        <div class="vr-line"></div>
        @foreach($options as $key => $val)
          @if($key == 0)
            <li id="selectPrice" class="selectSize" price="{{$val->gia}}" vid="{{$val->id}}"><a href="#">{{$val->size}}</a></li>
          @else
            <li id="selectPrice" price="{{$val->gia}}" vid="{{$val->id}}"><a href="#">{{$val->size}}</a></li>
          @endif
        @endforeach
        </br>
        <button type="buttom" class="btn btn-primary" data-toggle="modal" data-target="#myModal" data-backdrop="true" id="addToCart">Đưa Vào Giỏ Hàng</button>
      </ul>
     </div>
  </div>
</div>
  <!-- vr-left-sidebar right-->
<!-- Modal -->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="myModal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content" style="padding: 10px; color: #000; text-align: center;">
      <div class="modal-header">
        <button id="close_modal" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -12px;"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="alert alert-success" role="alert" style="font-size: 14px;" id="alert_success" >Đã thêm sản phẩm vào giỏ hàng</div>
      <div class="alert alert-danger" role="alert" style="font-size: 14px;" id="alert_fail">Thêm sản phẩm thất bại</div>
      <div>
        <a href="{{URL::route('viewCart')}}" type="button" class="btn btn-default">Xem giỏ hàng</a>
        <a href="{{URL::route('checkout')}}" type="button" class="btn btn-primary">Thanh toán</a>
      </div>
    </div>
  </div>
</div>
@stop
@section('scripts')
<script type="text/javascript">
  var url = '{{URL::route('ajaxAddVidToOrder')}}';
  $( document ).ready(function() {
      var current_price = $('.selectSize').attr('price');
      current_price = $.number(current_price);
      $('#current_price').text(current_price+' VNĐ');

      $('li#selectPrice').click(function(){
        $('li.selectSize').removeClass('selectSize');
        
        var current_price = $(this).attr('price');
        current_price = $.number(current_price);
        $('#current_price').text(current_price+' VNĐ');
        $(this).addClass('selectSize');
      })
      $('#myModal').on('shown.bs.modal', function () {
        
      })
      $('.modal').click(function(){
        $('#close_modal').click();
      })

      //add vid
      $('#addToCart').click(function(){
        $(this).attr('disable', 'true');
        var vid = $('.selectSize').attr('vid');
        $.get( url+"?vid="+vid, function( data ) {
          
          if(data == 1){
            $('#alert_success').css('display', 'block');
            $('#alert_fail').css('display', 'none');
            $(this).attr('disable', 'false');
          }else{
            $('#alert_success').css('display', 'none');
            $('#alert_fail').css('display', 'true');
            $(this).attr('disable', 'false');
          }
          
        });
      })
  });
</script>
  

@stop