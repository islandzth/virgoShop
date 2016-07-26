@extends('admin::layouts.default.layout')

@section('styles')
    <link rel="stylesheet" href="{{asset('static/muadambao/components/bootstrap-datepicker-release/css/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('static/muadambao/css/type_ahead.css')}}">
    <style>
        select[name=product-warranty-status]{
            margin-top: 10px;
        }
        .price-conds{
            list-style-type: disc;
            padding-left: 20px;
        }
        .variants-quantity{
            display: none;
        }
    </style>
@stop

@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Quản lý sản phẩm
            </h4>
            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
        </div>
        <div class="widget-body">
            {{ Form::open(array('url' => Config::get('app.admin_url').'products','method'=>'GET')) }}
                <div class="form-group">
                    <label for="ip-ncc">Nhà cung cấp</label>
                    @if (isset($shop))
                        <input type="text" class="form-control" id="ip-ncc" placeholder="Nhà cung cấp" name="ncc-text" value="{{ $shop->name }}">
                        <input type="hidden" name="shopid" id="ip-shopid" value="{{ $shop->id }}">
                    @else
                        <input type="text" class="form-control" id="ip-ncc" placeholder="Nhà cung cấp" name="ncc-text" value="">
                        <input type="hidden" name="shopid" id="ip-shopid">
                    @endif
                </div>
                <div class="form-group">
                    <label for="ip-keyword">Từ khóa</label>
                    <input type="text" class="form-control" id="ip-keyword" placeholder="Từ khóa" name="keyword" value="{{ $keyword }}">
                </div>
                <div class="form-group">
                    <label for="ip-status">Trạng thái</label>
                    <select id="ip-status" class="form-control" name="status">
                        <option value="">-- Chọn trạng thái --</option>
                        @foreach ($productStatus as $status)
                            @if ($status['product_status_id'] == $queryStatusId)
                                <option value="{{ $status['product_status_id'] }}" selected>{{ $status['admin_status_text'] }}</option>
                            @else
                                <option value="{{ $status['product_status_id'] }}">{{ $status['admin_status_text'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="ip-status">Trạng thái đảm bảo</label>
                    <select id="ip-status" class="form-control" name="warranty_status">
                        <option value="">-- Chọn trạng thái đảm bảo --</option>
                        @foreach ($productWarrantyStatus as $status)
                            @if ($status['product_warranty_status_id'] == $queryWarrantyStatusId)
                                <option value="{{ $status['product_warranty_status_id'] }}" selected>{{ $status['admin_status_text'] }}</option>
                            @else
                                <option value="{{ $status['product_warranty_status_id'] }}">{{ $status['admin_status_text'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <!-- <div class="form-group">
                    <label for="ip-from">Từ ngày</label>
                    <input type="text" class="form-control" id="ip-from" placeholder="Từ ngày" name="from" value="{{ $from }}">
                </div>
                <div class="form-group">
                    <label for="ip-to">Đến ngày</label>
                    <input type="text" class="form-control" id="ip-to" placeholder="Đến ngày" name="to" value="{{ $to }}">
                </div> -->
                <button type="submit" class="btn btn-default">Tìm kiếm</button>
            {{ Form::close() }}
            <table class="table table-hover" id="products-list">
                <thead>
                <tr>
                    <th>#STT</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Trạng thái</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($products as $i=>$product)
                        <tr attr-product="{{ $product->product_name }}" attr-product-id="{{ $product->product_id }}">
                            <td>{{ $i + 1 + $page*$rowsPerPage }}</td>
                            <td>
                                <img width="50px" src="{{ Config::get('app.upload_url') }}product_images/thumbs/100_{{ $product->product_image }}" />
                            </td>
                            <td class="product-name">
                                <p>
                                    <a href="{{ Config::get('app.url').StringUtils::rewriteUrl($product->product_name) . '-' . $product->product_id . '.html' }}">{{ $product->product_name }}</a>
                                </p>
                                <p>
                                    <span class="label label-default">ID: {{ $product->product_id }}</span>
                                    <span class="label label-default">SKU: {{ $product->sku }}</span>
                                    {{--
                                    @if ($product->productWarrantyStatus->is_warranty)
                                        <span class="label label-default">SKU: {{ $product->sku }}</span>
                                        @if (!$product->sku)
                                            <button class="btn btn-warning btn-xs set-sku-btn">Nhập SKU</button>
                                        @else
                                            <span class="label label-default">SKU: {{ $product->sku }}</span>
                                            <button class="btn btn-warning btn-xs set-sku-btn" attr-sku="{{ $product->sku }}">Thay đổi SKU</button>
                                        @endif
                                    @endif
                                    --}}
                                </p>
                                <p>
                                    <span class="label label-info">NCC: {{ $product->shop_name }}</span>
                                </p>
                                <ol class="breadcrumb">
                                    @foreach ($product->categories as $prdCat)
                                        <li><a href="#">{{ $prdCat->category_name }}</a></li>
                                    @endforeach
                                </ol>
                                <p>
                                    <span class="label label-primary">{{ $product->productWarrantyStatus->admin_status_text }}</span>
                                </p>
                                @if(count($product->productWarrantyStatus->statusChangeableAceptsForAdmin))
                                    @foreach ($product->productWarrantyStatus->statusChangeableAceptsForAdmin as $status)
                                        @if ($status->pivot->message_required)
                                            <a href="{{ Config::get('app.admin_url') }}product/warrantystatus/set?productid={{ $product->product_id }}&status={{ $status->product_warranty_status_id }}" class="btn btn-default btn-sm warranty-status-action-btn" attr-msg-required="1" >{{ $status->admin_action_text }}</a>
                                        @else
                                            <a href="{{ Config::get('app.admin_url') }}product/warrantystatus/set?productid={{ $product->product_id }}&status={{ $status->product_warranty_status_id }}" class="btn btn-default btn-sm warranty-status-action-btn">{{ $status->admin_action_text }}</a>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td class="product-status">
                                @if ($product->web_quantity == 0)
                                    <span class="label label-warning"><i class="fa fa-warning"></i> Cháy hàng</span>
                                @else
                                    @if ($product->productWarrantyStatus->is_warranty && $product->sku && $product->is_content_updated)
                                        <p>
                                            <span class="label label-primary">{{ $product->productStatus->admin_status_text }}</span>
                                        </p>
                                        @if(count($product->productStatus->statusChangeableAcepts))
                                            @foreach ($product->productStatus->statusChangeableAcepts as $status)
                                                @if ($status->pivot->message_required)
                                                    <a href="{{ Config::get('app.admin_url') }}product/status/set?productid={{ $product->product_id }}&status={{ $status->product_status_id }}" class="btn btn-default btn-sm status-action-btn" attr-msg-required="1" >{{ $status->admin_action_text }}</a>
                                                @else
                                                    <a href="{{ Config::get('app.admin_url') }}product/status/set?productid={{ $product->product_id }}&status={{ $status->product_status_id }}" class="btn btn-default btn-sm status-action-btn">{{ $status->admin_action_text }}</a>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif
                                @endif
                            </td>
                            <td>
                                <p>{{ number_format($product->product_price) }} đ</p>
                                @if (count($product->prices))
                                    <hr />
                                    <p>Giá theo số lượng {{ $product->productUnitStr }}</p>
                                    <ul class="price-conds">
                                        @foreach ($product->prices as $price)
                                            <li> > {{ $price->from_quantity }} => {{ number_format($price->price) }} đ</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                            <td>
                                @if (count($product->variants))
                                    <p> {{ $product->total_quantity.$product->productUnitStr }}<p>
                                    <button class="mg10 btn btn-white btn-small product-quantity-bt" data-rel="product-quantity-{{ $product->product_id }}">Chi tiết</button>
                                    <div class="variants-quantity" data-dest="product-quantity-{{ $product->product_id }}">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#STT</th>
                                                        <th>Tên biến thể</th>
                                                        <th>Số lượng</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                    @foreach ($product->variants as $index=>$variant)
                                                    <tr>
                                                        <td> {{ ($index+1) }}</td>
                                                        <td> {{ $variant->name }}</td>
                                                        <td> {{ ($variant->inventory_quantity?$variant->inventory_quantity:0).$product->productUnitStr }}</td>
                                                    </tr>
                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                @else
                                    {{ $product->inventory_quantity.$product->productUnitStr }}
                                @endif
                            </td>
                            <td>
                                <p>
                                    @if (!$product->productWarrantyStatus->is_warranty)
                                        <span class="label label-warning"><i class="fa fa-warning"></i> SP không phải là đảo bảo</span>
                                    @elseif (!$product->sku && !$product->is_content_updated)
                                        <span class="label label-warning"><i class="fa fa-warning"></i> Chưa nhập SKU và viết nội dung</span>
                                    @elseif (!$product->sku)
                                        <span class="label label-warning"><i class="fa fa-warning"></i> Chưa nhập SKU</span>
                                    @elseif (!$product->is_content_updated)
                                        <span class="label label-warning"><i class="fa fa-warning"></i> Chưa viết nội dung</span>
                                    @endif
                                </p>

                                @if ($product->productWarrantyStatus->is_warranty && $product->sku && $product->is_content_updated)
                                    <p>
                                        @if ($product->web_quantity == 0 || count($product->inProgressRequests))
                                            <p>
                                                @if ($product->web_quantity == 0)
                                                        <span class="label label-warning"><i class="fa fa-warning"></i> Cháy hàng</span>
                                                @endif
                                                @if (count($product->inProgressRequests))
                                                    <span class="label label-info"><i class="fa fa-info-circle"></i> Có {{ count($product->inProgressRequests) }} yêu cầu đang xử lý</span>
                                                @endif
                                            </p>
                                        @endif
                                        <button class="btn btn-default btn-sm load_product_request_btn"><i class="fa fa-paper-plane"></i> Gọi hàng NCC</button>
                                    </p>
                                @endif
                                
                                @if ($product->productWarrantyStatus->is_warranty)
                                    @if (!$product->is_content_updated)
                                        <a href="{{ Config::get('app.admin_url') }}product/edit/{{ $product->product_id }}?contentedited=1" class="btn btn-default btn-sm" ><i class="fa fa-edit"></i> Sửa</a>
                                    @else
                                        <a href="{{ Config::get('app.admin_url') }}product/edit/{{ $product->product_id }}" class="btn btn-default btn-sm" ><i class="fa fa-edit"></i> Sửa</a>
                                    @endif
                                    <a href="{{ Config::get('app.admin_url') }}product/delete?productid={{ $product->product_id }}" class="btn btn-default btn-sm delete-product-btn" attr-msg-required="1" ><i class="fa fa-remove"></i> Xóa</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div align="center"><?= $pagination ?></div>
        </div>
    </div>

    <div class="modal fade" id="confirm-msg-modal" style="z-index:100000;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Thông tin</h4>
                </div>
                <div class="modal-body">
                    <form action="#" id="confirm-msg-frm">
                        <div class="form-group">
                            <label for="input-message" class="control-label">Xin bạn cho biết lý do(Không bắt buộc):</label>
                            <textarea class="form-control" id="input-message" placeholder="message" name="message"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="sbm-confirm-msg-frm">Gửi</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Product warranty quantity Modal -->
    <div class="modal fade" id="productQuantityModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Chi tiết số lượng sản phẩm</h4>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->  



    <!-- Product warranty quantity Modal -->
    <div class="modal fade" id="productSkuInputModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">Nhập SKU cho sản phẩm</h5>
                </div>
                <div class="modal-body">
                    <h4 id="product-sku-frm-title"></h4>
                    <form action="#" id="product-sku-ip-frm">
                        <input name="product_id" type="hidden" value="">
                        <div class="form-group">
                            <label class="required">SKU</label>
                            <input type="text" name="sku" class="form-control" placeholder="SKU">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="save-sku">Lưu</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->  



    <!-- load product request Modal -->
    <div class="modal fade" id="load-product-request-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">Gọi hàng</h5>
                </div>
                <div class="modal-body">
                    {{ Form::open() }}
                        <div id="products-requested-wrp"></div>
                        <div id="load-product-request-wrp"></div>
                    {{ Form::close() }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="save-product-request">Lưu</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script type="text/template" id="product-requested-details-tpl">
        <% if(loadProductRequests.length){ %>
            <h4>Chọn yêu cầu nhập hàng(Cộng dồn)</h4>
            <% _.each(loadProductRequests,function(loadProductRequest){ %>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <input type="checkbox" name="load_product_request_choose" value="<%= loadProductRequest.load_product_request_id %>">
                        <span class="label label-default">ID: <%= loadProductRequest.load_product_request_id %></span>
                        <span class="label label-default">SKU: <%= loadProductRequest.sku %></span>
                        <span class="label label-info">Trạng thái: <%= loadProductRequest.status.admin_status_text %></span>
                        <span class="label label-info">Thời gian tạo: <%= moment(loadProductRequest.created_at).format('HH:mm DD/MM/YYYY') %></span>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tên</th>
                                    <th>Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <% if(loadProductRequest.variants.length){ %>
                                                <% _.each(loadProductRequest.variants,function(variant){ %>
                                                    <tr>
                                                        <td><%= variant.name %></td>
                                                        <td><%= variant.pivot.quantity_request + (product.productUnitStr?' ['+product.productUnitStr+']':'') %></td>
                                                    </tr>
                                                <% }); %>
                                <% }else{ %>
                                    <tr>
                                        <td><%= product.product_name %></td>
                                        <td><%= loadProductRequest.loadProductRequestProduct.quantity_request + (product.productUnitStr?' ['+product.productUnitStr+']':'') %></td>
                                    </tr>
                                <% } %>
                            </tbody>
                        </table>
                    </div>
                </div>
            <% }); %>
        <% } %>
    </script>

    <script type="text/template" id="load-product-request-content-tpl">
        <input type="hidden" name="id" value="<%= product_id %>">
        <div class="form-group">
            <label>Thông tin yêu cầu</label>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên</th>
                        <th>Số lượng</th>
                    <tr>
                </thead>
                <tbody>
                    <% if(variants.length){ %>
                        <% _.each(variants,function(variant,index){ %>
                            <tr>
                                <td><%= (index+1) %></td>
                                <td><%= variant.name %></td>
                                <td>
                                    <input attr-id="<%= variant.variant_id %>" type="text" name="quantity_request_<%= variant.variant_id %>" class="form-control" placeholder="Nhập số lượng yêu cầu">
                                </td>
                            </tr>
                        <% }); %>
                    <% }else{ %>
                        <tr>
                            <td>1</td>
                            <td><%= product_name %></td>
                            <td>
                                <input attr-id="<%= product_id %>" type="text" name="quantity_request_<%= product_id %>" class="form-control" placeholder="Nhập số lượng yêu cầu">
                            </td>
                        </tr>
                    <% } %>
                </tbody>
            </table>
        </div>
    </script>
@stop

@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.js"></script>

    <script src="{{asset('static/muadambao/js/form-master/jquery.form.js')}}"></script>
    <script src="{{asset('static/muadambao/js/jquery.validate.min.js?v=2')}}"></script>
    <script src="{{asset('static/muadambao/js/jquery.serializeJSON-master/jquery.serializejson.js')}}"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.js"></script>
    <script src="{{asset('static/muadambao/components/bootstrap-datepicker-release/js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('static/muadambao/components/typeahead.js-master/dist/typeahead.bundle.js')}}"></script>
    <script src="{{asset('static/muadambao/js/Utils.js')}}"></script>

    <script type="text/javascript">
        var products = {{ json_encode($products) }};

        // class for save show modal ask message
        function ActionMsg(){
            this.cb = null;
            this.show = function(cb){
                this.cb = cb;
                $("#confirm-msg-frm").resetForm();
                $("#confirm-msg-modal").modal();
            };

            this.close = function(){
                $("#confirm-msg-modal").modal('hide');
            };

            var $this = this;

            $("#confirm-msg-frm").submit(function(evt){
                evt.preventDefault();
                // get message
                var message = $(this).find("textarea").val();
                $this.cb(message);

                $this.close();
            });

            $("#sbm-confirm-msg-frm").click(function(evt){
                evt.preventDefault();

                $("#confirm-msg-frm").submit();
            });
        }

        var actionMsg = new ActionMsg();

        $(document).ready(function(){

            // add autocompleted for shop input
            var shopIpE = $('#ip-ncc');

            // add typeahead
            var bloodhoundData = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('signless'),
                queryTokenizer: function(query){
                    // convert query to signless
                    query = Utils.signless(query);
                    return Bloodhound.tokenizers.whitespace(query);
                },
                // prefetch: '../data/films/post_1960.json',
                remote: {
                    url:'{{ Config::get("app.admin_url")."ajax/products" }}?query=%QUERY',
                    ajax:{
                        beforeSend:function(){
                            shopIpE.addClass('typeahead-loading');
                        },
                        complete:function(){
                            shopIpE.removeClass('typeahead-loading');
                        }
                    }
                }
            });

            bloodhoundData.initialize();
            shopIpE.typeahead(
                {
                    minLength: 3,
                    highlight: true
                },{
                    source:bloodhoundData.ttAdapter(),
                    displayKey:'name',
                    templates:{
                        empty:'<div class="empty-msg">Không có kết quả</div>',
                        suggestion:_.template(
                            '<div class="clearfix">'+
                                '<%= name %>'+
                            '</div>')
                }
            }).bind('typeahead:selected',function(evt,selected){
                $('#ip-shopid').val(selected.value);
            }).change(function(evt){
                if($(this).val() == ''){
                    $('#ip-shopid').val('');
                }
            });


            $('#ip-from,#ip-to').datepicker({
                format: "dd/mm/yyyy"
            });

            

            $("#products-list").on('click','.warranty-status-action-btn',function(evt){
                evt.preventDefault();

                var link = $(this).attr('href');
                var messageRequired = $(this).attr('attr-msg-required');
                var trE = $(this).parents('tr');
                var productName = trE.attr('attr-product');
                var action = $(this).html();

                function done(msg){
                    if(msg){
                        link += '&message='+encodeURI(msg);
                    }
                    $.ajax({
                        url:link,
                        type:"get",
                        dataType:'json',
                        success:function(rps){
                            if(rps.errorMsg){
                                alert(rps.errorMsg);
                                return ;
                            }
                            alert('Thành công');

                            var newTrE = $(rps.html);
                            newTrE.find('td').first().html(trE.find('td').first().html());
                            trE.replaceWith(newTrE);
                        }
                    });
                }

                var msgConfirm = 'Bạn muốn "'+action+'" sản phẩm "'+productName+'" ?';
                
                if(confirm(msgConfirm)){
                    if($(this).attr('attr-msg-required')){
                            actionMsg.show(done);
                    }else{
                        done();
                        return ;
                    }
                }
            });

            // show detail product quantity
            $("#products-list").on('click','.product-quantity-bt',function(evt){
                evt.preventDefault();

                var rel = $(this).attr('data-rel');
                var content = $('[data-dest="'+rel+'"]').html();
                var modalE = $("#productQuantityModal");
                modalE.find(".modal-body").html(content);
                modalE.modal();
            });

             $("#products-list").on('click','.set-sku-btn',function(evt){
                evt.preventDefault();
                var trE = $(this).parents('tr');
                var productName = trE.attr('attr-product');
                var productId = trE.attr('attr-product-id');
                var sku = $(this).attr('attr-sku');

                var modalE = $("#productSkuInputModal");
                var title = 'Nhập SKU cho sản phẩm "'+productName+'"';
                if(sku){
                    title = 'Thay đổi SKU cho sản phẩm "'+productName+'"';
                }
                modalE.find("#product-sku-frm-title").html(title);
                if(sku){
                    modalE.find('input[name=sku]').val(sku);
                }
                modalE.find('input[name=product_id]').val(productId);
                // show modal
                modalE.modal();
            });

            // add event for submit sku input form
            $("#save-sku").click(function(evt){
                $("#product-sku-ip-frm").submit();
            });

            // add event for submit sku
            $("#product-sku-ip-frm").validate({
                rules: {
                    sku: {
                        required: true
                    }
                },
                submitHandler:function(){
                    // get form data
                    var formE = $("#product-sku-ip-frm");
                    var data = formE.serializeJSON();

                    var productId = data.product_id;

                    var trE = $("#products-list tr[attr-product-id="+productId+"]");
                    $.ajax({
                        url:"{{ Config::get('app.admin_url') }}product/sku/set",
                        data:data,
                        type:'GET',
                        dataType:'json',
                        success:function(rps){
                            if(rps.errorMsg){
                                alert(rps.errorMsg);
                                return ;
                            }
                            alert('Thành công');

                            var newTrE = $(rps.html);
                            newTrE.find('td').first().html(trE.find('td').first().html());
                            trE.replaceWith(newTrE);
                        }
                    });
                }
            });

            // add event for load product request
            var productRequestedDetailsTpl = $("#product-requested-details-tpl").html();
            var loadProductRequestContentTpl = $("#load-product-request-content-tpl").html();

            $("#load-product-request-modal form").submit(function(evt){
                evt.preventDefault();

                var formE = $("#load-product-request-modal form");
                // get data to post
                // get product
                var productId = formE.find('input[name=id]').val();

                // get load product request add to
                var addTo = formE.find('input[name=load_product_request_choose]:checked').val();
                // get quantity
                var quantityDetails = [];
                formE.find("input[name^=quantity_request]").each(function(){
                    var quantity = $(this).val();
                    var id = $(this).attr('attr-id');
                    if(quantity.trim()){
                        quantityDetails.push({
                            id:id,
                            quantity:quantity
                        });
                    }
                });

                var dataPost = {
                    id:productId,
                    addTo:addTo,
                    quantityDetails:quantityDetails
                }

                var modalE = $("#load-product-request-modal");
                if(quantityDetails.length){
                    $.ajax({
                        url:'{{ Config::get("app.admin_url") }}loadproductrequest/addproduct',
                        type:'POST',
                        dataType:'json',
                        data:dataPost,
                        success:function(rps){
                            if(rps.error){
                                alert(rps.msg);
                                return ;
                            }
                            alert('Thành công!');
                            if(rps.html){
                                trE = $("tr[attr-product-id="+productId+"]");
                                var newTrE = $(rps.html);
                                newTrE.find('td').first().html(trE.find('td').first().html());
                                trE.replaceWith(newTrE);
                            }
                            $("#load-product-request-modal").modal('hide');
                        }
                    });
                }else{
                    modalE.modal("hide");
                }
            });

            $("#save-product-request").click(function(evt){
                $("#load-product-request-modal form").submit();
            });

            $("#products-list").on('click','.load_product_request_btn',function(evt){
                evt.preventDefault();

                var trE = $(this).parents('tr');
                var productId = trE.attr('attr-product-id');

                // get product information
                var product = _.find(products,function(product){
                    return product.product_id == productId;
                });

                // load load product request relative with this product
                $.ajax({
                    url:"{{ Config::get('app.admin_url') }}product/loadproductrequest",
                    type:'GET',
                    data:{
                        id:productId
                    },
                    dataType:'json',
                    success:function(rps){
                        // build template
                        var productRequestedDetailsHtml = _.template(productRequestedDetailsTpl,rps);
                        var loadProductRequestContentHtml = _.template(loadProductRequestContentTpl,product);

                        var modalE = $("#load-product-request-modal");
                        modalE.find("#products-requested-wrp").html(productRequestedDetailsHtml);
                        modalE.find("#load-product-request-wrp").html(loadProductRequestContentHtml);
                        modalE.modal();
                    }
                });
            });
        });
    </script>
@stop