<nav id="headerContainer" class="navbar navbar-inverse navbar-fixed-top">
    <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target="#adminNav" data-canvas="body">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>

    <div class="navbar-header">
            <a class="navbar-brand" href="/"><span class="glyphicon glyphicon-inbox"></span> MT2</a>
    </div>
    <ul class="nav navbar-nav navbar-right">
        @if(Sentinel::check())
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{Sentinel::getUser()->first_name}} {{Sentinel::getUser()->first_name}}<span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="#">My Profile</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="{{route("logout")}}" target="_self">Logout</a></li>
            </ul>
        </li>
            @else
            <li><a href="{{route("login")}}" target="_self">Login</a></li>
        @endif
    </ul>
</nav>
