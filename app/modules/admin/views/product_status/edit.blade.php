@extends('admin::layouts.default.layout')

@section('content')
    <div class="row">
        <div class="col-md-6">
            @if (!empty($errs))
                <div class="alert alert-danger" role="alert">
                	<ul>
	                	@foreach ($errs as  $error)
	                		<li>{{ $error }}</li>
	                	@endforeach
                	</ul>
                </div>
            @endif
            {{ Form::open(array('url'=>Config::get('app.admin_url').'productstatus/edit?id='.$productStatus['product_status_id'],'class'=>'form-vertical no-padding no-margin')) }}
                <h4>Tạo thuộc tính</h4>
                <div class="form-group">
                    <label for="input-admin-status-text" class="required">Tên trạng thái(admin)</label>
                    <input type="text" name="admin_status_text" class="form-control" id="input-admin-status-text" placeholder="Name" value="{{ $productStatus['admin_status_text'] }}">
                </div>
                <div class="form-group">
                    <label for="input-admin-action-text" class="required">Tên hành động(admin)</label>
                    <input type="text" name="admin_action_text" class="form-control" id="input-admin-action-text" placeholder="Name" value="{{ $productStatus['admin_action_text'] }}">
                </div>
                <div class="form-group">
                    <label for="input-user-status-text" class="required">Tên trạng thái(user)</label>
                    <input type="text" name="user_status_text" class="form-control" id="input-user-status-text" placeholder="Name" value="{{ $productStatus['user_status_text'] }}">
                </div>
                <div class="form-group">
                    <label for="input-user-action-text" class="required">Tên hành động(user)</label>
                    <input type="text" name="user_action_text" class="form-control" id="input-user-action-text" placeholder="Name" value="{{ $productStatus['user_action_text'] }}">
                </div>
                <div class="form-group">
                    <label for="input-identity">Tên rút gọn</label>
                    <input type="text" name="identity" class="form-control" id="input-identity" placeholder="Identity" value="{{ $productStatus['identity'] }}">
                </div>
                <div class="checkbox">
                    <label>
                        @if ($productStatus['is_show'])
                            <input type="checkbox" name="is_show" value="1" checked> Hiện sản phẩm ?
                        @else
                            <input type="checkbox" name="is_show" value="1"> Hiện sản phẩm ?
                        @endif
                    </label>
                </div>
                <button type="submit" class="btn btn-default">Lưu thay đổi</button>
            {{ Form::close() }}
        </div>
    </div>
@stop
