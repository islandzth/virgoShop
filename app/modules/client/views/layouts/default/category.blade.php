<!--sidebar-->
        <div class="col-sm-2 vr-left-sidebar ">
        <div class="sidebar-offcanvas" id="sidebar">
          <div>
            <ul class="nav navbar">
              <li><a href="{{URL::route('index')}}">HOME</a></li>
              @foreach($navbarCatList as $catObj)
              <li><a href="{{URL::route('categoryDetail', array($catObj->category_id, $catObj->identity))}}">{{$catObj->name}}</a></li>
              @endforeach
            </ul>
           </div>
        </div><!--/sidebar-->
  	   </div><!-- vr-left-sidebar-->