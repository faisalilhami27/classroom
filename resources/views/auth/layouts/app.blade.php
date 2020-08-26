<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title') | Classroom</title>
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta property="og:url" content="http://demo.madebytilde.com/elephant">
  <meta property="og:type" content="website">
  <meta property="og:title" content="The fastest way to build Modern Admin APPS for any platform, browser, or device.">
  <meta property="og:image" content="http://demo.madebytilde.com/elephant.jpg">
  <link rel="icon" type="image/png" href="{{ asset('storage/' . optional(configuration())->school_logo) }}" sizes="32x32">
  <link rel="icon" type="image/png" href="{{ asset('storage/' . optional(configuration())->school_logo) }}" sizes="16x16">
  <meta name="theme-color" content="#1d2a39">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,400italic,500,700">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
  <style>
    /* register */
    .tab-content > .tab-pane {
      display: block;
    }

    #myTabContent {
      position: relative;
      width: 100%;
      height: 370px;
      z-index: 5;
      overflow: hidden;
    }

    #myTabContent .tab-pane {
      position: absolute;
      top: 0;
      padding: 10px 40px;
      z-index: 1;
      opacity: 0;
      -webkit-transition: all linear 0.3s;
      -moz-transition: all linear 0.3s;
      -o-transition: all linear 0.3s;
      -ms-transition: all linear 0.3s;
      transition: all linear 0.3s;
    }

    #login,
    .content-3 {
      -webkit-transform: translateX(-250px);
      -moz-transform: translateX(-250px);
      -o-transform: translateX(-250px);
      -ms-transform: translateX(-250px);
      transform: translateX(-250px);
    }

    #newuser,
    .content-4 {
      -webkit-transform: translateX(250px);
      -moz-transform: translateX(250px);
      -o-transform: translateX(250px);
      -ms-transform: translateX(250px);
      transform: translateX(250px);
    }

    .register .register-right ul .nav-item:a.active ~ #myTabContent #login,
    .register .register-right ul .nav-item:a.active ~ #myTabContent .content-2,
    .register .register-right ul .nav-item:a.active ~ #myTabContent #newuser
    {
      -webkit-transform: translateX(0px);
      -moz-transform: translateX(0px);
      -o-transform: translateX(0px);
      -ms-transform: translateX(0px);
      transform: translateX(0px);
      z-index: 100;
      -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
      filter: alpha(opacity=100);
      opacity: 1;
      -webkit-transition: all ease-out 0.2s 0.1s;
      -moz-transition: all ease-out 0.2s 0.1s;
      -o-transition: all ease-out 0.2s 0.1s;
      -ms-transition: all ease-out 0.2s 0.1s;
      transition: all ease-out 0.2s 0.1s;
    }


    @keyframes page {
      0% {
        left: 0;
      }
      50% {
        left: 10px;
      }
      100% {
        left: 0;
      }
    }

    @-moz-keyframes page {
      0% {
        left: 0;
      }
      50% {
        left: 10px;
      }
      100% {
        left: 0;
      }
    }

    @-webkit-keyframes page {
      0% {
        left: 0;
      }
      50% {
        left: 10px;
      }
      100% {
        left: 0;
      }
    }

    @-ms-keyframes page {
      0% {
        left: 0;
      }
      50% {
        left: 10px;
      }
      100% {
        left: 0;
      }
    }

    @-o-keyframes page {
      0% {
        left: 0;
      }
      50% {
        left: 10px;
      }
      100% {
        left: 0;
      }
    }

    body {
      background: -webkit-linear-gradient(left, #1143a6, #00c6ff);
    }

    .register {
      background: -webkit-linear-gradient(left, #1143a6, #00c6ff);
      padding: 3%;
    }

    .register-left {
      text-align: center;
      color: #fff;
      margin-top: 4%;
    }

    .register-left input {
      border: none;
      border-radius: 1.5rem;
      padding: 2%;
      width: 60%;
      background: #f8f9fa;
      font-weight: bold;
      color: #383d41;
      margin-top: 30%;
      margin-bottom: 3%;
      cursor: pointer;
    }

    .register-right {
      background: #f8f9fa;
      height: 435px;
      border-top-left-radius: 15% 50%;
      border-bottom-left-radius: 15% 50%;
    }

    .register-left img {
      margin-top: 15%;
      margin-bottom: 5%;
      width: 25%;
      -webkit-animation: mover 2s infinite alternate;
      animation: mover 1s infinite alternate;
    }

    @-webkit-keyframes mover {
      0% {
        transform: translateY(0);
      }
      100% {
        transform: translateY(-20px);
      }
    }

    @keyframes mover {
      0% {
        transform: translateY(0);
      }
      100% {
        transform: translateY(-20px);
      }
    }

    .register-left p {
      font-weight: lighter;
      padding: 12%;
      margin-top: -9%;
    }

    .register .register-form {

    }


    .register .nav-tabs {
      margin-top: 1%;
      border: none;
      background: #0062cc;
      border-radius: 20px;
      width: 35%;
      float: right;
    }

    #myTab .nav-item {
      padding: 5px 3px;
      text-align: center;
      display: block;
      margin: 0px 6px;
    }

    .register .nav-tabs .nav-link {
      padding: 10px 8px;
      height: 25px;
      color: #fff;
      font-size: 13px;
      border-top-right-radius: 1.5rem;
      border-bottom-right-radius: 1.5rem;
    }

    .register .nav-tabs .nav-link:hover {
      border: none;
    }

    .register .nav-tabs .nav-link.active {
      color: #1143a6;
      border: 1px solid #1143a6;
      border-top-left-radius: 1.5rem;
      border-bottom-left-radius: 1.5rem;
    }

    .register-heading {
      text-align: center;
      color: #1143a6;
    }

    #login.active {
      -webkit-transform: translateX(0px);
      transform: translateX(0px);
      z-index: 100;
      -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
      filter: alpha(opacity=100);
      opacity: 1;
      -webkit-transition: all ease-out 0.2s 0.1s;
      transition: all ease-out 0.2s 0.1s;
    }

    #newuser.active {
      -webkit-transform: translateX(0px);
      transform: translateX(0px);
      z-index: 100;
      -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
      filter: alpha(opacity=100);
      opacity: 1;
      -webkit-transition: all ease-out 0.2s 0.1s;
      transition: all ease-out 0.2s 0.1s;
    }
  </style>
  @stack('styles')
  @show
</head>
<body>
<div id="app">
  @yield('content')
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script>
@stack('scripts')
@show
</body>
</html>
