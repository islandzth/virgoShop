@extends('admin::layouts.default.layout')

@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Quản lý sản phẩm
            </h4>
            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
            <div style="background:#EBEBEB; padding: 5px">
                {{ Form::open(array('url' => URL::route('manageProduct'),'id'=>'createCat','class'=>'form-inline form-vertical no-padding no-margin', 'method'=>'get')) }}
                    
                    <div class="form-group">
                        <label class="sr-only">Email</label>
                        <p class="form-control-static">Từ khóa:</p>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword2" class="sr-only">Password</label>
                        <input type="text" name="filter_keyword" class="form-control" id="exampleInputEmail1" value="{{$filter_keyword}}">
                    </div>

                    <div class="form-group">
                        <label class="sr-only">Email</label>
                        <p class="form-control-static">Danh mục:</p>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword2" class="sr-only">Password</label>
                        <select class="form-control" style="width:200px" name="filter_cat">
                            <option value="0" selected>----</option>
                            @foreach($listCat as $catObj)
                                @if($catObj->category_id == $filter_cat)
                                    <option value="{{$catObj->category_id}}" selected>{{$catObj->name}}</option>
                                @else
                                    <option value="{{$catObj->category_id}}" >{{$catObj->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Lọc</button>
                {{ Form::close() }}
            </div>
        </div>
        <div class="widget-body">
            @if(count($listProduct) >0 )
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th>Tên thuộc tính</th>
                    <th>Trạng thái</th>
                    <th>Đổi trạng thái</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                    
                        <?php $stt = 1 ?>
                        @foreach($listProduct as $productObj)
                            <tr id="{{$productObj->product_id}}">
                                <td>{{$stt}}</td>
                                <td><b>{{$productObj->name}}</b></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <button type="button" class="btn btn-primary" id="btn_yes_all">Còn hàng</button>
                                    <button type="button" class="btn btn-defualt" id="btn_no_all">Hết hàng</button>
                                </td>
                                <td>
                                    <a href="{{ URL::route('editProduct', array('id'=>$productObj->product_id))}}" target="_blank" class="btn">Sửa</a>
                                </td>
                            </tr>
                            @foreach($productObj->option as $optionObj)
                            <tr id="{{$productObj->product_id.'_'.$optionObj->id}}" pid="{{$productObj->product_id}}">
                                <td></td>
                                <td></td>
                                <td>{{$optionObj->size}}</td>
                                <td id="v_status">
                                    @if($optionObj->enable == 1)
                                        <span style="color:blue">Còn hàng</span>
                                    @else
                                        <span style="color:gray">Hết hàng</span>
                                    @endif
                                </td>
                                <td id="v_btn_status">
                                    @if($optionObj->enable == 1)
                                        <button type="button" class="btn btn-defualt" id="btn_no" oid="{{$optionObj->id}}">Hết hàng</button>
                                    @else
                                        <button type="button" class="btn btn-primary" id="btn_yes" oid="{{$optionObj->id}}">Còn hàng</button>
                                    @endif
                                    
                                </td>
                                <td>
                                </td>
                            </tr>
                            @endforeach
                            <?php $stt++ ?>
                        @endforeach
                    
                    
                </tbody>
            </table>
            @else
                Không có sản phẩm nào
            @endif
            {{$listProduct->links();}}
        </div>
    </div>
@stop

@section('scripts')
    <script>
        var url_change = '{{URL::route('disableProduct')}}';
        $( document ).ready(function(){
            $('#btn_yes_all').click(function(){
                var pId = $(this).parents("tr").attr('id');
                $.get( url_change, { status: "1", pid: pId }, function( data ) {
                    data = jQuery.parseJSON(data);
                    if(data['err'] == 1){
                        alert('Có lỗi xãy ra, Ok để f5 trang');
                        location.reload();
                    }else{
                        if(data['all'] == 1 && data['status'] == 1){
                            $( 'tr[pid="'+data["pid"]+'"]' ).each(function( index ) {
                                var tempStatus = $(this).find('#v_status');
                                tempStatus.remove('span');
                                tempStatus.html('<span style="color:blue">Còn hàng</span>');
                                var tempBtnStatus = $(this).find('#v_btn_status');
                                var oId = tempBtnStatus.find('button').attr('oid');
                                tempBtnStatus.remove('button');
                                tempBtnStatus.html('<button type="button" class="btn btn-defualt" id="btn_no" oid="'+oId+'">Hết hàng</button>');
                            });

                        }else{
                            alert('Có lỗi xãy ra, Ok để f5 trang');
                            location.reload();
                        }
                    }
                });
            })

            $('#btn_no_all').click(function(){
                var pId = $(this).parents("tr").attr('id');
                $.get( url_change, { status: "0", pid: pId }, function( data ) {
                    data = jQuery.parseJSON(data);
                    if(data['err'] == 1){
                        alert('Có lỗi xãy ra, Ok để f5 trang');
                        location.reload();
                    }else{
                        if(data['all'] == 1 && data['status'] == 0){
                            $( 'tr[pid="'+data["pid"]+'"]' ).each(function( index ) {
                                var temp = $(this).find('#v_status');
                                temp.remove('span');
                                temp.html('<span style="color:gray">Hết hàng</span>');
                                var tempBtnStatus = $(this).find('#v_btn_status');
                                var oId = tempBtnStatus.find('button').attr('oid');
                                tempBtnStatus.remove('button');
                                tempBtnStatus.html('<button type="button" class="btn btn-primary" id="btn_yes" oid="'+oId+'">Còn hàng</button>');
                            });

                        }else{
                            alert('Có lỗi xãy ra, Ok để f5 trang');
                            location.reload();
                        }
                    }
                });
            })

            $('body').on('click', '#v_btn_status>button[id="btn_no"]', function(){
                var vId = $(this).attr('oid');
                $.get( url_change, { status: "0", vid: vId }, function( data ) {
                    data = jQuery.parseJSON(data);
                    if(data['err'] == 1){
                        alert('Có lỗi xãy ra, Ok để f5 trang');
                        location.reload();
                    }else{
                        if(data['all'] == 0 && data['status'] == 0){
                            var master = $('#'+data['pid']+'_'+data['vid']);
                            var temp = master.find('#v_status');
                            temp.remove('span');
                            temp.html('<span style="color:gray">Hết hàng</span>');
                            var tempBtnStatus = master.find('#v_btn_status');
                            var oId = tempBtnStatus.find('button').attr('oid');
                            tempBtnStatus.remove('button');
                            tempBtnStatus.html('<button type="button" class="btn btn-primary" id="btn_yes" oid="'+oId+'">Còn hàng</button>');

                        }else{
                            alert('Có lỗi xãy ra, Ok để f5 trang');
                            location.reload();
                        }
                    }
                });
            })

            $('body').on('click', '#v_btn_status>button[id="btn_yes"]', function(){
                var vId = $(this).attr('oid');
                $.get( url_change, { status: "1", vid: vId }, function( data ) {
                    data = jQuery.parseJSON(data);
                    if(data['err'] == 1){
                        alert('Có lỗi xãy ra, Ok để f5 trang');
                        location.reload();
                    }else{
                        if(data['all'] == 0 && data['status'] == 1){
                            var master = $('#'+data['pid']+'_'+data['vid']);
                            var tempStatus = master.find('#v_status');
                            tempStatus.remove('span');
                            tempStatus.html('<span style="color:blue">Còn hàng</span>');
                            var tempBtnStatus = master.find('#v_btn_status');
                            var oId = tempBtnStatus.find('button').attr('oid');
                            tempBtnStatus.remove('button');
                            tempBtnStatus.html('<button type="button" class="btn btn-defualt" id="btn_no" oid="'+oId+'">Hết hàng</button>');

                        }else{
                            alert('Có lỗi xãy ra, Ok để f5 trang');
                            location.reload();
                        }
                    }
                });
            })
        })
    </script>
@stop
