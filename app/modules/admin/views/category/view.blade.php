@extends('admin::layouts.default.layout')

@section('scripts')
    <script type="text/javascript">
        var id = {{ $category->category_id }};

        $(document).ready(function(){
        	// add event for add option button
        	$("#add-attr-category").click(function(evt){
        		evt.preventDefault();
        		// load attributes of category
        		$.ajax({
        			url:"{{ Config::get('app.admin_url').'category/attribute/remains' }}",
        			dataType:'json',
        			data:{
        				id:id
        			},
        			success:function(rpsData){
        				// add attributes
        				$("select[name=attribute_id]").empty();
        				for(var i=0;i<rpsData.attrs.length;i++){
        					$("select[name=attribute_id]").append('<option value="'+rpsData.attrs[i]['attribute_id']+'">'+rpsData.attrs[i]['name']+'</option>');
        				}

    		    		// reset add attribute form
    		    		$("#add-attr-frm").resetForm();

    		    		// hide error
    		    		$("#add-errors").hide();

    		    		// show modal
    		    		$("#add-attr-modal").modal();
        			}
        		});
        	});

        	// add event for submit add attribute form
        	$("#add-attr-frm").submit(function(evt){
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

        	$("#save-attr").click(function(evt){
        		evt.preventDefault();
        		$("#add-attr-frm").submit();
        	});


        	// add class for detach attribute for category
        	$(".detach-attr").click(function(evt){
        		evt.preventDefault();

        		var url = $(this).attr('href');
        		// show confirm
        		if(confirm("Chắc chắn muốn xóa thuộc tính khỏi danh mục ?")){
        			$.ajax({
        				url:url,
        				success:function(rpsData){
        					if(rpsData.errs){
        						return alert(rpsData.errs[0]);
        					}
        					alert('Xóa thành công');
        					location.reload();
        				}
        			})
        		}
        	});
        });
    </script>
@stop
@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Quản lý danh mục {{ $category->category_name }}
            </h4>
            <h5>Thuộc tính danh mục</h5>
            <table class="table table-hover">
                <thead>
	                <tr>
	                    <th>Tên thuộc tính</th>
                        <th>Tên rút gọn</th>
	                    <th>Required?</th>
	                    <th>Hành động</th>
	                </tr>
                </thead>
                <tbody>
                	@foreach ($category->attributes as $attribute)
                		<tr>
                			<td>{{ $attribute->name }}</td>
                            <td>{{ $attribute->identity }}</td>
                			<td>
                                @if (Category::checkRequired($category->category_id,$attribute->attribute_id))
                                    Required
                                @else
                                    ---
                                @endif         
                            </td>
                			<td>
                                <a class="detach-attr" href="{{ Config::get('app.admin_url').'category/attribute/delete?catid='.$category->category_id.'&attrid='.$attribute->attribute_id }}">Delete</a>
                			</td>
                		</tr>
                	@endforeach
                </tbody>
            </table>
            <button id="add-attr-category" type="button" class="btn btn-default">Thêm thuộc tính</button>
        </div>
    </div>

    <div class="modal fade" id="add-attr-modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<h4 class="modal-title">Thêm thuộc tính</h4>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger" role="alert" id="add-errors">
						<ul>
						</ul>
					</div>
					{{ Form::open(array('url'=>Config::get('app.admin_url').'category/attribute','id'=>'add-attr-frm','class'=>'form-vertical no-padding no-margin')) }}
						<input type="hidden" name="category_id" value="{{ $category->category_id }}">
		                <div class="form-group">
		                    <label for="input-value" class="required">Thuộc tính</label>
		                    <select name="attribute_id">
		                    </select>
		                </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="required" value="1"> Phải có khi đặt đơn hàng ?
                                </label>
                            </div>
                        </div>
		            {{ Form::close() }}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
					<button type="button" class="btn btn-primary" id="save-attr">Lưu</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
@stop
