@extends('admin::layouts.default.layout')

@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Sửa đơn hàng
            </h4>
            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
            <div style="padding: 5px; margin-bottom:10px">
                @if(isset($err))
                    @if($err == 1)
                        <div class="alert alert-danger" role="alert">{{$msg}}</div>
                    @elseif($err == 0)
                        <div class="alert alert-success" role="alert">{{$msg}}</div>
                    @endif
                @endif
                
            </div>
        </div>
        <div class="widget-body">
            {{ Form::open(array('url' => URL::route("orderEdit", array($orderObj->order_id)), 'class' => 'form-horizontal', 'method' => 'post', 'id' => 'formInfo')) }}
                <div class="form-group">
                  <label for="customerName" class="col-sm-2 control-label">Tên khách hàng</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="customerName" id="customerName" value="{{$orderObj->customer_name}}" >
                  </div>
                </div>

                <div class="form-group">
                  <label for="customerPhone" class="col-sm-2 control-label">Số điện thoại</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="customerPhone" id="customerPhone" value="{{$orderObj->customer_phone}}">
                  </div>
                </div>

                <div class="form-group">
                  <label for="customerEmail" class="col-sm-2 control-label">Email</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="customerEmail" id="customerEmail" value="{{$orderObj->customer_email}}">
                  </div>
                </div>

                <div class="form-group">
                  <label for="customerAddress" class="col-sm-2 control-label">Địa chỉ</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputEmail3" name="customerAddress" value="{{$orderObj->customer_address}}">
                  </div>
                </div>

                <div class="form-group">
                  <label for="customerProvince" class="col-sm-2 control-label">Tỉnh/thành</label>
                  <div class="col-sm-4">
                    <select class="form-control" name="customerProvince" id="selectProvince">
                      @foreach($province as $provinceObj)
                        @if($orderObj->customer_province == $provinceObj->provinceid)
                            <option value="{{$provinceObj->provinceid}}" selected>{{$provinceObj->name}}</option>
                        @else
                            <option value="{{$provinceObj->provinceid}}">{{$provinceObj->name}}</option>
                        @endif
                        
                      @endforeach
                    </select>
                  </div>
                  <label for="customerDistrict" class="col-sm-2 control-label">Quận/huyện</label>
                  <div class="col-sm-4">
                    <select class="form-control" name="customerDistrict" id="selectDistrict">
                      @foreach($district as $districtObj)
                        @if($orderObj->customer_district == $districtObj->districtid)
                            <option value="{{$districtObj->districtid}}" selected>{{$districtObj->name}}</option>
                        @else
                            <option value="{{$districtObj->districtid}}">{{$districtObj->name}}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="customerAddress" class="col-sm-2 control-label">Ghi chú (nếu có)</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" rows="3" name="customerNote">{{$orderObj->note}}</textarea>
                  </div>
                </div>
                <hr/>
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10" style="text-align: center;">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                  </div>
                </div>
            {{ Form::close() }}
            <hr/>
            <h4>Sản phẩm đặt hàng</h4>
            <table class="table">
                <tr>
                    <th>STT</th>
                    <th>Hình ảnh</th>
                    <th>Tên đơn hàng</th>
                    <th>Thuộc tính</th>
                    <th>Gia</th>
                    <th>Xoa</th>
                </tr>
                    @foreach($metaOrder as $key => $metaObj)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td><img src="{{$url_img.$metaObj->image}}" style="width:80px" /></td>
                            <td>{{$metaObj->name}}</td>
                            <td>{{$metaObj->size}}</td>
                            <td>{{number_format($metaObj->gia)}}</td>
                            <td><a href="#" id="removeMeta" oid="{{$metaObj->order_id}}" metaid="{{$metaObj->id}}"><i class="glyphicon glyphicon-remove"></i></a></td>
                        </tr>
                    @endforeach
                    
            </table>
            <div>
                <table class="table" style="width:30%; float:right; font-size:16px">
                    <tr>
                        <td><b>Tổng tiền:</b></td>
                        <td id="show_totalpay">{{number_format($totalPay)}}</td>
                    </tr>
                        
                </table>
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        var url_rm = '{{URL::route('ajaxRmMetaOrder')}}';
        $( document ).ready(function(){
            $('body').on('click', 'a[id="removeMeta"]', function(e){
                console.log("a");
                e.preventDefault();
                var oid = $(this).attr('oid');
                var metaid = $(this).attr('metaid');
                var trObj = $(this).parents('tr');
                $.get( url_rm, { metaid: metaid, oid: oid }, function( data ) {
                    data = jQuery.parseJSON(data);
                    if(data['err'] == 1){
                        alert('Đã có lỗi xãy ra, ok để load lại trang');
                        location.reload();
                    }else if(data['err'] == 2){
                        trObj.remove();
                        alert('Đơn hang không còn sản phẩm nào');
                        $('#show_totalpay').text(data['totalPay']);

                    }else{
                        trObj.remove();
                        alert('Đã xóa sản phẩm');
                        $('#show_totalpay').text($.number(data['totalPay']));
                        
                    }
                    
                });
            })
            
        })
    </script>
@stop
