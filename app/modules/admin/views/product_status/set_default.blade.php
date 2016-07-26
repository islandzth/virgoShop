@extends('admin::layouts.default.layout')

@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Trạng thái mặc định của sản phẩm
            </h4>
            <h5>Trạng thái mặc định hiện tại</h5>
            <table class="table table-hover" id="rel-status-list">
                <thead>
                <tr>
                    <th>#ID</th>
                    <th>Tên Lựa chọn</th>
                    <th>Tên rút gọn</th>
                    <th>Giá trị</th>
                </tr>
                </thead>
                <tbody>
                    @if (isset($currentDefault))
                        <tr>
                            <td>{{ $currentDefault->product_status_id }}</td>
                            <td>{{ $currentDefault->admin_status_text }}</td>
                            <td>{{ $currentDefault->identity }}</td>
                            <td>{{ $currentDefault->value }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="3">
                                <span class="label label-warning">Chưa cài đặt trạng thái mặc định của sản phẩm</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            @if (count($all))
                @if (!empty($errs))
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            @foreach ($errs as  $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{ Form::open(array('id'=>'set-default-status-frm','class'=>'form-vertical no-padding no-margin')) }}
                    <div class="form-group">
                        <label for="input-value" class="required">Chọn trạng thái mặc định</label>
                        <select class="form-control" name="value" id="input-value">
                            @foreach ($all as $status)
                                <option value="{{ $status->product_status_id }}">{{ $status->admin_status_text }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-default">Lưu</button>
                {{ Form::close() }}
            @endif
        </div>
    </div>
@stop
