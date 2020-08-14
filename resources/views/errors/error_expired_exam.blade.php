<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Forbidden</title>
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
  <link rel="icon" type="image/png" href="{{ asset('storage/'. optional(configuration())->school_logo) }}" sizes="16x16">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,400italic,500,700">
  <link rel="stylesheet" href="{{ asset('backend/css/vendor.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/elephant.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/errors.min.css') }}">
</head>
<body>
<div class="error">
  <div class="error-body">
    <h1 class="error-heading">Maaf</h1>
    <h4 class="error-subheading">Ujian sudah tidak tersedia lagi</h4>
    <p>
      <small>Silahkan klik tombol di bawah ini untuk kembali ke halaman sebelumnya</small>
    </p>
    <p><a class="btn btn-primary btn-pill btn-thick" href="#" onclick="window.history.go(-1); return false;"><i class="icon icon-backward"></i> Kembali ke halaman utama</a></p>
  </div>
  <div class="error-footer">
    <p>
      <small>© {{ date('Y') . ' ' . optional(configuration())->school_name }}</small>
    </p>
  </div>
</div>
<script src="{{ asset('backend/js/vendor.min.js') }}"></script>
<script src="{{ asset('backend/js/elephant.min.js') }}"></script>
</body>
</html>
