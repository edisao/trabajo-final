@extends('layouts.main')
@section('page_title', trans('msg.msgEditarInformacion') )

@section('css_datatable')
<link rel="stylesheet" href="{{ asset('css/plugins/dataTables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/dataTables/dataTables.responsive.css') }}">
@endSection

@section('css_select2')
<link rel="stylesheet" href="{{ asset('css/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/select2/select2-bootstrap4.min.css') }}">
@endSection

@section('css_summernote')
<link href="{{ asset('css/style-loader.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endSection

@section('page_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ trans('ecommerce.pedidos') }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">{{ trans('labels.inicio') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/pedidos/">{{ trans('ecommerce.pedidos') }}</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>{{ trans('labels.editar') }}</strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 white-bg">
                <div class="wrapper wrapper-content">
                    
                    <form role="form" name="FormRegistro" id="FormRegistro" action="{{route('pedidos.update',$pedido)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>{{ trans('labels.estado') }}</label>
                            <select class="form-control select2" id="Estado" name="Estado" style="width: 100%;">
                                <option selected disabled hidden value='-1'></option>
                                <?php foreach ($estados as $row) : ?>
                                    <option value="<?= $row->id ?>" {{ old('Estado', $pedido->estado_id) == $row->id ? 'selected' : '' }}><?= $row->nombre ?></option>
                                <?php endforeach; ?>
                            </select>
                            @error('Estado')
                            <span class="small text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <h1>{{ trans('ecommerce.observacionAdicional') }}</h1>
                        <div class="form-group">
                            <textarea id="ObservacionesAdicionales" rows="4" name="ObservacionesAdicionales" class="summernote">{{old('ObservacionesAdicionales', $pedido->informacion_adicional)}}</textarea>
                            @error('ObservacionesAdicionales')
                            <span class="small text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div>
                            <input id="PedidoId" name="PedidoId" type="hidden" value="{{ $pedido->id }}" />
                            <button type="submit" class="btn btn-primary">{{ trans('labels.actualizar') }}</button>
                        </div>
                    </form>

                    <h1>{{ trans('ecommerce.detallePedido') }}</h1>
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-6">
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.numero') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"><span class="">{{ 'PED-' . $pedido->id }}</span></dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.numProductos') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $pedido->total_productos }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.total') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"> {{ $pedido->monto_total }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.estado') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"> {{ $pedido->estado_nombre }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <div class="col-lg-6" id="cluster_info">

                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.ultimaActualizacion') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ \Carbon\Carbon::parse($pedido->updated_at)->isoFormat('DD MMM YYYY HH:mm') }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.fechaCreacion') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ \Carbon\Carbon::parse($pedido->created_at)->isoFormat('DD MMM YYYY HH:mm') }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.fechainicio') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ \Carbon\Carbon::parse($pedido->fecha_inicia_pedido)->isoFormat('DD MMM YYYY HH:mm') }}</dd>
                                        </div>
                                    </dl>
                                    @if (!empty($pedido->fecha_fin_pedido))
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.fechaFin') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ \Carbon\Carbon::parse($pedido->fecha_fin_pedido)->isoFormat('DD MMM YYYY HH:mm') }}</dd>
                                        </div>
                                    </dl>
                                    @endif
                                    
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    
                                    <dl class="row mb-0">
                                        <div class="col-sm-2 text-sm-right">
                                            <dt>{{ trans('ecommerce.observacionCliente') }}:</dt>
                                        </div>
                                        <div class="col-sm-10 text-sm-left">
                                            <dd>
                                                {{ $pedido->observacion_cliente }}
                                            </dd>
                                        </div>
                                    </dl>
                                    
                                </div>
                            </div>

                            <div class="row m-t-sm">
                                <div class="col-lg-12">
                                    <div class="panel blank-panel">
                                        <div class="panel-heading">
                                            <div class="panel-options">
                                                <ul class="nav nav-tabs">
                                                    <li><a class="nav-link active" href="#tab-1" data-toggle="tab">{{ trans('ecommerce.detallePedido') }}</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="panel-body">

                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab-1">
                                                    <div class="feed-activity-list">
                                                        <div class="feed-element">
                                                            
                                                            <table id="tableDataDetalle" class="table table-bordered table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ trans('ecommerce.cantidad') }}</th>
                                                                        <th>{{ trans('ecommerce.productos') }}</th>
                                                                        <th>{{ trans('ecommerce.marca') }}</th>
                                                                        <th>{{ trans('ecommerce.precio') }}</th>
                                                                        <th>{{ trans('ecommerce.valorTotal') }}</th>
                                                                        <th>{{ trans('labels.opciones') }}</th>
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
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div id="loader">
            <div id="center">
                <img src="{{ asset('/img/loading.gif') }}" />
            </div>
        </div>

    </div>
