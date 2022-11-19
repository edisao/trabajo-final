@extends('layouts.main')
@section('page_title', trans('ecommerce.pedidos'))

@section('css_datatable')
<link rel="stylesheet" href="{{ asset('css/plugins/dataTables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/dataTables/dataTables.responsive.css') }}">
@endSection

@section('page_content')
    <!-- Content Page header -->
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ trans('ecommerce.detallePedido') }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">{{ trans('labels.inicio') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('pedidos.index') }}">{{ trans('ecommerce.pedidos') }}</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>{{$pedido->nombres . ' ' . $pedido->apellidos }}</strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 white-bg">
                <div class="wrapper wrapper-content">
                    
                    <div class="ibox">
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="m-b-md">
                                        <span class="spacerMenu float-right m-l-xs" data-toggle="tooltip" data-placement="top" title="{{ trans('labels.eliminar') }}"><a href="#" data-href="{{ route('pedidos.delete', $pedido) }}" data-toggle="modal" data-target="#modal-confirm-delete" data-placement="top" data-name="{{ $pedido->nombres . ' ' . $pedido->apellidos }}" class="btn btn-danger"><i class="fa fa-trash"></i> {{ trans('labels.eliminar') }}</a></span> 
                                        <span class="spacerMenu float-right m-l-xs" data-toggle="tooltip" data-placement="top" title="{{ trans('labels.editar') }}"><a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-default"><i class="fa fa-pencil"></i> {{ trans('labels.editar') }}</a></span>
                                        <h2>{{ $pedido->nombres . ' ' . $pedido->apellidos }}</h2>
                                    </div>

                                </div>
                            </div>
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
    </div>

    @include('layouts.partials.delete-confirm')

@endsection

@section('js_datatable')
<script src="{{ asset('js/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/dataTables.responsive.js') }}"></script>
@endSection

@section('script')
<script>
    $(function() {
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