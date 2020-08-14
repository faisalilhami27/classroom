@extends('backend.layouts.app')
@section('title', $title)
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      @if (checkPermission()->create)
        <button class="btn btn-info btn-sm" onclick="addData()" type="button" style="margin-bottom: 10px">
          <i class="icon icon-plus-circle"></i> Tambah
        </button>
      @endif
      <div class="row gutter-xs">
        <div class="col-xs-12">
          <div class="card">
            <div class="card-header">
              <strong>Daftar Navigation</strong>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="navigation_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Title</th>
                    <th>URL</th>
                    <th>Nomor Urut Parent</th>
                    <th>Nomor Urut Child</th>
                    <th>Icon</th>
                    <th width="150px">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="modal_navigation" role="dialog" class="modal fade" aria-labelledby="modal_navigation_label"
         aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal">
              <span aria-hidden="true">×</span>
              <span class="sr-only">Close</span>
            </button>
            <h4 class="modal-title">Tambah Data Navigation</h4>
          </div>
          <form class="form" id="form_navigation" method="post">
            <input type="hidden" name="id" id="id">
            <div class="modal-body">
              <div class="form-group">
                <label for="title">Nama Menu <span class="text-danger">*</span></label>
                <input id="title" name="title" class="form-control" type="text" placeholder="Contoh : Kelola Menu"
                       maxLength="50" autoComplete="off"/>
                <span class="text-danger">
                  <strong id="title-error"></strong>
                </span>
              </div>
              <div class="form-group">
                <label for="url">Nama Route <span class="text-danger">*</span></label>
                <input id="url" name="url" class="form-control" type="text" placeholder="Contoh: navigasi.index"
                       maxLength="30" autoComplete="off"/>
                <span class="text-danger">
                  <strong id="url-error"></strong>
                </span>
              </div>
              <div class="form-group">
                <label for="icon">Icon</label>
                <input id="icon" name="icon" class="form-control" type="text" placeholder="Contoh : icon icon-user"
                       maxLength="30" autoComplete="off"/>
                <span class="text-danger">
                  <strong id="icon-error"></strong>
                </span>
              </div>
              <div class="form-group">
                <label for="parent_id" class="form-label">Parent Menu <span class="text-danger">*</span></label>
                <select id="parent_id" name="parent_id" class="form-control">
                  <option value="">-- Pilih Parent Menu --</option>
                  <option value="0">Main Menu</option>
                  @foreach($navigations as $navigation)
                    <option value="{{ $navigation->id }}">{{ $navigation->title }}</option>
                  @endforeach
                </select>
                <span class="text-danger">
                  <strong id="parent_id-error"></strong>
                </span>
              </div>
              <div class="form-group order_num" style="display: none">
                <label for="order_num">Nomor Urut</label>
                <input id="order_num" name="order_num" class="form-control" type="text" placeholder="Contoh : 1"
                       maxLength="4" autoComplete="off"/>
                <span class="text-danger">
                  <strong id="order_num-error"></strong>
                </span>
              </div>
              <div class="form-group order_sub" style="display: none">
                <label for="order_sub">Nomor Urut Sub Menu</label>
                <input id="order_sub" name="order_sub" class="form-control" type="text" placeholder="Contoh : 1"
                       maxLength="4" autoComplete="off"/>
                <span class="text-danger">
                  <strong id="order_sub-error"></strong>
                </span>
              </div>
              <p>Note : </p>
              <ol style="margin-left: -25px">
                <li>*) Harus diisi</li>
                <li>Pengisian nama route bisa dilihat contohnya di web.php</li>
                <li>Jika menu dijadikan parent menu nama route bisa diisi dengan tanda #</li>
                <li>Jika menu dijadikan parent menu pilih Main Menu</li>
              </ol>
            </div>
            <div class="modal-footer">
              <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
              <button class="btn btn-primary btn-submit" type="submit">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script>
    let table, url, type;

    // load data in datatable
    table = $('#navigation_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('navigation.json') }}',
        "type": 'POST',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'title'},
        {data: 'url'},
        {data: 'order_num', sClass: 'text-center'},
        {data: 'order_sub', sClass: 'text-center'},
        {data: 'icon', orderable: false},
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1],
          orderable: false
        },
      ],
    });

    // select2
    $('#parent_id').select2({width: '100%'});

    // show order sub menu
    $('#parent_id').change(function () {
      const value = $(this).val();
      if (value == "") {
        $('.order_sub').slideUp(1000);
        $('.order_num').slideUp(1000);
      } else {
        if (value == 0) {
          $('.order_sub').hide();
          $('.order_num').slideDown(1000);
        } else {
          $('.order_sub').slideDown(1000);
          $('.order_num').hide();
        }
      }
    })

    // show modal add data
    const addData = function () {
      $('#modal_navigation').modal('show');
      $('.modal-title').text('Tambah Data Navigation');
      url = '{{ route('navigation.create') }}';
      type = 'post';
      resetForm();
    }

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_navigation').modal('show');
      $('.modal-title').text('Edit Data Navigation');
      url = '{{ route('navigation.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('navigation.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('#id').val(data.id);
            $('#title').val(data.title);
            $('#url').val(data.url);
            $('#icon').val(data.icon);
            $('#order_num').val(data.order_num);
            $('#order_sub').val(data.order_sub);
            $('#parent_id').select2({width: '100%'}).val(data.parent_id).trigger('change');
            if (data.order_num != null) {
              $('.order_sub').hide();
              $('.order_num').show();
            } else {
              $('.order_sub').show();
              $('.order_num').hide();
            }
          } else {
            notification(resp.status, resp.message);
          }
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
    }

    // delete data
    const deleteData = function (id) {
      $.confirm({
        content: 'Data yang dihapus tidak akan dapat dikembalikan.',
        title: 'Apakah yakin ingin menghapus ?',
        type: 'red',
        typeAnimated: true,
        buttons: {
          cancel: {
            text: 'Batal',
            btnClass: 'btn-danger',
            keys: ['esc'],
            action: function () {
            }
          },
          ok: {
            text: '<i class="icon icon-trash"></i> Hapus',
            btnClass: 'btn-warning',
            action: function () {
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ route('navigation.delete') }}",
                type: "delete",
                data: {id: id},
                dataType: "json",
                success: function (data) {
                  notification(data.status, data.message);
                  setTimeout(function () {
                    location.reload();
                  }, 1000)
                },
                error: function (xhr, status, error) {
                  alert(status + " : " + error);
                }
              });
            }
          }
        }
      });
    }

    // submit data
    $('.btn-submit').click(function (e) {
      e.preventDefault();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: url,
        type: type,
        data: $('#form_navigation').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          $('#modal_navigation').modal('hide');
          loadingAfterSend();
          resetForm();
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
      $(".btn-submit").attr('disabled', 'disabled');
      $(".btn-submit").html('<i class="fa fa-spinner fa-spin"></i> Menyimpan data....');
    }

    // method for handle after send data
    const loadingAfterSend = function () {
      $(".btn-submit").removeAttr('disabled');
      $(".btn-submit").html('Submit');
    }

    // reset form
    const resetForm = function () {
      $('#id').val('');
      $('#title').val('');
      $('#url').val('');
      $('#icon').val('');
      $('#order_num').val('');
      $('#order_sub').val('');
      $('#parent_id').select2({width: '100%'}).val('').trigger('change');
    }
  </script>
@endpush
