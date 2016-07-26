<!-- Docs master nav -->
<header class="navbar navbar-static-top bs-docs-nav" id="top" role="banner">
  <div class="container">
    <nav class="navbar navbar-default" role="navigation">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{ Config::get('app.admin_url') }}">Admin</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Danh mục<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{URL::route('manageCategories')}}">Quản lý</a></li>
                <li class="divider"></li>
                <li><a href="{{URL::route('createCategories')}}">Tạo mới</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Sản phẩm<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{URL::route('manageProduct')}}">Quản lý</a></li>
                <li class="divider"></li>
                <li><a href="{{URL::route('createProduct')}}">Tạo mới</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Quản lý user<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ Config::get('app.admin_url') }}loadproductrequests">Danh sách</a></li>
                <!-- <li><a href="{{ Config::get('app.admin_url') }}loadproductrequeststatus/default">/a></li> -->
              </ul>
            </li>
            
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Đơn hàng<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="{{ URL::route('orderManage') }} ">Danh sách</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin<span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <!-- <li><a href="{{ Config::get('app.admin_url') }}web-config">Cấu hình</a></li> -->
                <li><a href="{{ URL::route('manageAdminUser') }}">Quản lý</a></li>
                <li><a href="{{ URL::route('regUser') }}">Tạo user admin</a></li>
              </ul>
            </li>
          </ul>
        </div>
      <!-- /.container-fluid -->
      </nav>
  </div>
</header>