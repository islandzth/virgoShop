@extends('admin::layouts.default.layout')

@section('content')
    <div class="row">
        <div class="col-md-4">
            @if (isset($msg))
                <div class="alert alert-danger" role="alert">{{ $msg }}</div>
            @endif
            {{ Form::open(array('url' => Config::get('app.admin_url').'createUser','id'=>'loginform','class'=>'form-vertical no-padding no-margin')) }}
                <h4>Tạo tài khoản</h4>
                <div class="form-group">
                    <label for="input-username">User name</label>
                    <input type="text" name="username" class="form-control" id="input-username" placeholder="User name">
                </div>
                <div class="form-group">
                    <label for="input-password">Password</label>
                    <input type="password" name="password" class="form-control" id="input-password" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-default">Login</button>
            {{ Form::close() }}
        </div>
    </div>
@stop