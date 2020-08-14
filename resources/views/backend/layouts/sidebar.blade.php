<div class="layout-main">
  <div class="layout-sidebar">
    <div class="layout-sidebar-backdrop"></div>
    <div class="layout-sidebar-body">
      <div class="custom-scrollbar">
        <nav id="sidenav" class="sidenav-collapse collapse">
          <ul class="sidenav level-1 list-menu">
            <li class="sidenav-search">
              <form class="sidenav-form" action="{{ route('role.pick') }}" method="POST" id="pickRole" role="pickRole">
                @csrf
                <div class="form-group form-group-sm">
                  @if(countRole() > 1)
                    <select name="role_id" id="demo-select2-1" class="form-control">
                      @foreach(getRoleUser() as $user)
                        @if(!empty(session('role_id')) && session('role_id') == $user->role->id)
                          <option value="{!! $user->role->id !!}" selected>{!! $user->role->name !!}</option>
                        @else
                          <option value="{!! $user->role->id !!}">{!! $user->role->name !!}</option>
                        @endif
                      @endforeach
                    </select>
                  @else
                    <div class="input-with-icon">
                      <input class="form-control" id="search" type="text" placeholder="Search…">
                      <span class="icon icon-search input-icon"></span>
                    </div>
                  @endif
                </div>
              </form>
            </li>
            <li class="sidenav-heading">Navigation</li>
            <li class="sidenav-item {{ isActiveRoute('dashboard') }}">
              <a href="{{ route('dashboard') }}">
                <span class="sidenav-icon icon icon-dashboard"></span>
                <span class="sidenav-label">Dashboard</span>
              </a>
            </li>
            @if (!is_null(configuration()))
              @if(!empty(navigation()))
                <input type="hidden" value="{{ getNavigationId() }}">
                @foreach(navigation() as $route)
                  @if(empty($route['child']))
                    <li class='sidenav-item {!! isActiveRoute($route['url']) !!}'>
                      <a href='{!! checkRouteExist($route['url']) !!}'>
                        <span class='sidenav-icon {{ $route['icon'] }}'></span>
                        <span class='sidenav-label'> {{ $route['title'] }}</span>
                      </a>
                    </li>
                  @else
                    <li class='sidenav-item has-subnav'>
                      <a href='#'>
                        <span class='sidenav-icon {{ $route['icon'] }}'></span>
                        <span class='sidenav-label'> {{ $route['title'] }}</span>
                      </a>
                      <ul class='sidenav level-2 sub-menu collapse'>
                        <li class='sidenav-heading'>{{ $route['title'] }}</li>
                        @foreach($route['child'] as $child)
                          <li class='{{ isActiveRoute($child['url']) }}'>
                            <a href='{!! checkRouteExist($child['url']) !!}' style='cursor: pointer'>{{ $child['title'] }}</a>
                          </li>
                        @endforeach
                      </ul>
                    </li>
                  @endif
                @endforeach
              @else
                <p>Anda belum dipilih</p>
              @endif
            @else
              <li class='sidenav-item has-subnav'>
                <a href='#'>
                  <span class='sidenav-icon icon icon-gears'></span>
                  <span class='sidenav-label'>Pengaturan</span>
                </a>
                <ul class='sidenav level-2 sub-menu collapse'>
                  <li class='sidenav-heading'>Pengaturan</li>
                  <li class='{{ isActiveRoute('configuration.index') }}'>
                    <a href='{!! checkRouteExist('configuration.index') !!}' style='cursor: pointer'>Konfigurasi Aplikasi</a>
                  </li>
                </ul>
              </li>
            @endif
          </ul>
        </nav>
      </div>
    </div>
  </div>

  {{-- Content --}}
  <div id="app">
    @yield('content')
  </div>

  <div class="layout-footer">
    <div class="layout-footer-body">
      <small class="version"> Version 1.0</small>
      <small class="copyright">{{ \Carbon\Carbon::now()->format('Y') }} &copy; Classroom <a href="https://laravel.com" target="_blank">Powered By Laravel 7</a></small>
    </div>
  </div>
</div>
