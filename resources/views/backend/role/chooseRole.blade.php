<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Halaman Pilih Role</title>
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
  <link rel="icon" type="image/png" href="{{ asset('storage/' . configuration()->school_logo) }}" sizes="32x32">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,400italic,500,700">
  <link rel="stylesheet" href="{{ asset('backend/css/vendor.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/elephant.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/errors.min.css') }}">
</head>
<body>
<center>
  <div class="error-body">
    <h1 class="error-heading"><b>Calssroom</b></h1>
    <h4 class="error-subheading">Untuk masuk ke dalam Aplikasi</h4>
    <p>
      <small>Silahkan pilih level yang anda miliki dibawah ini :</small>
    </p>
  </div>
  <div class="row" style="position: relative; top: -30px">
    <form id="chooseRole" method="POST" action="{!! route('role.pick') !!}">
      @csrf
      @forelse($accessList as $access)
        <button style="margin-top: 10px" class="btn btn-primary btn-pill btn-thick" type="submit" name="role_id" value="{!! $access->role->id !!}">
          {!! $access->role->name !!}
        </button>
      @empty
        <center>Belum tersedia akses menu, silahkan kontak admin untuk mendapatkan akses.</center>
      @endforelse
    </form>
  </div>
  <div class="error-footer">
    <p>
      <small>© {{ date('Y') }} Classroom</small>
    </p>
  </div>
</center>
<a href="{{ route('logout') }}" style="margin-right: 10px" class="btn btn-danger pull-right" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
  <i class="icon icon-sign-out"></i> <strong>Logout</strong>
</a>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
  @csrf
</form>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="{{ asset('backend/js/vendor.min.js') }}"></script>
<script src="{{ asset('backend/js/elephant.min.js') }}"></script>
</body>
</html>
