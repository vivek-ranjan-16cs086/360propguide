<!-- Sidebar  -->
<nav id="sidebar">
    <div class="sidebar_blog_1">
        <div class="sidebar-header">
        </div>
        <div class="sidebar_user_info">
            <div class="icon_setting"></div>
            <div class="user_profle_side">
                <div class="user_img"><img class="img-responsive" src="{{url('assets/images/icon.png')}}" alt="#" />
                </div>
                <div class="user_info">
                    <h6>{{ucfirst(Auth::user()->name)}}</h6>
                    <p><span class="online_animation"></span> Online</p>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar_blog_2">
        <h4>General</h4>
        <ul class="list-unstyled components">
            <li class="active">
                <a href="{{route('dashboard')}}"><i class="fa fa-dashboard yellow_color"></i> <span>Dashboard</span></a>
            </li>
            <li><a href="{{route('career.index')}}"><i class="fa-solid fa-graduation-cap green_color"></i><span>Career</span></a></li>
            <li><a href="{{route('blogs.index')}}"><i class="fa-solid fa-blog green_color"></i><span>Blogs</span></a></li>
            <li><a href="{{route('projects.index')}}"><i class="fa fa-home red_color2"></i> <span>Projects</span></a></li>
            <li><a href="{{ route('properties.index') }}"><i class="fa fa-home red_color2"></i> <span>Properties</span></a></li>
            <li><a href="{{route('queries.index')}}"><i class="fa fa-bar-chart-o green_color"></i> <span>Queries</span></a></li> 
            <li><a href="{{route('custom-links.index')}}"><i class="fa fa-cog yellow_color"></i> <span>Custom Links</span></a></li>
            <!--<li><a href="{{route('settings.index')}}"><i class="fa fa-cog yellow_color"></i> <span>Settings</span></a></li> -->
			<li><a href="{{ route('developers.index') }}"><i class="fa fa-code yellow_color"></i><span>Developers</span></a></li>
			<li><a href="{{ route('locations.index') }}"><i class="fa-solid fa-location-dot yellow_color"></i><span>Locations</span></a></li>
        </ul>
    </div>
</nav>
<!-- end sidebar -->
