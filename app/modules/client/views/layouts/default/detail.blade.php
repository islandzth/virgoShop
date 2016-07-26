<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Basic Page Needs
		================================================== -->
		<meta charset="utf-8" />
		<title>
			@section('title')
				Virgo Boutique
			@show
		</title>
		<!-- Mobile Specific Metas
		================================================== -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="_token" content="{{ csrf_token() }}" />

		<!-- CSS
		================================================== -->
        <!-- <link rel="stylesheet" href="{{asset('static/virgo/css/bootstrap.css')}}"> -->
        <link href="{{asset('static/virgo/css/bootstrap.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('static/virgo/css/font-awesome.min.2.css')}}">
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('static/virgo/css/styleDetails.css')}}">
        <link rel="stylesheet" href="{{asset('static/virgo/css/styles.css')}}">

		@yield('styles')
	</head>

	<body>
		<div class="container-fluid">
			@include('client::layouts.default.header')

			<div class="container-fluid vr-center-container">
				<div class="row-fluid row-offcanvas row-offcanvas-left">
					@include('client::layouts.default.category')
					@yield('content')
				</div><!--/.row row-offcanvans-->
			</div><!--/.container-->  
			@include('client::layouts.default.footer')
		</div>


		
		<!-- Javascripts
		================================================== -->
	    <script src="{{asset('static/virgo/js/bootstrap.min.js')}}"></script>
	    <script src="{{asset('static/virgo/js/scripts.js')}}"></script>
        <script>
        	
        </script>
        @yield('scripts')
	</body>
</html>