@extends('backend.layouts.app')
@section('title', $title)
@section('content')
  <div class="layout-content">
    <div class="layout-content-body">
      @if (checkPermission()->create)
        <button class="btn btn-info btn-sm" type="button" onclick="addData()" style="margin-bottom: 10px">
          <i class="icon icon-plus-circle"></i> Tambah
        </button>
      @endif
      <div class="row gutter-xs">
        <div class="col-xs-12">
          <div class="card">
            <div class="card-header">
              <strong>{{ $title }}</strong>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="userNav_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Role</th>
                    <th>Menu</th>
                    <th>Create</th>
                    <th>Update</th>
                    <th>Delete</th>
                    <th>Aksi</th>
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
  </div>

  <div id="modal_user_navigation" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_user_navigation" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="role_user_id">Role</label>
              <select name="role_user_id" id="role_user_id" class="form-control">
                <option value="">-- Pilih Role --</option>
                @foreach($roles as $role)
                  <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
              </select>
              <span class="text-danger">
                <strong id="role_user_id-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="navigation_id">Menu Navigasi</label>
              <select name="navigation_id" id="navigation_id" class="form-control">
                <option value="">-- Pilih Navigasi --</option>
                @foreach($navigations as $navigation)
                  <option value="{{ $navigation->id }}">{{ $navigation->title }}</option>
                @endforeach
              </select>
              <span class="text-danger">
                <strong id="navigation_id-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label class="form-label">Hak Akses</label><br/>
              <label class="custom-control custom-control-primary custom-checkbox">
                <input class="custom-control-input" name="create" id="create" type="checkbox" value="0"/>
                <span class="custom-control-indicator" style="margin-top: -2px"></span>
              </label>
              <label for="create" style="margin-left: 5px">Create</label>
              <label class="custom-control custom-control-primary custom-checkbox" style="margin-left: 10px">
                <input class="custom-control-input" name="update" id="update" type="checkbox" value="0"/>
                <span class="custom-control-indicator" style="margin-top: -2px"></span>
              </label>
              <label for="update" style="margin-left: 5px">Update</label>
              <label class="custom-control custom-control-primary custom-checkbox" style="margin-left: 10px">
                <input class="custom-control-input" name="delete" id="delete" type="checkbox" value="0"/>
                <span class="custom-control-indicator" style="margin-top: -2px"></span>
              </label>
              <label for="delete" style="margin-left: 5px">Delete</label>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
            <button class="btn btn-primary btn-submit" type="submit">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script>
    let table, url, type;

    // load data in datatable
    table = $('#userNav_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('permission.json') }}',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'role.name'},
        {data: 'navigation.title'},
        {data: 'create'},
        {data: 'update'},
        {data: 'delete'},
        {data: 'action'}
      ],

      columnDefs: [
        {
          targets: [0, -1, -2, -3, -4],
          orderable: false
        },
      ],
    });

    // select2
    $('#role_user_id, #navigation_id').select2({width: "100%"});

    // open modal add data
    const addData = function() {
      $('#modal_user_navigation').modal('show');
      $('.modal-title').text('Tambah User Navigation');
      resetForm();
    }

    // submit data
    $('.btn-submit').click(function (e) {
      e.preventDefault();
      const role = $('#role_user_id').val();
      const navigation = $('#navigation_id').val();
      const create = ($('#create').is(':checked')) ? 1 : 0;
      const update = ($('#update').is(':checked')) ? 1 : 0;
      const destroy = ($('#delete').is(':checked')) ? 1 : 0;

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: '{{ route('permission.store') }}',
        type: 'post',
        data: {
          role_user_id: role,
          navigation_id: navigation,
          create: create,
          update: update,
          destroy: destroy
        },
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          loadingAfterSend();
          resetForm();
          if (data.status === 200) {
            $('#modal_user_navigation').modal('hide');
            setTimeout(function () {
              location.reload();
            }, 1000);
          }
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

    // update status
    table.on('change', 'input[type="checkbox"]', function () {
      const type = $(this).val();
      const id = $(this).attr('id');
      const value = ($(this).is(':checked')) ? 1 : 0;

      $.ajax({
        headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
        url: '{{ route('permission.update') }}',
        type: 'post',
        data: {
          id: id,
          type: type,
          value: value
        },
        dataType: 'json',
        success: function (resp) {
          notification(resp.status, resp.message);
          table.ajax.reload();
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
    });

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
                url: "{{ route('permission.delete') }}",
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
      $('#role_user_id').select2({width: '100%'}).val('').trigger('change');
      $('#navigation_id').select2({width: '100%'}).val('').trigger('change');
      $('#create').prop('checked', false);
      $('#update').prop('checked', false);
      $('#delete').prop('checked', false);
    }
  </script>
@endpush
