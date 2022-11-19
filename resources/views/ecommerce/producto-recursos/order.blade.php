<?php 
    use App\Libraries\UtilEcommerce;
    $utilEcommerce = new UtilEcommerce();    
?>
@extends('layouts.main')
@section('page_title', trans('ecommerce.recursoProductos'))

@section('page_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Content Page header -->
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ trans('ecommerce.recursoProductos') . ': ' . $producto->nombre }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">{{ trans('labels.inicio') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('productos.index') }}">{{ trans('ecommerce.productos') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <strong><a href="{{ route('productos.edit', $producto) }}">{{ $producto->nombre }}</a></strong>
                </li>
                <li class="breadcrumb-item active">
                    {{ trans('labels.ordenar') }}
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 white-bg">
                <div class="wrapper wrapper-content">
                    
                    <div class="dd" id="nestable">
                        <ol class="dd-list">
                            @foreach ($productoRecursos as $row)
                                <li class="dd-item" data-id="{{$row->id}}">
                                    <div class="dd-handle"> 
                                        @if(!empty($row->archivo) && file_exists($utilEcommerce->getCurrentBaseEcommercePath() .'product/'.$producto->selector.'/'. $row->archivo))
                                            <a href="{{ asset($utilEcommerce->getCurrentBaseEcommercePath() . $row->archivo) }}" data-lightbox="image-1" data-title="{{ $row->nombre }}"><img alt="{{ $row->nombre }}" class="" width="50" src="{{ asset($utilEcommerce->getCurrentBaseEcommercePath() .'product/'.$producto->selector.'/'. $row->archivo ) }}"></a>
                                        @endif
                                        {{$row->nombre}}
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>
        
                    <div class="m-t-md">
                        <div id="contentResult"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_order_items')
<script src="{{ asset('js/plugins/nestable/jquery.nestable.js') }}"></script>
@endSection

@section('script')
<script>
    $(function() {

        var updateOutput = function(e) {
            var list = e.length ? e : $(e.target),
                output = list.data('output');

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: "{{route('productoRecursos.updateOrder')}}",
                method: "POST",
                data: {
                    order: list.nestable('serialize')
                },
                success: function(result) {
                    $("#contentResult").html(result);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert("Unable to save new list order: " + errorThrown);
            });
        };

        $('#nestable').nestable({
            group: 1,
            //maxDepth: 7,
        }).on('change', updateOutput);

    });
</script>

@endSection