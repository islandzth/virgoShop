@extends('admin::layouts.default.layout')

@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Quản lý trạng thái sản phẩm
            </h4>
            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
        </div>
        <div class="widget-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#ID</th>
                    <th>Tên trạng thái(admin)</th>
                    <th>Tên hành động(admin)</th>
                    <th>Tên trạng thái(user)</th>
                    <th>Tên hành động(user)</th>
                    <th>Tên rút gọn</th>
                    <th>Hiện sản phẩm?</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($productStatus as $status)
                        <tr>
                            <td>{{ $status->product_status_id }}</td>
                            <td>{{ $status->admin_status_text }}</td>
                            <td>{{ $status->admin_action_text }}</td>
                            <td>{{ $status->user_status_text }}</td>
                            <td>{{ $status->user_action_text }}</td>
                            <td>{{ $status->identity }}</td>
                            <td>
                                @if ($status->is_show)
                                    Hiện
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                                <a href="{{ Config::get('app.admin_url').'productstatus/view?id='.$status->product_status_id }}">View</a>
                                |
                                <a href="{{ Config::get('app.admin_url').'productstatus/edit?id='.$status->product_status_id }}">Edit</a>
                                |
                                <a href="{{ Config::get('app.admin_url').'productstatus/delete?id='.$status->product_status_id }}">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
