<div class="layout-header">
  <div class="navbar navbar-default">
    <div class="navbar-header">
      <a class="navbar-brand navbar-brand-center">
        <span style="font-weight: bold; color: #fff; font-size: 20px;">Admin Classroom</span>
      </a>
      <button class="navbar-toggler visible-xs-block collapsed" type="button" data-toggle="collapse" data-target="#sidenav">
        <span class="sr-only">Toggle navigation</span>
        <span class="bars">
          <span class="bar-line bar-line-1 out"></span>
          <span class="bar-line bar-line-2 out"></span>
          <span class="bar-line bar-line-3 out"></span>
        </span>
        <span class="bars bars-x">
          <span class="bar-line bar-line-4"></span>
          <span class="bar-line bar-line-5"></span>
        </span>
      </button>
      <button class="navbar-toggler visible-xs-block collapsed" type="button" data-toggle="collapse" data-target="#navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="arrow-up"></span>
        <span class="ellipsis ellipsis-vertical">
          @if(Auth::guard('employee')->check())
            @if(!is_null(Auth::user()->employee_id))
              @php ($name = Auth::user()->employee->name)
              @if(!is_null(Auth::user()->employee->photo))
                @php ($photo = url('storage/'.Auth::user()->employee->photo))
              @else
                @php ($photo = Avatar::create(Auth::user()->employee->name)->toBase64())
              @endif
            @else
              @php ($name = Auth::user()->username)
              @php ($photo = Avatar::create(Auth::user()->username)->toBase64())
            @endif
          @endif
          <img class="ellipsis-object" width="32" height="32" src="{{ $photo }}" alt="Profile">
        </span>
      </button>
    </div>
    <div class="navbar-toggleable">
      <nav id="navbar" class="navbar-collapse collapse">
        <button class="sidenav-toggler hidden-xs" title="Collapse sidenav ( [ )" aria-expanded="true"
                type="button">
          <span class="sr-only">Toggle navigation</span>
          <span class="bars">
                <span class="bar-line bar-line-1 out"></span>
                <span class="bar-line bar-line-2 out"></span>
                <span class="bar-line bar-line-3 out"></span>
                <span class="bar-line bar-line-4 in"></span>
                <span class="bar-line bar-line-5 in"></span>
                <span class="bar-line bar-line-6 in"></span>
              </span>
        </button>
        <ul class="nav navbar-nav navbar-right">
          <li class="visible-xs-block">
            <h4 class="navbar-text text-center">Hi, {{ Auth::user()->employee->name }}</h4>
          </li>
          <li class="dropdown hidden-xs">
            <button class="navbar-account-btn" data-toggle="dropdown" aria-haspopup="true">
              @if(Auth::guard('employee')->check())
                @if(!is_null(Auth::user()->employee_id))
                  @php ($name = Auth::user()->employee->name)
                  @if(!is_null(Auth::user()->employee->photo))
                    @php ($photo = url('storage/'.Auth::user()->employee->photo))
                  @else
                    @php ($photo = Avatar::create(Auth::user()->employee->name)->toBase64())
                  @endif
                @else
                  @php ($name = Auth::user()->employee->name)
                  @php ($photo = Avatar::create(Auth::user()->username)->toBase64())
                @endif
              @endif
              <img class="circle" width="36" height="36" src="{{ $photo }}" alt="test">  {{ $name }}
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
              <li class="divider"></li>
              @if(countRole() > 1)
                <li><a href="{{ route('role.pickList') }}"><i class="icon icon-user-plus"></i> Pilih Menu Akses</a></li>
              @endif
              <li>
                <a href="#">
                  <i class="fa fa-user"></i> Profile
                </a>
              </li>
              <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form1').submit();">
                  <i class="fa fa-sign-out-alt"></i> Log out
                </a>
                <form id="logout-form1" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
                </form>
              </li>
            </ul>
          </li>
          @if(countRole() > 1)
            <li class="visible-xs-block">
              <a href="{{ route('role.pickList') }}">
                <span class="icon icon-user-plus icon-lg icon-fw"></span>
                Pilih Hak Akses
              </a>
            </li>
            <li class="visible-xs-block">
              <a href="#">
                <span class="icon icon-user icon-lg icon-fw"></span>
                Profile
              </a>
            </li>
            <li class="visible-xs-block">
              <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out-alt"></i> Log out
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          @else
            <li class="visible-xs-block">
              <a href="#">
                <span class="icon icon-user icon-lg icon-fw"></span>
                Profile
              </a>
            </li>
            <li class="visible-xs-block">
              <a href="{{ route('logout') }}" class="icon icon-power-off icon-lg icon-fw" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span class="icon icon-power-off icon-lg icon-fw"></span>
                Sign out
              </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          @endif
        </ul>
        <div class="title-bar">
          <h1 class="title-bar-title">
            <span class="d-ib">Classroom</span>
            <span class="d-ib"></span>
          </h1>
          <p class="title-bar-description">
            <small>Tempat belajar online untuk para {{ (optional(configuration())->type_school == 1) ? 'mahasiswa dan dosen' : 'siswa dan guru'  }}</small>
          </p>
        </div>
      </nav>
    </div>
  </div>
</div>
