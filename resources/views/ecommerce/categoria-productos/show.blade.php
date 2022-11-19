<?php 
    use App\Libraries\UtilEcommerce;
    $utilEcommerce = new UtilEcommerce();
?>
@extends('layouts.main')
@section('page_title', trans('ecommerce.categoriaProductos'))

@section('css_lightbox2')
<link rel="stylesheet" href="{{ asset('css/plugins/lightbox2/css/lightbox.min.css') }}">
@endsection

@section('page_content')
    <!-- Content Page header -->
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ trans('ecommerce.categoriaProductos') }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">{{ trans('labels.inicio') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('categoriaProductos.index') }}">{{ trans('ecommerce.categoriaProductos') }}</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>{{$categoriaProducto->nombre}}</strong>
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
                                            if ($categoriaProducto->enabled) {
                                                $tooltipActivo = trans('labels.activo');
                                                $imageEstado = "fa fa-check";
                                                $labelEnabled = "label-primary";
                                            }
                                        ?>
                                        <span class="spacerMenu float-right m-l-xs" data-toggle="tooltip" data-placement="top" title="{{ trans('labels.eliminar') }}"><a href="#" data-href="{{ route('categoriaProductos.delete', $categoriaProducto) }}" data-toggle="modal" data-target="#modal-confirm-delete" data-placement="top" data-name="{{ $categoriaProducto->nombre }}" class="btn btn-danger"><i class="fa fa-trash"></i> {{ trans('labels.eliminar') }}</a></span> 
                                        <span class="spacerMenu float-right m-l-xs" data-toggle="tooltip" data-placement="top" title="{{ trans('labels.editar') }}"><a href="{{ route('categoriaProductos.edit', $categoriaProducto) }}" class="btn btn-default"><i class="fa fa-pencil"></i> {{ trans('labels.editar') }}</a></span>
                                        <span class="spacerMenu float-right m-l-xs" data-toggle="tooltip" data-placement="top" title="{{ $tooltipActivo }}"><a href="{{route('categoriaProductos.enabled', $categoriaProducto)}}" class="btn btn-default {{ $labelEnabled }}"><i class="{{ $imageEstado }}"></i> {{ $tooltipActivo }}</a></span> 
                                        <h2>{{$categoriaProducto->nombre}}</h2>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.estado') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"><span class="label {{ $labelEnabled }}">{{ $tooltipActivo }}</span></dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.slug') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"> {{ $categoriaProducto->slug }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.id') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"> {{ $categoriaProducto->selector }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('ecommerce.productos') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $categoriaProducto->total_productos }}</dd>
                                        </div>
                                    </dl>
                                    @if (!empty($categoriaProducto->url_imagen))
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.urlImagen') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1"><a href="{{ $categoriaProducto->url_imagen }}" target="_blank">{{ trans('labels.abrir') }}</a></dd>
                                        </div>
                                    </dl>
                                    @endif
                                    @if (!empty($categoriaProducto->url_imagen_thum))
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.urlImagenThum') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ $categoriaProducto->url_imagen_thum }}</dd>
                                        </div>
                                    </dl>
                                    @endif
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.imagen') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="project-people mb-1">
                                                @if(!empty($categoriaProducto->imagen) && file_exists(public_path($utilEcommerce->getCurrentBaseEcommercePath(). 'category/').$categoriaProducto->imagen) )
                                                    <a href="{{ URL::to('/') . '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'category/' . $categoriaProducto->imagen }}" data-lightbox="image-1" data-title="{{ $categoriaProducto->nombre }}"><img alt="{{ $categoriaProducto->nombre }}" class="img-lg" src="{{ URL::to('/') . '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'category/' . $categoriaProducto->imagen }}"></a>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>

                                </div>
                                <div class="col-lg-6" id="cluster_info">

                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.ultimaActualizacion') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ \Carbon\Carbon::parse($categoriaProducto->updated_at)->isoFormat('DD MMM YYYY HH:mm') }}</dd>
                                        </div>
                                    </dl>
                                    <dl class="row mb-0">
                                        <div class="col-sm-4 text-sm-right">
                                            <dt>{{ trans('labels.fechaCreacion') }}:</dt>
                                        </div>
                                        <div class="col-sm-8 text-sm-left">
                                            <dd class="mb-1">{{ \Carbon\Carbon::parse($categoriaProducto->created_at)->isoFormat('DD MMM YYYY HH:mm') }}</dd>
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
                                                {{ $categoriaProducto->resumen }}
                                            </dd>
                                        </div>
                                    </dl>
                                    
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

@section('js_lightbox2')
<script src="{{ asset('js/plugins/lightbox2/lightbox.min.js') }}"></script>
@endSection

@section('script')
<script>
    $(function() {
        
    });
</script>

@endSection