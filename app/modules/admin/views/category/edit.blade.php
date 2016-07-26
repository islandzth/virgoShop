@extends('admin::layouts.default.layout')

@section('content')
    <div class="row">
        <div class="col-md-6">
            @if (isset($msg))
                <div class="alert alert-danger" role="alert">{{ $msg }}</div>
            @endif
            {{ Form::open(array('url' => URL::route('editCategories', $category->category_id),'id'=>'createCat','class'=>'form-horizontal form-vertical no-padding no-margin')) }}
                <h4>Tạo danh mục</h4>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-4 col-md-4 control-label">Tên danh mục:</label>
                    <div class="col-sm-8 col-md-8">
                      <input type="text" name="nameCategory" class="form-control" id="inputEmail3" value="{{$category->name}}">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPassword3" class="col-sm-4 col-md-4 control-label">Giới tính:</label>
                    <div class="col-sm-8 col-md-8">
                        <label class="radio-inline">
                            @if($category->sex == 1)
                                <input type="radio" name="sexCategory" id="inlineRadio1" value="1" checked="true"> nam
                            @else
                                <input type="radio" name="sexCategory" id="inlineRadio1" value="1" > nam
                            @endif
                        </label>
                        <label class="radio-inline">
                            @if($category->sex == 0)
                                <input type="radio" name="sexCategory" id="inlineRadio1" value="0" checked="true"> nữ
                            @else
                                <input type="radio" name="sexCategory" id="inlineRadio1" value="0"> nữ
                            @endif
                        </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPassword3" class="col-sm-4 col-md-4 control-label">Ẩn danh mục:</label>
                    <div class="col-sm-8 col-md-8">
                        <label class="radio-inline">
                            @if($category->enable == 1)
                                <input type="radio" name="enableCategory" id="inlineRadio1" value="0" > có
                            @else
                                <input type="radio" name="enableCategory" id="inlineRadio1" value="0" checked="true"> có
                            @endif
                        </label>
                        <label class="radio-inline">
                            @if($category->enable == 1)
                                <input type="radio" name="enableCategory" id="inlineRadio1" value="0" > không
                            @else
                                <input type="radio" name="enableCategory" id="inlineRadio1" value="0" checked="true"> không
                            @endif
                        </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPassword3" class="col-sm-4 col-md-4 control-label">Mô tả:</label>
                    <div class="col-sm-8 col-md-8">
                        <textarea class="form-control" name="descriptionCat" rows="3" placeholder="hiển thị trong meta description (150 ki tu)">{{$category->discription}}</textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-default">Thay đổi</button>
                    </div>
                  </div>
            {{ Form::close() }}
        </div>
    </div>
@stop