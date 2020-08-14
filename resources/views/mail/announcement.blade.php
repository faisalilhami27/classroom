<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Pengumuman Classroom</title>
</head>
<body>
<center>
  <h2>Pengumuman Baru</h2>
  <p>{{ $announcement->title }}</p>
  <p>Silahkan login ke aplikasi classroom <b><a href="{{ route('login') }}">disini</a></b> untuk melihat detailnya</p>
</center>
</body>
</html>
