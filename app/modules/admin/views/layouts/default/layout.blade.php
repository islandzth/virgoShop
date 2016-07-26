<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
				Laravel 4 Sample Site
			@show
		</title>
		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="_token" content="{{ csrf_token() }}" />

		<!-- CSS
		================================================== -->
        <link rel="stylesheet" href="{{asset('static/muadambao/components/bootstrap/css/bootstrap.css')}}">
        <link rel="stylesheet" href="{{asset('static/muadambao/css/common.css')}}">
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

		@yield('styles')
		<script type="text/Javascript">
			var ADMIN_URL="{{ Config::get('app.admin_url') }}";
		</script>
	</head>

	<body>
		@include('admin::layouts.default.header')
		<div class="container">
			<div id="page-content">
				<!-- Content -->
				@yield('content')
				<!-- ./ content -->
			</div>
			@include('admin::layouts.default.footer')
		</div>
		
		<!-- Javascripts
		================================================== -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="{{asset('static/muadambao/js/form-master/jquery.form.js')}}"></script>
        <script src="{{asset('static/muadambao/components/bootstrap/js/bootstrap.js')}}"></script>
        
	    <script src="{{asset('static/muadambao/js/form-master/jquery.form.js')}}"></script>
	    <script src="{{asset('static/muadambao/js/jquery.validate.min.js?v=2')}}"></script>
	    <script src="{{asset('static/muadambao/js/jquery.serializeJSON-master/jquery.serializejson.js')}}"></script>
	    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js"></script>
	    <script src="{{asset('static/virgo/js/jquery.number.min.js')}}"></script>
        <script>
        	$(function() {
			    $.ajaxSetup({
			        headers: {
			            'X-CSRF-Token': $('meta[name="_token"]').attr('content')
			        },
			        data:{
			        	_token: $('meta[name="_token"]').attr('content')
			        }
			    });
			});
			var CSRF_TOKEN = '<?=csrf_token()?>';
        </script>
        @yield('scripts')
	</body>
</html>