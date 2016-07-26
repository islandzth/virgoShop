@extends('admin::layouts.default.layout')

@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Quản lý user admin
            </h4>
            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
            
        </div>
        <div class="widget-body">
            @if(count($listUser) >0 )
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Tên đăng nhập</th>
                    <th>Trạng thái</th>
                    <th>Đổi trạng thái</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($listUser as $userObj)
                        <tr>
                            <td>{{$userObj->username}}</td>
                            <td>
                                @if($userObj->lv == 1)
                                    <span style="color:blue">Đang hoạt động</span>
                                @else
                                    <span style="color:gray">Dừng hoạt động</span>
                                @endif
                            </td>
                            <td>
                                @if($userObj->lv == 1)
                                    <button type="button" class="btn btn-default">Đừng</button>
                                @else
                                    <button type="button" class="btn btn-primary">Kích hoạt</button>
                                @endif
                            </td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                Không có user nào
            @endif
        </div>
    </div>
@stop

@section('scripts')
    <script>
        var url_change = '{{URL::route('disableProduct')}}';
        $( document ).ready(function(){
            
        })
    </script>
@stop
