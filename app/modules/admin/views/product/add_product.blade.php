@extends('admin::layouts.default.layout')

@section('content')
    <div class="row">
        <div class="col-md-6">
            @if (isset($msg))
                <div class="alert alert-danger" role="alert">{{ $msg }}</div>
            @endif
            {{ Form::open(array('url' => URL::route('createProduct'),'id'=>'createCat','class'=>'form-horizontal form-vertical no-padding no-margin')) }}
                <h4>Tạo sản phẩm</h4>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-4 col-md-4 control-label">Tên sản phẩm:</label>
                    <div class="col-sm-8 col-md-8">
                      <input type="text" name="nameProduct" class="form-control" id="inputEmail3" placeholder="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-sm-4 col-md-4 control-label">Category:</label>
                    <div class="col-sm-8 col-md-8">
                      <select class="form-control" name="catProduct">
                        @foreach($listCat as $catObj)
                          <option value="{{$catObj->category_id}}">{{$catObj->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-sm-4 col-md-4 control-label">Màu sắc:</label>
                    <div class="col-sm-3 col-md-3">
                      <input type="text" name="colorProduct[]" class="form-control" id="inputEmail3" placeholder="">
                    </div>
                    <label for="inputEmail3" class="col-sm-1 col-md-1 control-label">Giá:</label>
                    <div class="col-sm-3 col-md-3">
                      <input type="text" name="priceProduct[]" class="form-control" id="inputEmail3" placeholder="">
                    </div>
                  </div>
                  <div class="form-group" id="btn_extra_color">
                    <div class="col-sm-5 col-md-5"></div>
                    <a href="javascript:void(0)">Thêm màu sắc</a>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-sm-4 col-md-4 control-label">keyword:</label>
                    <div class="col-sm-8 col-md-8">
                      <input type="text" name="keyWordProduct" class="form-control" id="inputEmail3" placeholder="">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPassword3" class="col-sm-4 col-md-4 control-label">Mô tả:</label>
                    <div class="col-sm-8 col-md-8">
                        <textarea class="form-control" name="descriptionProduct" rows="3" placeholder="hiển thị trong meta description (150 ki tu)"></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-sm-4 col-md-4 control-label">up ảnh:</label>
                    <div class="col-sm-8 col-md-8">
                      <div id="container_image">
                        <div class="row" style="    padding-left: 15px;">

                          <button type="button" id="btn_upload_image" class="btn btn-primary">Đăng ảnh</button>
                          <div id="progress" style="display:none; width:100%; background:#EBEBEB; border:1px solid gray;height: 20px;margin-top: 5px;margin-bottom: 5px;">
                            <div id="bar" style="height:100%; background:#f60"></div>
                          </div>
                        </div>
                        
                        <div class="row" id="show_image" style="padding:10px 0px">
                          
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-default">Tạo mới</button>
                    </div>
                  </div>
            {{ Form::close() }}

            {{ Form::open(array('url' => URL::route('ajaxUploadFileImage'),'id'=>'formImage', 'enctype'=>'multipart/form-data', 'method' => 'post')) }}
              <input type="file" id="input_file" name="files[]" multiple style="display:none" accept="image/*" />
            {{Form::close()}}
        </div>
    </div>
@stop

@section('scripts')
<script type="text/javascript">
  var url_image = '{{Config::get('app.static_image_product_url')}}'; 
  
  $( document ).ready(function() {
    
    
    $('#btn_upload_image').click(function(){
      $('#input_file').click();
    })

    $('#input_file').change(function(){
      $('#btn_upload_image').attr("disabled", "disabled");
      $('#progress').css("display", "block");
      var bar = $('#bar');
      var progress = $('#progress');
      var show_image = $('#show_image');
      $('#formImage').ajaxSubmit({ 
          beforeSend: function () {
                //status.empty();
                var percentVal = '0%';
                bar.width(percentVal);
            },
            uploadProgress: function (event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                bar.width(percentVal);
            },
            success: function () {
                var percentVal = '100%';
                bar.width(percentVal);
            },
            complete: function (xhr) {
                //status.html(xhr.responseText);
                var kq = xhr.responseText;
                kq = jQuery.parseJSON(kq);
                if(kq['err'] == 1){
                  alert(kq['msg']);
                }else{
                  for(var i = 0; i < kq['arrName'].length; i++){
                    show_image.append('<div class="col-sm-12 col-md-12" id="show_detail_img" style="border: 1px solid #ebebeb;margin-bottom: 10px;">'+
                            '<img src="'+url_image+kq['arrName'][i]+'"/ style="width:100%">'+
                            '<input type="hidden" value="'+kq['arrName'][i]+'" name="name_image[]" />'+
                            '<input type="radio" value="'+kq['arrName'][i]+'" name="set_defualt_image" checked>Chọn ảnh đại diện'+
                            '<span class="glyphicon glyphicon-remove" id="remove_img" style="color: red;float: right;cursor: pointer;font-size: 17px;"></span>'+
                          '</div>');
                  }
                }
                $('#btn_upload_image').removeAttr("disabled");
                bar.width('0%')
                $('#progress').css("display", "none");
            }
      }); 
    });
    $('#btn_extra_color a').click(function(){

      $( "#btn_extra_color" ).before('<div class="form-group">'+
                    '<label for="inputEmail3" class="col-sm-4 col-md-4 control-label">Màu sắc:</label>'+
                    '<div class="col-sm-3 col-md-3">'+
                      '<input type="text" name="colorProduct[]" class="form-control" id="inputEmail3" placeholder="">'+
                    '</div>'+
                    '<label for="inputEmail3" class="col-sm-1 col-md-1 control-label">Giá:</label>'+
                    '<div class="col-sm-3 col-md-3">'+
                      '<input type="text" name="priceProduct[]" class="form-control" id="inputEmail3" placeholder="">'+
                    '</div>'+
                    '<span class="col-sm-1 col-md-1 glyphicon glyphicon-remove" id="remove_option" style="color:red; cursor:pointer"></span>'+
                  '</div>');
    })
    $('body').on( 'click', 'span[id="remove_option"]', function(){
      $(this).parent( ".form-group" ).remove();
    })
    $('body').on('click', 'span[id="remove_img"]', function(){
      $(this).parent('div[id="show_detail_img"]').remove();
    })
  });
function showResponse(responseText, statusText, xhr, $form){
    console.log(responseText);
  }
</script>
@stop