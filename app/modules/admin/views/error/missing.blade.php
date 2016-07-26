@extends('admin::layouts.default.layout')
@section('content')
    <div class="widget">
        <div class="widget-title">
            <h4>
                <i class="icon-reorder"></i>Không tìm thấy trang
            </h4>
            <span class="tools"> <a href="javascript:;" class="icon-chevron-down"></a> </span>
        </div>
        <div class="widget-body">
            @if (Input::get('msg'))
                {{ Input::get('msg') }}
            @endif
        </div>
    </div> 
@stop