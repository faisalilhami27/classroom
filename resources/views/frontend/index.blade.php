<!doctype html>
<html lang="en" id="full">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Classroom</title>
  <link rel="icon" type="image/png" href="{{ asset('storage/'. optional(configuration())->school_logo) }}" sizes="32x32">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css" rel="stylesheet">
  <style>
    /* scroller browser */
    ::-webkit-scrollbar {
      width: 7px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
      -webkit-box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
      -webkit-border-radius: 10px;
      border-radius: 10px;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
      -webkit-border-radius: 10px;
      border-radius: 10px;
      background: #a6a5a5;
      -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
    }

    ::-webkit-scrollbar-thumb:window-inactive {
      background: rgba(0, 0, 0, 0.4);
    }
  </style>
</head>
<body>
<div id="app">
  <app user="{{ $encode }}"></app>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script>
</body>
</html>