@endsection

@section('js_validation')
<script src="{{ asset('js/plugins/validate/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/plugins/validate/additional-methods.min.js') }}"></script>
@endSection

@section('js_datatable')
<script src="{{ asset('js/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/dataTables.responsive.js') }}"></script>
@endSection

@section('js_select2')
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
@endSection

@section('js_summernote')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="{{ asset('js/plugins/summernote/summernote-ext-print.js') }}"></script>
<script src="{{ asset('js/plugins/summernote/summernote-save-button.js') }}"></script>
<script src="{{ asset('js/plugins/summernote/summernote-ext-codewrapper.js') }}"></script>
@endSection

@section('script')
<script>
    $(function() {
        $('.select2').select2({placeholder :'{{trans('labels.seleccioneItem')}}', allowClear: true});

        $('#loader').hide();
        $('#ObservacionesAdicionales').summernote({
            prettifyHtml:false,
            focus: false,
            toolbar: [
                ['cleaner',['cleaner']],
                ['Style', ['style']],
                ['UndoRedo', ['undo', 'redo', 'clear']],
                ['Paragraph1', ['bold', 'italic', 'underline', 'strikethrough']],
                ['FontName', ['fontname', 'fontsize', 'color']],
                ['SuperSub', ['superscript', 'subscript']],
                ['Paragraph2', ['ol', 'ul', 'paragraph', 'height']],
                ['insert', ['picture', 'link', 'video', 'table', 'hr']],
                ['Fullscreen', ['fullscreen']],
                ['Codigo', ['highlight']],
                ['Print', ['print']],
                ['CodeView', ['codeview']],
                ['save', ['save']]
            ],
            placeholder: '',
            height: 300,
            tabsize: 2,
            dialogsInBody: true,
            lang: 'es-ES',
            callbacks: {
                onImageUpload: function(files) {
                    var $editor = $(this);
                    var data = new FormData();
                    data.append('image', files[0]);
                    data.append('source', 'orders');
                    //data.append('_token', "{{csrf_token()}}");
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{route('usuarioRecursos.storeImage')}}',
                        method: 'POST',
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#loader').show();
                        },
                        complete: function(){
                            $('#loader').hide();
                        },
                        success: function(response) {
                            $editor.summernote('insertImage', response);
                        },
                        error: function(){
                            $('#loader').hide();
                        }
                    });
                }
            }
        });

        $('#FormRegistro').validate({
            rules: {
                Estado: {
                    required: true,
                }
            },
            messages: {
                Estado: {
                    required: "{{ trans('validation.required', [ 'attribute' => 'Estado']) }}",
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        }).settings.ignore = ".note-editor *";

        /* Init Script Datatable */
        var titleExportDocument = "{{ trans('ecommerce.pedidos') }}";
        $("#tableDataDetalle").DataTable({
            'responsive': true,
            'bAutoWidth': false,
            'lengthChange': true,
            'autoWidth': false,
            //'ordering': false,
            'order': [[ 0, 'desc' ]],
            dom: '<"html5buttons"B>lTfgitp',
            'lengthMenu': [10, 50, 100, 200, 1000, 2000, 5000],
            'pageLength': 50,
            'ajax':"{{route('pedidoDetalles.data', ['PedidoId'=>$pedido->id])}}",
            'buttons': [{
                    extend: 'csv',
                    title: titleExportDocument
                },
                {
                    extend: 'excel',
                    title: titleExportDocument
                },
                {
                    extend: 'pdf',
                    title: titleExportDocument
                }, 'copy', 'print', 'colvis'
            ],
            'columns':[
                {data:'cantidad', width: 40},
                {data:'producto_nombre'},
                {data:'marca_nombre'},
                {data:'precio'},
                {data:'total'},
                {data:'opc', name: 'action', orderable:false, searchable:false, className: 'text-right', width: 40},
            ]
        });
        /* End Script Datatable */
        
    });
</script>

@endSection