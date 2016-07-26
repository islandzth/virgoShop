<tr attr-product="{{ $product->product_name }}" attr-product-id="{{ $product->product_id }}">
    <td></td>
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
                    <a href="{{ Config::get('app.admin_url') }}product/warrantystatus/set?productid={{ $product->product_id }}&status={{ $status->product_warranty_status_id }}" class="btn btn-default btn-sm warranty-status-action-btn" attr-msg-required="1" >{{ $status->user_action_text }}</a>
                @else
                    <a href="{{ Config::get('app.admin_url') }}product/warrantystatus/set?productid={{ $product->product_id }}&status={{ $status->product_warranty_status_id }}" class="btn btn-default btn-sm warranty-status-action-btn">{{ $status->user_action_text }}</a>
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