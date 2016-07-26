@extends('admin::layouts.default.layout')

@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Quản lý danh mục
            </h4>
            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
        </div>
        <div class="widget-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Tên danh mục</th>
                    <th>Tên định danh</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($listCat as $catObj)
                    <tr>
                        <td><a href="#">{{$catObj->name}}</a></td>
                        <td>{{$catObj->identity}}</td>
                        <td><a href="{{URL::route('editCategories', $catObj->category_id)}}">Sửa</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        function deleteCat(catId) {
            if (confirm('Có chắc muốn xóa danh mục này?')) {
            window.location = "{{ Config::get('app.admin_url') }}category/delete?id=" + catId;
            }
        }

        function updateOrder(catId, order) {
            $.get("{{ Config::get('app.admin_url') }}category/order", {'id': catId, 'order': order}, function (resp) {

            });
        }
    </script>
@stop
