@extends('layouts.main')
@section('page_title', trans('ecommerce.productos'))

@section('css_datatable')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
<link rel="stylesheet" href="{{ asset('css/plugins/dataTables/dataTables.responsive.css') }}">
@endSection

@section('css_lightbox2')
<link rel="stylesheet" href="{{ asset('css/plugins/lightbox2/css/lightbox.min.css') }}">
@endsection

@section('page_content')
    <!-- Content Page header -->
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ trans('ecommerce.oferta') }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">{{ trans('labels.inicio') }}</a>
                </li>
                <li class="breadcrumb-item active">
                    <a href="{{ route('productos.index') }}">{{ trans('ecommerce.productos') }}</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>{{ trans('ecommerce.oferta') }}</strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 white-bg">
                <div class="wrapper wrapper-content">
                    <p>
                        <a href="{{ route('marcaProductos.create') }}" class="btn btn-white btn-bitbucket">
                            <i class="fa fa-plus-circle"></i> {{ trans('labels.nuevo') }}
                        </a>
                        <a href="{{ route('productos.orderOfert') }}" class="btn btn-white btn-bitbucket">
                            <i class="fa fa-sort-alpha-asc"></i> {{ trans('labels.ordenar') }}
                        </a>
                    </p>
                    
                    <table id="tableData" class="table table-bordered table-striped" width="100%">
                        <thead>
                            <tr>
                                <th>{{ trans('labels.imagen') }}</th>
                                <th>{{ trans('labels.nombre') }}</th>
                                <th>{{ trans('labels.codigo') }}</th>
                                <th>{{ trans('ecommerce.precioVentaPublico') }}</th>
                                <th>{{ trans('ecommerce.marca') }}</th>
                                <th>{{ trans('labels.estado') }}</th>
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

@endsection

@section('js_datatable')
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
<script src="{{ asset('js/plugins/datatables/dataTables.responsive.js') }}"></script>
@endSection

@section('js_lightbox2')
<script src="{{ asset('js/plugins/lightbox2/lightbox.min.js') }}"></script>
@endSection

@section('script')
<script>
    
    $(document).ready(function() {

        /* Init Script Datatable */
        var titleExportDocument = "{{ trans('ecommerce.productos') }}";
        $("#tableData").DataTable({
            'responsive': true,
            processing: true,
            serverSide: true,
            dom: '<"html5buttons"B>lTfgitp',
            'lengthMenu': [10, 50, 100, 200, 1000, 2000, 5000],
            'pageLength': 50,
            'ajax':"{{route('productos.dataOfert')}}",
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
                {data:'imagen_portada'},
                {data:'nombre'},
                {data:'codigo'},
                {data:'precio'},
                {data:'marca_nombre'},
                {data:'estado'},
                {data:'opciones', name: 'action', orderable:false, searchable:false, className: 'text-right', width: 40},
            ]
        });
        /* End Script Datatable */

    });

    
</script>

@endSection