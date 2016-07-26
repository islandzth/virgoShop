@extends('admin::layouts.default.layout')

@section('scripts')
    <script type="text/template" id="web-config-row-tpl">
        <tr attr-id="<%= web_config_id %>">
            <td><%= name %></td>
            <td><%= identity %></td>
            <td><%= value %></td>
            <td>
                <a href="{{ Config::get('app.admin_url') }}web_config/edit?id=<%= web_config_id %>" class="edit-web-config">Edit</a>
                |
                <a href="{{ Config::get('app.admin_url') }}web_config/remove?id=<%= web_config_id %>" class="remove-web-config">Remove</a>
            </td>
        </tr>
    </script>
    <script type="text/javascript">

        function resetModal(){
            // reset form
            $("#web-config-frm").resetForm();
            // hide error
            $("#form-errors").hide().find("ul").empty();
        }
        
        $(document).ready(function(){

            var webConfigEditing = null;

            var webConfigRowTpl = $('#web-config-row-tpl').html();

            var webConfigs = {{ $webConfigs }};
            // add validate form
            $("#web-config-frm").validate({
                rules: {
                    name: {
                        required: true
                    },
                    value: {
                        required: true
                    }
                },
                highlight: function (element) {
                    $(element).closest('.form-group').removeClass('success').addClass('error');
                },
                success: function (element) {
                    element
                        .text('OK!').addClass('valid')
                        .closest('.form-group').removeClass('error').addClass('success');
                },
                submitHandler:function(){
                    // get form data
                    var formE = $("#web-config-frm");
                    var data = formE.serializeJSON();

                    var url = '{{ Config::get('app.admin_url') }}web-config/add';
                    if(action == 'edit'){
                        url = '{{ Config::get('app.admin_url') }}web-config/edit';

                        data.id = webConfigEditing.web_config_id;
                    }
                    $.ajax({
                        url:url,
                        data:data,
                        type:'POST',
                        dataType:'json',
                        success:function(rps){
                            if(rps.errors){
                                var errorsWrpE = $("#form-errors");
                                var errorsUlE = errorsWrpE.find('ul');
                                for(var i=0;i<rps.errors.length;i++){
                                    errorsUlE.append('<li>'+rps.errors[i]+'</li>');
                                }
                                errorsWrpE.show();
                            }else{
                                var webConfig = rps.webConfig;
                                // reset webConfigs
                                webConfigs = rps.webConfigs;
                                if(action == 'add'){
                                    $("#web-config-list tbody").append(_.template(webConfigRowTpl,webConfig));
                                }else{
                                    // get current config row
                                    $("#web-config-list tbody").find('tr[attr-id='+webConfigEditing.web_config_id+']').replaceWith(_.template(webConfigRowTpl,webConfig));
                                }

                                // hide modal
                                $("#web-config-modal").modal('hide');
                            }
                        }
                    });
                }
            });
    
            // add web config
            $("#add-web-config").click(function(evt){
                evt.preventDefault();

                // reset modal
                resetModal();

                // show modal
                $("#web-config-modal").modal().find('.modal-title').html('Thêm cấu hình');

                action = 'add';
            });

            // edit web config
            $("#web-config-list").on('click','.edit-web-config',function(evt){
                evt.preventDefault();

                var webConfigId = $(this).parents('tr').attr('attr-id');
                // get web config
                webConfigEditing = _.find(webConfigs,function(webConfig){
                    return webConfig.web_config_id == webConfigId;
                });

                // reset modal
                resetModal();

                // add value for form
                var formE = $("#web-config-frm");
                formE.find('input[name=name]').val(webConfigEditing.name);
                formE.find('input[name=identity]').val(webConfigEditing.identity);
                formE.find('input[name=value]').val(webConfigEditing.value);

                // show modal
                $("#web-config-modal").modal().find('.modal-title').html('Thay đổi cấu hình');

                action = 'edit';
            });

            $("#save-web-config").click(function(evt){
                evt.preventDefault();
                $("#web-config-frm").submit();
            });

            $(".remove-web-config").click(function(evt){
                evt.preventDefault();

                var trE = $(this).parents('tr');

                var webConfigId = trE.attr('attr-id');
                var webConfig = _.find(webConfigs,function(webConfig){
                    return webConfig.web_config_id == webConfigId;
                });
                var link = $(this).attr('href');
                if(confirm('Bạn chắc chắn muốn xóa cấu hình "'+webConfig.name+'"')){
                    $.ajax({
                        url:link,
                        success:function(rps){
                            // re
                            webConfigs = rps.webConfigs;

                            trE.remove();
                        }
                    });
                }
            });
        });
    </script>
@stop

@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Quản lý cấu hình
            </h4>
            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
        </div>
        <div class="widget-body">
            <table class="table table-hover" id="web-config-list">
                <thead>
                <tr>
                    <th>Tên cấu hình</th>
                    <th>Tên rút gọn</th>
                    <th>Giá trị</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($webConfigs as $webConfig)
                        <tr attr-id="{{ $webConfig->web_config_id }}">
                            <td>{{ $webConfig->name }}</td>
                            <td>{{ $webConfig->identity }}</td>
                            <td>{{ $webConfig->value }}</td>
                            <td>
                                <a href="{{ Config::get('app.admin_url').'web_config/edit?id='.$webConfig->web_config_id }}" class="edit-web-config">Edit</a>
                                |
                                <a href="{{ Config::get('app.admin_url').'web_config/remove?id='.$webConfig->web_config_id }}" class="remove-web-config">Remove</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button id="add-web-config" type="button" class="btn btn-default">Thêm cấu hình</button>
        </div>
    </div>

    <div class="modal fade" id="web-config-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Thêm cấu hình</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert" id="form-errors">
                        <ul>
                        </ul>
                    </div>
                    {{ Form::open(array('url'=>Config::get('app.admin_url').'web-config/add','id'=>'web-config-frm','class'=>'form-vertical no-padding no-margin')) }}
                        <div class="form-group">
                            <label for="input-value" class="required">Tên cấu hình</label>
                            <input type="text" name="name" class="form-control" id="input-name" placeholder="name">
                        </div>
                        <div class="form-group">
                            <label for="input-value">Tên rút gọn</label>
                            <input type="text" name="identity" class="form-control" id="input-identity" placeholder="identity">
                        </div>
                        <div class="form-group">
                            <label for="input-value" class="required">Giá trị</label>
                            <input type="text" name="value" class="form-control" id="input-value" placeholder="value">
                        </div>
                    {{ Form::close() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="save-web-config">Lưu</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop
