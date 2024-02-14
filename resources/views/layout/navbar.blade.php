@if (request()->route())
@if (\Route::current()->getName() != 'login' && \Route::current()->getName() != 'registration' && \Route::current()->getName() != 'forgotPassword')
<nav class="navbar navbar-expand-md bg-light navbar-light">
  <div class="container">
    <a class="navbar-brand" href="{{route('index')}}">
         <img src="{{asset('')}}images/logo/logo.png" alt="wexprez logo" height="40px" width="160px">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse " id="collapsibleNavbar">
      <ul class="navbar-nav ms-auto">
      @if (Session::has('user'))

        <li class="nav-item" id="global-search">
          <div class="input-group">
            <input type="search" class="form-control" name="search" id="search" required placeholder="Search here..">
            <span onclick="search()" class="input-group-text"><i class="fas fa-search"></i></span>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{route('index')}}">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{route('setting')}}">Settings</a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropbtn" href="{{route('notification')}}">
          <i class="fa-solid fa-bell"></i>@if($notifyCount != 0)<sup>{{$notifyCount}}</sup>@endif</a>

          @if($notifyCount != 0)
            <div class="dropdown-content">
              <ul class="list-unstyled">
                @foreach($notifyList->slice(0, 7) as $notify)
                <li><a href="{{route('notificationReadPage',$notify->id)}}">{{$notify->text}}</a></li>
                @endforeach
              </ul>
            </div>
            @endif
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="{{route('profileIxprez', $profile->user_name)}}">
          <img src="{{$profile->profile_image}}" alt="{{$profile->full_name}}" height="25px" width="25px" class="rounded-circle">
          {{$profile->full_name}}</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="{{route('logout')}}">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
          </a>
        </li>

        @else
        <li class="nav-item">
          <a class="nav-link" href="{{route('login')}}">
          <i class="fa-solid fa-right-to-bracket"></i> Login
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="{{route('registration')}}">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Registration
          </a>
        </li>
        @endif
      </ul>
    </div>
  </div>
</nav>
@endif
@endif


