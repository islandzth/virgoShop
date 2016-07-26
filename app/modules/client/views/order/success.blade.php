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
      <div class="col-sm-7 col-sm-offset-1" style="color:#000; text-align:center">
          @if(err == 0)
            <div class="alert alert-success" role="alert">
              {{$msg}}<br/>
              Chuyển hướng về giỏ hàng trong 5s...
              
            </div>
          @else
            <div class="alert alert-danger" role="alert">
              {{$msg}}<br/>
              Chuyển hướng về trang chủ trong 5s...
            </div>
          @endif
          <meta http-equiv="refresh" content="5; url={{$url_return}}" />
      </div>
    </div>
  </div>
</div>



@stop
@section('scripts')
<script type="text/javascript">
  


</script>
  

@stop