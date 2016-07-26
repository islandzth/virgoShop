@extends('admin::layouts.default.layout')

@section('content')
    <div class="row">
        <div class="col-md-6">
            @if (isset($msg))
                <div class="alert alert-danger" role="alert">{{ $msg }}</div>
            @endif
            {{ Form::open(array('url' => URL::route('createCategories'),'id'=>'createCat','class'=>'form-horizontal form-vertical no-padding no-margin')) }}
                <h4>Tạo danh mục</h4>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-4 col-md-4 control-label">Tên danh mục:</label>
                    <div class="col-sm-8 col-md-8">
                      <input type="text" name="nameCategory" class="form-control" id="inputEmail3" placeholder="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPassword3" class="col-sm-4 col-md-4 control-label">Giới tính:</label>
                    <div class="col-sm-8 col-md-8">
                        <label class="radio-inline">
                            <input type="radio" name="sexCategory" id="inlineRadio1" value="1"> nam
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="sexCategory" id="inlineRadio2" value="0" checked="true"> nữ
                        </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPassword3" class="col-sm-4 col-md-4 control-label">Mô tả:</label>
                    <div class="col-sm-8 col-md-8">
                        <textarea class="form-control" name="descriptionCat" rows="3" placeholder="hiển thị trong meta description (150 ki tu)"></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-default">Khởi tạo</button>
                    </div>
                  </div>
            {{ Form::close() }}
        </div>
    </div>
@stop