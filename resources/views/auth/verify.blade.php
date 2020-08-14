<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Verifikasi email</title>
  <link rel="stylesheet" href="{{ asset('backend/css/vendor.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/elephant.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/signup-2.min.css') }}">
</head>
<body>
  <div class="container">
    <h3>Silahkan klik link dibawah ini untuk verifikasi email anda :</h3>
    <a href="{{ URL('verify/email/' . $token) }}" class="btn btn-primary btn-sm">Verifikasi Email</a>
  </div>
</body>
</html>
