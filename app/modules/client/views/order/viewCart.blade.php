@extends('client::layouts.default.layout')

@section('content')
<div class="col-sm-8">
        <div class="container-fluid">
          @if($err == 2)
            <div class="alert alert-warning" role="alert">{{$msg}}</div>
          @else
            <div class="row">
              <div class="vr-line"></div>
              <h4><SPAN>SHOPING BASKET</SPAN></h4>
              <div class="vr-line"></div>
              <div class="col-sm-3">
                <h5></h5>
              </div>
              <div class="col-sm-3">
                <h5>Name Product</h5>
              </div>
              <div class="col-sm-3">
                <h5>Size</h5>
              </div>
              <div class="col-sm-3 pull-right">
                <h5>Price</h5>
              </div>
          </div>
          <div class="vr-line"></div>
          <!--Start -->
          @for($i = 0; $i < count($arrkq); $i++)
            <div class="row">
                <div class="col-sm-3">
                  <div class="row">
                    <div class="col-sm-6 ">
                      <a href="" class="thumbnail">
                        <img src="{{$pathImg.$arrkq[$i]['productImage']}}" class="img-thumbnail">
                      </a>  
                    </div>
                </div>
                </div>

                <div class="col-sm-3">
                  <h5>{{$arrkq[$i]['productName']}}</h5>
                </div>
                <div class="col-sm-3">
                  <h5>{{$arrkq[$i]['productOption']}}</h5>
                </div>
                <div class="col-sm-3">
                  <h5>{{number_format($arrkq[$i]['productPrice'])}}</h5>
                <li class="col-sm-2 pull-right">
                  <div class="social-icons"> 
                    <ul class="nav navbar-nav">
                      <li><a href="#" id="removeProduct" vid="{{$arrkq[$i]['vid']}}"><i class="fa fa-times"></i></a></li>
                    </ul>
                  </div>
                </li>
                </div>
            </div>
            <div class="vr-line"></div>
          @endfor
          <!--END-->
          <!--total -->
          <div class="pull-right">
            <h1>Total product</h1>

          </div>
          <!--END Total-->
          @endif
          
          <a href="{{URL::route('index')}}" class="btn btn-primary">CONTINUE SHOPING</a>
          <a href="{{URL::route('checkout')}}" class="btn btn-primary pull-right">CONTINUE</a>
        </div>
      </div>

    </div><!--/.row row-offcanvans-->

@stop
@section('scripts')
<script type="text/javascript">
  var urlrm = '{{URL::route('ajaxRmProducToCart')}}';
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
  });
</script>
  

@stop