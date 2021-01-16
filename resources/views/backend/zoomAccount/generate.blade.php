@extends('backend.layouts.app')
@section('title', $title)
@push('styles')
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">
@endpush
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      <div class="row gutter-xs">
        <div class="col-xs-12">
          <div class="card">
            <div class="card-header">
              <strong>{{ $title }}</strong>
            </div>
            <div class="card-body">
              <div class="clearfix"></div>
              <form id="form_generate" method="post">
                <input type="hidden" name="id" id="id" value="{{ $user->id }}">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="first_name">First Name</label>
                      <input type="text" readonly name="first_name" id="first_name" class="form-control" value="{{ $user->first_name }}" style="background-color: rgba(0,0,0, 0.1)">
                    </div>
                    <div class="form-group">
                      <label for="email">Email</label>
                      <input type="email" readonly name="email" id="email" class="form-control" value="{{ $user->email }}" style="background-color: rgba(0,0,0, 0.1)">
                    </div>
{{--                    <div class="form-group">--}}
{{--                      <label for="pmi">Personal Meeting ID</label>--}}
{{--                      <div class="input-with-icon">--}}
{{--                        <div class="input-group">--}}
{{--                          <input class="form-control pmi-field" disabled id="pmi" value="{{ $zoomAccount->pmi }}" style="background-color: rgba(0,0,0, 0.1)" type="password" placeholder="Terisi ketika telah selesai aktivasi di email">--}}
{{--                          <span class="input-group-addon">--}}
{{--                              <label class="custom-control custom-control-primary custom-checkbox">--}}
{{--                                <input class="custom-control-input form-checkbox pmi" type="checkbox">--}}
{{--                                <span class="custom-control-indicator"></span>--}}
{{--                                <span class="custom-control-label">Show</span>--}}
{{--                              </label>--}}
{{--                            </span>--}}
{{--                        </div>--}}
{{--                        <span class="icon icon-key input-icon"></span>--}}
{{--                      </div>--}}
{{--                    </div>--}}
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="last_name">Last Name</label>
                      <input type="text" readonly name="last_name" id="last_name" class="form-control" value="{{ (is_null($user->last_name)) ? '-' : $user->last_name }}" style="background-color: rgba(0,0,0, 0.1)">
                    </div>
{{--                    <div class="form-group">--}}
{{--                      <label for="uuid">UUID</label>--}}
{{--                      <div class="input-with-icon">--}}
{{--                        <div class="input-group">--}}
{{--                          <input class="form-control uuid-field" disabled id="uuid" value="{{ $zoomAccount->id }}" style="background-color: rgba(0,0,0, 0.1)" type="password" placeholder="Terisi ketika berhasil generate">--}}
{{--                          <span class="input-group-addon">--}}
{{--                              <label class="custom-control custom-control-primary custom-checkbox">--}}
{{--                                <input class="custom-control-input form-checkbox uuid" type="checkbox">--}}
{{--                                <span class="custom-control-indicator"></span>--}}
{{--                                <span class="custom-control-label">Show</span>--}}
{{--                              </label>--}}
{{--                            </span>--}}
{{--                        </div>--}}
{{--                        <span class="icon icon-key input-icon"></span>--}}
{{--                      </div>--}}
{{--                    </div>--}}
                  </div>
                </div>
                <p>Note :</p>
                <ol style="margin-left: -25px">
                  <li>Akun zoom ini diperlukan jika akan melakukan pembelajaran menggunakan video conference</li>
                  <li>Generate user zoom hanya bisa dilakukan sekali</li>
                  <li>Jika akan mengubah nama dan email silahkan tekan tombol edit</li>
                  <li>Pastikan email aktif karena nanti akan ada email balasan dari zoom</li>
                  <li>Buka kotak masuk di email dan akan ada email aktivasi dari zoom, setelah itu bisa pilih sign in with google, sign in with facebook, atau sign in with password</li>
                  <li>Disarankan memilih sign in with google atau sign in with password</li>
                  <li>Disarankan menggunakan email dari Gmail karena supaya memudahkan dalam login ke aplikasi zoom</li>
                </ol>
                  @if ($user->userEmployee->status_generate == 0)
                  <button type="submit" class="btn btn-info btn-sm btn-generate pull-right"><i class="icon icon-send"></i> Generate</button>
                  <button type="button" class="btn btn-primary btn-sm btn-edit pull-right" style="margin-right: 10px"><i class="icon icon-edit"></i> Edit</button>
                  @endif
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
  <script>
    let status = 0;
    // show hide password
    $('.pmi').click(function () {
      if ($(this).is(':checked')) {
        $('.pmi-field').attr('type', 'text');
      } else {
        $('.pmi-field').attr('type', 'password');
      }
    });

    // show hide password
    $('.uuid').click(function () {
      if ($(this).is(':checked')) {
        $('.uuid-field').attr('type', 'text');
      } else {
        $('.uuid-field').attr('type', 'password');
      }
    });

    $('.btn-edit').click(function () {
      if (status === 0) {
        $('#first_name').attr('readonly', false);
        $('#last_name').attr('readonly', false);
        $('#email').attr('readonly', false);
        $('.btn-edit').html('<i class="icon icon-close"></i> Tutup');
        $('.btn-generate').attr('disabled', true);
        status = 1;
      } else {
        $('#first_name').attr('readonly', true);
        $('#last_name').attr('readonly', true);
        $('#email').attr('readonly', true);
        $('.btn-edit').html('<i class="icon icon-edit"></i> edit');
        $('.btn-generate').removeAttr('disabled');
        status = 0;
      }
    });

    // submit data
    $('.btn-generate').click(function (e) {
      e.preventDefault();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('zoom.generate') }}',
        type: 'post',
        data: $('#form_generate').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          loadingAfterSend();
          setTimeout(function () {
            location.reload();
          }, 1000);
        },
        error: function (resp) {
          loadingAfterSend();
          if (_.has(resp.responseJSON, 'errors')) {
            _.map(resp.responseJSON.errors, function (val, key) {
              $('#' + key + '-error').html(val[0]).fadeIn(1000).fadeOut(5000);
            })
          }
          alert(resp.responseJSON.message)
        }
      });
    });

    // method for handle before send data
    const loadingBeforeSend = function () {
      $(".btn-generate").attr('disabled', 'disabled');
      $(".btn-generate").html('<i class="fa fa-spinner fa-spin"></i> Loading....');
    }

    // method for handle after send data
    const loadingAfterSend = function () {
      $(".btn-generate").removeAttr('disabled');
      $(".btn-generate").html('<i class="icon icon-send"></i> Generate');
    }
  </script>
@endpush
