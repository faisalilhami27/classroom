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
              <div class="clearfix"></div>
              <div class="table-responsive" style="margin-top: 10px">
                <table id="rules_table" class="table table-striped table-hover table-nowrap dataTable" width="100%">
                  <thead>
                  <tr>
                    <th width="20px">No</th>
                    <th>Jenis Ujian</th>
                    <th>Text</th>
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

  <div id="modal_rules" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <form action="" id="form_rules" method="post">
          <input type="hidden" id="id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="name">Nama Peraturan Ujian <span class="text-danger">*</span></label>
              <input type="text" name="name" id="name" class="form-control" placeholder="Nama peraturan ujian">
              <span class="text-danger">
                <strong id="name-error"></strong>
              </span>
            </div>
            <div class="form-group">
              <label for="text">Text <span class="text-danger">*</span></label>
              <textarea name="text" id="text" rows="10" class="editor"></textarea>
              <span class="text-danger">
                <strong id="text-error"></strong>
              </span>
            </div>
            <p>Note: *) harus diisi.</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
            <button class="btn btn-primary btn-submit" type="submit">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modal_show" role="dialog" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div class="well show_text"></div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
        </div>
      </div>
    </div>
  </div>
@stop
@push('scripts')
  <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
  <script>
    let table, url, type;
    let content = document.getElementById('text');
    CKEDITOR.replace(content, {
      language: 'en-gb',
      allowedContent: true,
      enterMode: CKEDITOR.ENTER_BR,
      basicEntities: false,
      entities_additional: 'lt,gt,amp,apos,quot',
      toolbar: [
        ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
        {
          name: 'basicstyles',
          items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat']
        },
        {
          name: 'paragraph',
          items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl']
        },
        {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
        {name: 'colors', items: ['TextColor', 'BGColor']},
      ]
    });
    CKEDITOR.config.allowedContent = true;
    CKEDITOR.on('instanceReady', function (ev) {
      ev.editor.dataProcessor.writer.selfClosingEnd = '>';
    });

    // load data in datatable
    table = $('#rules_table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [],

      ajax: {
        "url": '{{ route('rules.json') }}',
        "type": 'post',
        "headers": {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
      },

      columns: [
        {data: 'DT_RowIndex', searchable: false},
        {data: 'name'},
        {data: 'text', render: function (row, type, data) {
            return `<a href="#" class="badge badge-primary" onclick="showText(${data.id})">Lihat Text Peraturan</a>`;
        }},
        {data: 'action', sClass: 'text-center'},
      ],

      columnDefs: [
        {
          targets: [0, -1, -2],
          orderable: false
        },
      ],
    });

    // show modal add data
    const addData = function () {
      $('#modal_rules').modal('show');
      $('.modal-title').text('Tambah Data Peraturan Ujian');
      url = '{{ route('rules.create') }}';
      type = 'post';
      resetForm();
    }

    // show text rules
    const showText = function (id) {
      $('#modal_show').modal('show');
      $.ajax({
        url: '{{ route('rules.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          const parser = new DOMParser;
          const dom = parser.parseFromString('<!doctype html><body>' + resp.text,'text/html');
          const text = dom.body.textContent;
          $('.show_text').html(text);
        },
        error: function (xhr, status, error) {
          alert(status + ' : ' + error);
        }
      });
    }

    // get data from database for edit data
    const editData = function (id) {
      $('#modal_rules').modal('show');
      $('.modal-title').text('Edit Data Peraturan Ujian');
      url = '{{ route('rules.update') }}';
      type = 'put';
      $.ajax({
        url: '{{ route('rules.edit') }}',
        type: 'get',
        data: {id: id},
        dataType: 'json',
        success: function (resp) {
          if (resp.status === 200) {
            const data = resp.data;
            $('#id').val(data.id);
            $('#name').val(data.name);
            const parser = new DOMParser;
            const dom = parser.parseFromString('<!doctype html><body>' + resp.text,'text/html');
            const text = dom.body.textContent;
            CKEDITOR.instances['text'].setData(text);
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
                url: "{{ route('rules.delete') }}",
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
      for (let instance in CKEDITOR.instances) {
        CKEDITOR.instances[instance].updateElement();
      }
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: url,
        type: type,
        data: $('#form_rules').serialize(),
        dataType: 'json',
        beforeSend: function () {
          loadingBeforeSend();
        },
        success: function (data) {
          notification(data.status, data.message);
          $('#modal_rules').modal('hide');
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
      $('#name').val('');
      CKEDITOR.instances['text'].setData('');
    }
  </script>
@endpush
