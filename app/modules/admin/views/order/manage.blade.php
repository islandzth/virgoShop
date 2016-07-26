@extends('admin::layouts.default.layout')

@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Quản lý Đơn hàng
            </h4>
            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
            <div style="background:#EBEBEB; padding: 5px">
                <a href="{{URL::route('orderManage')}}">Tất cả</a> | 
                @for($i = 1; $i <= count($arrStatus); $i++)
                    <a href="{{URL::route('orderManage').'?stt='.$i}}">{{$arrStatus[$i]['text']}}</a> | 
                @endfor
            </div>
        </div>
        <div class="widget-body">
            @if(count($arrOrder) >0 )
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Trạng thái</th>
                    <th>Đổi trạng thái</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($arrOrder as $key => $val)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$val->customer_name}}</td>
                            <td>{{number_format($val->customer_phone)}}</td>
                            <td>{{$val->customer_address.'-'.District::where('districtid', $val->customer_district)->pluck('name').'-'.Province::where('provinceid', $val->customer_province)->pluck('name')}}</td>
                            <td>
                                <span class="label {{$arrStatus[$val->status]['label']}}">{{$arrStatus[$val->status]['text']}}</span>
                            </td>
                            <td>
                                @if($val->status >= 4)
                                    
                                @else
                                    <button stt="{{$val->status + 1}}" oid="{{$val->order_id}}" class="btn btn-primary" id="changeStt">{{$arrStatus[$val->status + 1]['text']}}</button><br/>
                                    <button stt="5" oid="{{$val->order_id}}" class="btn btn-default" id="changeStt">Huy</button>
                                @endif
                            </td>
                            <td><a href="{{URL::route('orderEdit', array($val->order_id))}}">Sửa đơn hàng</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                Không có đơn hàng nào
            @endif
            {{$arrOrder->links();}}
        </div>
    </div>
@stop

@section('scripts')
    <script>
        var url_change = '{{URL::route('ajaxChangeSttOrder')}}';
        $( document ).ready(function(){
            $('body').on('click', 'button[id="changeStt"]', function(){
                var oid = $(this).attr('oid');
                var stt = $(this).attr('stt');
                var trObj = $(this).parents('tr');
                console.log(oid+' - '+stt+' - '+trObj.length);

                $.get( url_change, { stt: stt, oid: oid }, function( data ) {
                    data = jQuery.parseJSON(data);
                    location.reload();
                });
            })
        })
    </script>
@stop
