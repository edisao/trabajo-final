<?php 
    use App\Libraries\UtilEcommerce;
    $utilEcommerce = new UtilEcommerce();    
?>
@extends('layouts.main')
@section('page_title', trans('ecommerce.productos'))

@section('css_datatable')
<link rel="stylesheet" href="{{ asset('css/plugins/dataTables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/dataTables/dataTables.responsive.css') }}">
@endSection

@section('css_lightbox2')
<link rel="stylesheet" href="{{ asset('css/plugins/lightbox2/css/lightbox.min.css') }}">
@endsection

@section('page_content')
    <!-- Content Page header -->
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ trans('ecommerce.productos') }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">{{ trans('labels.inicio') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/productos/">{{ trans('ecommerce.productos') }}</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>{{$producto->nombre}}</strong>
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
                                        <?php
                                            $imageEstado = "fa fa-times";
                                            $labelEnabled = "label-warning";
                                            $tooltipActivo = trans('labels.inactivo');
                                            if ($producto->enabled) {
                                                $tooltipActivo = trans('labels.activo');
                                                $imageEstado = "fa fa-check";
                                                $labelEnabled = "label-primary";
                                            }
                                        ?>
                                        <span class="spacerMenu float-right m-l-xs" data-toggle="tooltip" data-placement="top" title="{{ trans('labels.eliminar') }}"><a href="#" data-href="{{ route('productos.delete', $producto) }}" data-toggle="modal" data-target="#modal-confirm-delete" data-placement="top" data-name="{{ $producto->nombre }}" class="btn btn-danger"><i class="fa fa-trash"></i> {{ trans('labels.eliminar') }}</a></span> 
                                        <span class="spacerMenu float-right m-l-xs" data-toggle="tooltip" data-placement="top" title="{{ trans('labels.editar') }}"><a href="{{ route('productos.edit', $producto) }}" class="btn btn-default"><i class="fa fa-pencil"></i> {{ trans('labels.editar') }}</a></span>
                                        <span class="spacerMenu float-right m-l-xs" data-toggle="tooltip" data-placement="top" title="{{ $tooltipActivo }}"><a href="{{route('productos.enabled', $producto)}}" class="btn btn-default {{ $labelEnabled }}"><i class="{{ $imageEstado }}"></i> {{ $tooltipActivo }}</a></span> 
                                        <h2>{{$producto->nombre}}</h2>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    @if (empty($producto->marca_nombre) || empty($producto->resumen) || empty($producto->categoria_nombre) || empty($producto->imagen_portada) || ($producto->mostrar_precio && (bccomp($producto->pvp, 0, 2) == 0)) )
                                        <h1>{{ trans('labels.faltaConfigurar') }}</h1>
                                        @if (empty($producto->imagen_portada))
                                            <span class="label label-danger">{{ trans('ecommerce.imagenPrincipalProducto') }}</span>    
                                        @endif
                                        @if (empty($producto->resumen))
                                            <span class="label label-danger">{{ trans('labels.resumen') }}</span>    
                                        @endif
                                        @if (empty($producto->marca_nombre))
                                            <span class="label label-danger">{{ trans('ecommerce.marca') }}</span>    
                                        @endif
                                        @if (empty($producto->categoria_nombre))
                                            <span class="label label-danger">{{ trans('ecommerce.categoriaProductos') }}</span>    
                                        @endif
                                        @if ($producto->mostrar_precio)
                                            @if (bccomp($producto->pvp, 0, 2) == 0 )
                                            <span class="label label-danger">{{ trans('ecommerce.precio') }}</span>
                                            @endif
                                        @endif
                                    @endif
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt></dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"></dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.mostrarPrecio') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                @if ($producto->mostrar_precio)
                                                <span class="label label-primary">{{ trans('labels.si') }}</span>
                                                @else
                                                <span class="label label-danger">{{ trans('labels.no') }}</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.precioVentaPublico') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"> <h3>{{ $producto->pvp }}</h3></dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.precioIncluyeImpuestos') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                @if ($producto->incluye_impuestos)
                                                <span class="label label-primary">{{ trans('labels.si') }}</span>
                                                @else
                                                <span class="label label-warning">{{ trans('labels.no') }}</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.mostrarPrecioAnterior') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                @if ($producto->pvp_anterior)
                                                <span class="label label-primary">{{ trans('labels.si') }}</span>
                                                @else
                                                <span class="label label-warning">{{ trans('labels.no') }}</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.precioVentaAnterior') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"> <h3>{{ $producto->pvp_anterior }}</h3></dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.estado') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                @if ($producto->published)
                                                <span class="label label-primary">{{ trans('labels.publicado') }}</span>
                                                @else
                                                <span class="label label-warning">{{ trans('labels.borrador') }}</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.id') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"> {{ $producto->selector }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.nombreAlterno') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $producto->nombre_alterno }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.slug') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $producto->slug }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.sku') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $producto->sku }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.codigo') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $producto->codigo }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.serie') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $producto->serie }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.stock') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $producto->stock }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.oferta') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                @if ($producto->en_oferta)
                                                <span class="label label-primary">{{ trans('labels.si') }}</span>
                                                @else
                                                <span class="label label-warning">{{ trans('labels.no') }}</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.slider') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                @if ($producto->mostrar_slider)
                                                <span class="label label-primary">{{ trans('labels.si') }}</span>
                                                @else
                                                <span class="label label-warning">{{ trans('labels.no') }}</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.comentarios') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                @if ($producto->registra_comentarios)
                                                <span class="label label-primary">{{ trans('labels.si') }}</span>
                                                @else
                                                <span class="label label-warning">{{ trans('labels.no') }}</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>

                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.marca') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $producto->marca_nombre }}</dd>
                                        </div>
                                    </dl>
                                    
                                </div>
                                <div class="col-lg-6" id="cluster_info">
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt></dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"><span class="label {{ $labelEnabled }}">{{ $tooltipActivo }}</span></dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.ultimaActualizacion') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ \Carbon\Carbon::parse($producto->updated_at)->isoFormat('DD MMM YYYY HH:mm') }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.fechaCreacion') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ \Carbon\Carbon::parse($producto->created_at)->isoFormat('DD MMM YYYY HH:mm') }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.fechaPublicacion') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                @if (!empty($producto->published_at))
                                                {{ \Carbon\Carbon::parse($producto->published_at)->isoFormat('DD MMM YYYY HH:mm') }}
                                                @endif
                                                
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.categoriaProductos') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                {{ $producto->categoria_nombre }}
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.urlInformacionAdicional') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                {{ $producto->url_informacion_adicional }}
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.imagenPrincipalProducto') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                @if(!empty($producto->imagen_portada) && file_exists(public_path($utilEcommerce->getCurrentBaseEcommercePath() ). 'product/' . $producto->selector . '/' . $producto->imagen_portada) )
                                                    <a href="{{ URL::to('/') . '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/' . $producto->imagen_portada }}" data-lightbox="image-1" data-title="{{ $producto->nombre }}"><img alt="{{ $producto->nombre }}" class="" width="80" src="{{ URL::to('/') . '/' . $utilEcommerce->getCurrentBaseEcommercePath(). 'product/' . $producto->selector . '/' .$producto->imagen_portada }}"></a>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.largo') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                {{ $producto->longitud . ' ' . $producto->medida_nombre }}
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.ancho') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                {{ $producto->ancho . ' ' . $producto->medida_nombre }}
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.alto') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                {{ $producto->alto . ' ' . $producto->medida_nombre }}
                                            </dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.peso') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">
                                                {{ $producto->peso . ' ' . $producto->peso_nombre }}
                                            </dd>
                                        </div>
                                    </dl>
                                    
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-12">
                                    
                                    <dl class="row mb-0">
                                        <div class="col-sm-2 text-sm-right">
                                            <dt>{{ trans('labels.resumen') }}:</dt>
                                        </div>
                                        <div class="col-sm-10 text-sm-left">
                                            <dd>
                                                {{ $producto->resumen }}
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
                                                    <li><a class="nav-link active" href="#tab-1" data-toggle="tab">{{ trans('labels.detalle') }}</a></li>
                                                    <li><a class="nav-link" href="#tab-2" data-toggle="tab">{{ trans('labels.imagen') }}</a></li>
                                                    <li><a class="nav-link" href="#tab-3" data-toggle="tab">{{ trans('ecommerce.categoriaProductos') }}</a></li>
                                                    <li><a class="nav-link" href="#tab-4" data-toggle="tab">{{ trans('ecommerce.productosRelacionados') }}</a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="panel-body">

                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tab-1">
                                                    <div class="feed-activity-list">
                                                        <div class="feed-element">
                                                            {!! $producto->detalle !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="tab-2">
                                                    <div class="feed-activity-list">
                                                        <div class="feed-element">
                                                            <table id="tableDataImagen" class="table table-bordered table-striped" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ trans('labels.imagen') }}</th>
                                                                        <th>{{ trans('labels.nombre') }}</th>
                                                                        <th>{{ trans('labels.descripcion') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="tab-3">
                                                    <div class="feed-activity-list">
                                                        <div class="feed-element">
                                                            <table id="tableDataCategorias" class="table table-bordered table-striped" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ trans('labels.nombre') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="tab-4">
                                                    <div class="feed-activity-list">
                                                        <div class="feed-element">
                                                            <table id="tableProductosRelacionados" class="table table-bordered table-striped" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ trans('labels.nombre') }}</th>
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

