@extends('admin::layouts.default.layout')

@section('scripts')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>
    <script type="text/javascript">
        var id = {{ $productStatus->product_status_id }};
        var relIds = {{ json_encode($relIds) }};

        var allWarrantyStatus = {{ json_encode($allWarrantyStatus) }};

        function filterRemain(){
            return _.filter(allWarrantyStatus,function(status){
                return _.indexOf(relIds,status.product_status_id) == -1 && status.product_status_id != id;
            });
        }

        $(document).ready(function(){
        	// add event for add option button
        	$("#add-rel-status").click(function(evt){
        		evt.preventDefault();

                // get remain status(doesn't relative with current status)
                var remainStatus = filterRemain();

        		// reset add option form
        		$("#add-rel-status-frm").resetForm();
        		// hide error
        		$("#add-errors").hide();

                // remove old results
                var relStatusE = $("#rel-status");
                relStatusE.empty();

                // relStatusE.append('<option value="">-- Chọn trạng thái cần thêm --</option>');
                _.forEach(remainStatus,function(status){
                    relStatusE.append('<option value="'+status.product_status_id+'">'+status.admin_status_text+'</option>');
                });

        		// show modal
        		$("#add-rel-status-modal").modal();
        	});

        	// add event for submit add option form
        	$("#add-rel-status-frm").submit(function(evt){
        		evt.preventDefault();

        		$(this).ajaxSubmit({
        			dataType:'json',
        			success:function(rpsData){
        				if(rpsData.errs){
        					// show errors
        					$("#add-errors ul").empty();
        					for(var i=0;i<rpsData.errs.length;i++){
        						$("#add-errors ul").append('<li>'+rpsData.errs[i]+'</li>');
        					}
        					$("#add-errors").show();
        				}else{
        					location.reload();
        				}
        			}
        		});

        		return false;
        	});

        	$("#save-rel-status").click(function(evt){
        		evt.preventDefault();
        		$("#add-rel-status-frm").submit();
        	});

            $("#add-all-status").click(function(evt){
                evt.preventDefault();

                // get remain status
                var remainStatus = filterRemain();

                var remainStatusIds = _.map(remainStatus,function(status){
                    return status.product_status_id;
                });
                // submit all remain to server
                $.ajax({
                    url:ADMIN_URL+'productstatus/add-rels',
                    type:'POST',
                    data:{
                        id:id,
                        'rel-status':remainStatusIds
                    },
                    success:function(rps){
                        window.location.reload();
                    }
                });
            });

            $("#rel-status-list").on('click','.remove-rel-status',function(evt){
                evt.preventDefault();

                var link = $(this).attr('href');
                var statusRemovingName = $(this).attr('attr-status-name');
                if(confirm('Bạn chắc chắn muốn hủy liên kết với "'+statusRemovingName+'" ?')){
                    $.ajax({
                        url:link,
                        success:function(rps){
                            window.location.reload();
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
                <i class="icon-reorder"></i>Quản lý trạng thái sản phẩm đảm bảo "{{ $productStatus->admin_status_text }}"
            </h4>
            <h5>Trạng thái sản phẩm đảm bảo có thể chuyển đổi</h5>
            <table class="table table-hover" id="rel-status-list">
                <thead>
                <tr>
                    <th>Tên Lựa chọn</th>
                    <th>Yêu cầu nhập tin nhắn</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($productStatus->statusChangeableAcepts as $status)
                        <tr>
                            <td>{{ $status->admin_status_text }}</td>
                            <td>
                                @if ($status->pivot->message_required)
                                    Có
                                @else
                                    Không
                                @endif
                            </td>
                            <td>
                                <a href="{{ Config::get('app.admin_url').'productstatus/remove-rel?id='.$productStatus->product_status_id.'&rel-status='.$status->product_status_id }}" class="remove-rel-status" attr-status-name="{{ $status->admin_status_text }}">Xóa</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ((count($allWarrantyStatus) - 1) != count($relIds))
                <button id="add-rel-status" type="button" class="btn btn-default">Thêm trạng thái sản phẩm đảm bảo có thể chuyển đổi</button>
                <button id="add-all-status" type="button" class="btn btn-link">Tất cả</button>
            @endif
        </div>
    </div>

    @if ((count($allWarrantyStatus) - 1) != count($relIds))
    	<div class="modal fade" id="add-rel-status-modal">
    		<div class="modal-dialog">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    					<h4 class="modal-title">Thêm trạng thái sản phẩm đảm bảo có thể chuyển đổi</h4>
    				</div>
    				<div class="modal-body">
    					<div class="alert alert-danger" role="alert" id="add-errors">
    						<ul>
    						</ul>
    					</div>
    					{{ Form::open(array('url'=>Config::get('app.admin_url').'productstatus/add-rel','id'=>'add-rel-status-frm','class'=>'form-vertical no-padding no-margin')) }}
                            <input type="hidden" name="id" value="{{ $productStatus->product_status_id }}">
    		                <div class="form-group">
    		                    <label for="input-value" class="required">Chọn trạng thái cần thêm</label>
    		                    <select class="form-control" name="rel-status" id="rel-status"></select>
    		                </div>
                            <div class="checkbox" id="ip-message-required">
                                <label>
                                    <input type="checkbox" name="message_required" value="1"> Yêu cầu nhập tin nhắn khi chuyển đổi ?
                                </label>
                            </div>
    		            {{ Form::close() }}
    				</div>
    				<div class="modal-footer">
    					<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
    					<button type="button" class="btn btn-primary" id="save-rel-status">Lưu</button>
    				</div>
    			</div><!-- /.modal-content -->
    		</div><!-- /.modal-dialog -->
    	</div><!-- /.modal -->
    @endif
@stop
