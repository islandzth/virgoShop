<!-- top navbar -->
    <nav class="span2 navbar navbar-fixed-top vr-top">
    <div class="container-fluid">
        <div class="row-fluid">
          <div class="col-sm-6">
            <div class="row">
              <div class="col-sm-4">
                <a class="navbar-brand" href="{{URL::route('index')}}"><img src="/static/uploads/images/LOGO VIRGO.svg" class="img-responsive"></a>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
          <div class="pull-right" id="vr-nav-top"> 
                <ul class="nav navbar-nav">
                    <li><a href="#"><i>CONTACT</i></a></li>
                    <li><a href="#"><i>MY ACCOUNT</i></a></li>
                    <li><a href="{{URL::route('viewCart')}}"><i class="fa fa-cart-plus" style="font-size: 20px;"></i></a></li>
                </ul>
          </div>
          
          </div>
        </div>
          <div class="row-fluid vr-search pull-right">
            <!--<form class="navbar-form navbar-left" role="search">
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Search">
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
            </form>-->
          </div>

    </div>
    </nav>
  <!-- top navbar -->