@section('js_lightbox2')
<script src="{{ asset('js/plugins/lightbox2/lightbox.min.js') }}"></script>
@endSection


@section('script')
<script>
    $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-white btn-sm';
    $(function() {
        /* Init Script Datatable */
        var titleExportDocument = "{{ trans('labels.imagen') }}";
        $("#tableDataImagen").DataTable({
            'responsive': true,
            'bAutoWidth': false,
            'lengthChange': true,
            'autoWidth': false,
            'ordering': false,
            //'order': [[ 0, 'desc' ]],
            dom: '<"html5buttons"B>lTfgitp',
            'lengthMenu': [10, 50, 100, 200, 1000, 2000, 5000],
            'pageLength': 50,
            'ajax':"{{route('productoRecursos.data', ['productoId' => $producto->id] )}}",
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
                }, 'copy', 'print',
            ],
            'columns':[
                {data:'preview', width: 100},
                {data:'nombre'},
                {data:'descripcion'},
            ]
        });
        /* End Script Datatable */

        /* Init Script Datatable */
        var titleExportDocument = "{{ trans('labels.imagen') }}";
        $("#tableDataCategorias").DataTable({
            'responsive': true,
            'bAutoWidth': false,
            'lengthChange': true,
            'autoWidth': false,
            'ordering': false,
            //'order': [[ 0, 'desc' ]],
            dom: '<"html5buttons"B>lTfgitp',
            'lengthMenu': [10, 50, 100, 200, 1000, 2000, 5000],
            'pageLength': 50,
            'ajax':"{{route('productoCategorias.data', ['productoId' => $producto->id] )}}",
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
                }, 'copy', 'print',
            ],
            'columns':[
                {data:'nombre'},
            ]
        });
        /* End Script Datatable */
        
        /* Init Script Datatable */
        var titleExportDocument = "{{ trans('labels.imagen') }}";
        $("#tableProductosRelacionados").DataTable({
            'responsive': true,
            'bAutoWidth': false,
            'lengthChange': true,
            'autoWidth': false,
            'ordering': false,
            //'order': [[ 0, 'desc' ]],
            dom: '<"html5buttons"B>lTfgitp',
            'lengthMenu': [10, 50, 100, 200, 1000, 2000, 5000],
            'pageLength': 50,
            'ajax':"{{route('productoRelaciones.data', ['productoId' => $producto->id] )}}",
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
                }, 'copy', 'print',
            ],
            'columns':[
                {data:'producto_nombre'},
            ]
        });
        /* End Script Datatable */

    });
</script>

@endSection