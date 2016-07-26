@extends('client::layouts.default.layout')

@section('content')
<div class="col-xs-12 col-sm-10" style="text-align: center;">
  <div class="row">
    @foreach($product as $productObj)
    <div class="col-sm-6">
      <a href="{{URL::route('productdetail', array($productObj->identity, $productObj->product_id))}}" class="panel-delault">
        <div class="panel-body thumbnail">
          <img src="{{Config::get('app.static_image_product_url').$productObj->image}}" class="img">
          <div class="clearfix"></div>
          <div class="panel-heading">
          <?php $price = $productObj->option()->first() ?>
            <h5 class="text-center">{{$productObj->name}} <br> {{number_format($price->gia)}} USD</h5>
          </div>
        </div><!--/panel-body-->
      </a><!--/panel-->
    </div><!--/col-sm-6-->
    @endforeach
  </div><!--/.row-->
{{$product->links()}}
</div><!--/.col-xs-12-->

@stop