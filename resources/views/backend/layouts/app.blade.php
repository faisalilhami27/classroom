<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Classroom | @yield('title')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
  <link rel="icon" type="image/png" href="{{ asset('storage/'. optional(configuration())->school_logo) }}" sizes="32x32">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,400italic,500,700">
  <link rel="stylesheet" href="{{ asset('backend/css/vendor.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/elephant.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/css/application.min.css') }}">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
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
  @stack('styles')
  @show
</head>
<body class="layout layout-header-fixed layout-sidebar-fixed">
{{-- Header --}}
@include('backend.layouts.header')

{{-- Sidebar --}}
@include('backend.layouts.sidebar')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js"></script>
<script src="{{ asset('backend/js/vendor.min.js') }}"></script>
<script src="{{ asset('backend/js/elephant.min.js') }}"></script>
<script src="{{ asset('backend/js/application.min.js') }}"></script>
<script src="{{ asset('backend/js/demo.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-confirm.js') }}"></script>
<script src="{{ asset('backend/js/script.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.4.4/underscore-min.js"></script>
<script>
  $(document).ready(function () {
    setNavigation();

    $('[name="role_id"]').on('change', function() {
      $('#pickRole').submit();
    })
  });

  function setNavigation() {
    let path = window.location.pathname;
    path = path[0] == '/' ? path.substr(1) : path; //it will remove the dash in the URL
    $("ul.list-menu .sub-menu a").each(function() {
      const href = $(this).attr('href');
      const pathHref = href.split('/').slice(3).join('/');

      if (path === pathHref) {
        $(this).parent().parent().closest('li').addClass('active');
        $(this).parent().parent().closest('li').addClass('open');
        $(this).closest('ul').addClass('in');
      }
    });
  }
</script>
@stack('scripts')
@show
</body>
</html>
