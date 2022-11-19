@extends('layouts.main')
@section('page_title', trans('msg.msgEditarInformacion') )

@section('css_summernote')
<link href="{{ asset('css/style-loader.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endSection

@section('css_select2')
<link rel="stylesheet" href="{{ asset('css/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/select2/select2-bootstrap4.min.css') }}">
@endSection

@section('css_datatable')
<link rel="stylesheet" href="{{ asset('css/plugins/dataTables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/dataTables/dataTables.responsive.css') }}">

<link rel="stylesheet" href="{{ asset('css/plugins/dualListbox/bootstrap-duallistbox.min.css') }}">

@endSection

@section('css_lightbox2')
<link rel="stylesheet" href="{{ asset('css/plugins/lightbox2/css/lightbox.min.css') }}">
@endsection

@section('page_content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ trans('ecommerce.productos') }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">{{ trans('labels.inicio') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('productos.index') }}">{{ trans('ecommerce.productos') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('productos.show', $producto->selector) }}">{{ $producto->nombre }}</a>
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
                <div class="card card-primary card-outline card-outline-tabs">

                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs">
                            <li><a class="nav-link {{ empty($tabName) || $tabName == 'tab-1' ? 'active' : '' }}" href="#tab-1" data-toggle="tab">{{ trans('labels.informacion') }}</a></li>
                            <li><a class="nav-link {{ !empty($tabName) && $tabName == 'tab-2' ? 'active' : '' }}" href="#tab-2" data-toggle="tab">{{ trans('labels.imagen') }}</a></li>
                            <li><a class="nav-link {{ !empty($tabName) && $tabName == 'tab-3' ? 'active' : '' }}" href="#tab-3" data-toggle="tab">{{ trans('ecommerce.categoriaProductos') }}</a></li>
                            <li><a class="nav-link {{ !empty($tabName) && $tabName == 'tab-4' ? 'active' : '' }}" href="#tab-4" data-toggle="tab">{{ trans('ecommerce.dimensionesPeso') }}</a></li>
                            <li><a class="nav-link {{ !empty($tabName) && $tabName == 'tab-5' ? 'active' : '' }}" href="#tab-5" data-toggle="tab">{{ trans('ecommerce.productosRelacionados') }}</a></li>
                        </ul>
                    </div>

                    <div class="card-body">

                        <div class="tab-content" id="custom-tabsContent">
                            <div class="tab-pane {{ empty($tabName) || $tabName == 'tab-1' ? 'active' : '' }}" id="tab-1">
                                <div class="feed-activity-list">
                                    <div class="feed-element">
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
                                        
                                        
                                        <form role="form" name="FormRegistro" id="FormRegistro" class="form-horizontal" action="{{route('productos.update',$producto)}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            @if (!empty($producto->marca_nombre) && !empty($producto->resumen) && !empty($producto->categoria_nombre) && !empty($producto->imagen_portada) )
                                                @if ($producto->mostrar_precio && (bccomp($producto->pvp, 0, 2) > 0 ) )
                                                    <h1>{{ trans('labels.publicado') }}</h1>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="PublicarProducto" name="PublicarProducto" @if(old('PublicarProducto', $producto->published)) checked @endif>
                                                        <label class="form-check-label" for="PublicarProducto">{{ trans('ecommerce.publicarProducto') }}</label>
                                                    </div>
                                                @endif
                                            @endif

                                            <h1>{{ trans('labels.informacion') }}</h1>
                                            <div class="row pb-3">
                                                <div class="col-md-6">
                                                    <label for="Nombre">{{ trans('labels.nombre') }}</label>
                                                    <input type="text" class="form-control" id="Nombre" name="Nombre" value="{{old('Nombre', $producto->nombre )}}" maxlength="300" autocomplete="off" placeholder="" required>
                                                    @error('Nombre')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="NombreAlterno">{{ trans('labels.nombreAlterno') }}</label>
                                                    <input type="text" class="form-control" id="NombreAlterno" name="NombreAlterno" value="{{old('NombreAlterno', $producto->nombre_alterno )}}" maxlength="300" autocomplete="off" placeholder="">
                                                    @error('NombreAlterno')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="Sku">{{ trans('ecommerce.sku') }}</label>
                                                <input type="text" class="form-control" id="Sku" name="Sku" value="{{old('Sku', $producto->sku)}}" maxlength="150" autocomplete="off" placeholder="">
                                                @error('Sku')
                                                <span class="small text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                            
                                            <div class="row pb-3">
                                                <div class="col-md-6">
                                                    <label for="Codigo">{{ trans('labels.codigo') }}</label>
                                                    <input type="text" class="form-control" id="Codigo" name="Codigo" value="{{old('Codigo', $producto->codigo)}}" maxlength="200" autocomplete="off" placeholder="" >
                                                    @error('Codigo')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="Serie">{{ trans('labels.serie') }}</label>
                                                    <input type="text" class="form-control" id="Serie" name="Serie" value="{{old('Serie', $producto->serie)}}" maxlength="200" autocomplete="off" placeholder="" >
                                                    @error('Serie')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="row pb-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('ecommerce.marca') }}</label>
                                                        <select class="form-control select2" id="Marca" name="Marca" style="width: 100%;">
                                                            <option selected disabled hidden value='-1'></option>
                                                            <?php foreach ($marcas as $row) : ?>
                                                                <option value="<?= $row->id ?>" {{ old('Marca', $producto->marca_id) == $row->id ? 'selected' : '' }}><?= $row->nombre ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        @error('Marca')
                                                        <span class="small text-danger">{{$message}}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="RegistraComentarios" name="RegistraComentarios" @if(old('RegistraComentarios', $producto->registra_comentarios)) checked @endif>
                                                <label class="form-check-label" for="RegistraComentarios">{{ trans('labels.comentarios') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="MostrarSlider" name="MostrarSlider" @if(old('MostrarSlider', $producto->mostrar_slider)) checked @endif>
                                                <label class="form-check-label" for="MostrarSlider">{{ trans('ecommerce.slider') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="Oferta" name="Oferta" @if(old('Oferta', $producto->en_oferta)) checked @endif>
                                                <label class="form-check-label" for="Oferta">{{ trans('ecommerce.oferta') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="MostrarPrecio" name="MostrarPrecio" @if(old('MostrarPrecio', $producto->mostrar_precio)) checked @endif>
                                                <label class="form-check-label" for="MostrarPrecio">{{ trans('ecommerce.mostrarPrecio') }}</label>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <h1>{{ trans('ecommerce.precio') }}</h1>
                                            <div class="form-group">
                                                <label for="PrecioVentaPublico">{{ trans('ecommerce.precioVentaPublico') }}</label>
                                                <input type="text" class="form-control" id="PrecioVentaPublico" name="PrecioVentaPublico" value="{{old('PrecioVentaPublico', $producto->pvp)}}" maxlength="50" autocomplete="off" placeholder="">
                                                @error('PrecioVentaPublico')
                                                <span class="small text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="IncluyeImpuestos" name="IncluyeImpuestos" @if(old('IncluyeImpuestos', $producto->incluye_impuestos)) checked @endif>
                                                <label class="form-check-label" for="IncluyeImpuestos">{{ trans('ecommerce.precioIncluyeImpuestos') }}</label>
                                            </div>
                                            <div class="form-check pb-3">
                                                <input type="checkbox" class="form-check-input" id="MostrarPrecioVentaAnterior" name="MostrarPrecioVentaAnterior" @if(old('MostrarPrecioVentaAnterior', $producto->mostrar_pvp_anterior)) checked @endif>
                                                <label class="form-check-label" for="MostrarPrecioVentaAnterior">{{ trans('ecommerce.mostrarPrecioAnterior') }}</label>
                                            </div>
                                            <div class="form-group">
                                                <label for="PrecioVentaAnterior">{{ trans('ecommerce.precioVentaAnterior') }}</label>
                                                <input type="text" class="form-control" id="PrecioVentaAnterior" name="PrecioVentaAnterior" value="{{old('PrecioVentaAnterior', $producto->pvp_anterior)}}" maxlength="50" autocomplete="off" placeholder="">
                                                @error('PrecioVentaAnterior')
                                                <span class="small text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                            <h1>{{ trans('labels.resumen') }}</h1>
                                            <div class="form-group">
                                                <small><div id="charNum">500 caracteres m&aacute;ximo.</div></small>
                                                <textarea id="Resumen" rows="4" name="Resumen" wrap="physical" maxlength="500" class="form-control">{{old('Resumen', $producto->resumen)}}</textarea>
                                                @error('Resumen')
                                                <span class="small text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                            <h1>{{ trans('labels.detalle') }}</h1>
                                            <div class="form-group">
                                                <textarea id="Detalle" rows="6" name="Detalle" class="summernote">{{old('Detalle', $producto->detalle)}}</textarea>
                                                @error('Detalle')
                                                <span class="small text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                            
                                            <div>
                                                <button type="submit" class="btn btn-primary">{{ trans('labels.actualizar') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane {{ !empty($tabName) && $tabName == 'tab-2' ? 'active' : '' }}" id="tab-2">
                                <div class="feed-activity-list">
                                    <div class="feed-element">
                                        <p>
                                            <a href="{{ route('productoRecursos.order', $producto) }}" class="btn btn-white btn-bitbucket">
                                                <i class="fa fa-sort-alpha-asc"></i> {{ trans('labels.ordenar') }}
                                            </a>
                                        </p>
                                        <form role="form" name="FormRegistroImagen" id="FormRegistroImagen" class="form-horizontal" action="{{route('productoRecursos.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('POST')
                                            <h1>{{ trans('labels.imagen') }}</h1>
                                            <div class="form-group">
                                                <div class="custom-file">
                                                    <input id="Imagenes[]" name="Imagenes[]" class="custom-file-input" type="file" accept="image/*" value="{{old('Imagen')}}" multiple>
                                                    <label for="Imagen" class="custom-file-label">{{ trans('labels.seleccioneImagen') }}...</label>
                                                    @error('Imagen')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ trans('labels.tipos') }}</label>
                                                <select class="form-control select2" id="Tipo" name="Tipo" style="width: 100%;">
                                                    <option selected disabled hidden value='-1'></option>
                                                    <?php foreach ($tiposImagen as $tipo) : ?>
                                                        <option value="{{$tipo->id}}">{{$tipo->nombre}}</option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="small text-danger"><?= session('errors.Tipo') ?></span>
                                            </div>
                                            <div>
                                                <input id="ImagenProductoId" name="ImagenProductoId" type="hidden" value="{{ $producto->id }}" />
                                                <button type="submit" class="btn btn-primary">{{ trans('labels.registrar') }}</button>
                                            </div>
                                        </form>
                                        <div class="hr-line-dashed"></div>
                                        <table id="tableDataImagen" class="table table-bordered table-striped" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('labels.imagen') }}</th>
                                                    <th>{{ trans('labels.nombre') }}</th>
                                                    <th>{{ trans('labels.tipos') }}</th>
                                                    <th>{{ trans('labels.descripcion') }}</th>
                                                    <th>{{ trans('labels.visible') }}</th>
                                                    <th>{{ trans('labels.opciones') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>

                                    </div>                                    
                                </div>
                            </div>
                            <div class="tab-pane {{ !empty($tabName) && $tabName == 'tab-3' ? 'active' : '' }}" id="tab-3">
                                <div class="feed-activity-list">
                                    <div class="feed-element">
                                        <form role="form" name="FormRegistroCategoria" id="FormRegistroCategoria" class="form-horizontal" action="{{route('productoCategorias.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('POST')
                                            
                                            <div class="form-group">
                                                <label>{{ trans('ecommerce.categoriaProductos') }}</label>
                                                <select class="form-control select2" id="Categoria" name="Categoria" style="width: 100%;">
                                                    <option selected disabled hidden value='-1'></option>
                                                    <?php foreach ($categorias as $row) : ?>
                                                        <option value="<?= $row->id ?>">{{$row->nombre}}</option>
                                                    <?php endforeach; ?>
                                                </select>
                                                @error('Categorias')
                                                    <span class="small text-danger">{{$message}}</span>
                                                @enderror
                                            </div>
                                            
                                            <div>
                                                <input id="CategoriaProductoId" name="CategoriaProductoId" type="hidden" value="{{ $producto->id }}" />
                                                <button type="submit" class="btn btn-primary">{{ trans('labels.registrar') }}</button>
                                            </div>
                                        </form>
                                        <table id="tableDataCategorias" class="table table-bordered table-striped" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>{{ trans('labels.nombre') }}</th>
                                                    <th>{{ trans('labels.opciones') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="tab-pane {{ !empty($tabName) && $tabName == 'tab-4' ? 'active' : '' }}" id="tab-4">
                                <div class="feed-activity-list">
                                    <div class="feed-element">

                                        <form role="form" name="FormRegistroMedidasPeso" id="FormRegistroMedidasPeso" class="form-horizontal" action="{{route('productos.updateMedidas',$producto)}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <h1>{{ trans('ecommerce.medidas') }}</h1>
                                            <div class="row pb-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('ecommerce.unidadMedida') }}</label>
                                                        <select class="form-control select2" id="UnidadMedida" name="UnidadMedida" style="width: 100%;">
                                                            <option selected disabled hidden value='-1'></option>
                                                            <?php foreach ($medidas as $row) : ?>
                                                                <option value="<?= $row->id ?>" {{ old('UnidadMedida', $producto->unidad_medida_id) == $row->id ? 'selected' : '' }}><?= $row->nombre ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        @error('UnidadMedida')
                                                        <span class="small text-danger">{{$message}}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="Largo">{{ trans('ecommerce.largo') }}</label>
                                                    <input type="text" class="form-control" id="Largo" name="Largo" value="{{old('Largo', $producto->longitud )}}" maxlength="300" autocomplete="off" placeholder="">
                                                    @error('Largo')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="row pb-3">
                                                <div class="col-md-6">
                                                    <label for="Ancho">{{ trans('ecommerce.ancho') }}</label>
                                                    <input type="text" class="form-control" id="Ancho" name="Ancho" value="{{old('Ancho', $producto->ancho)}}" maxlength="200" autocomplete="off" placeholder="" >
                                                    @error('Ancho')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="Alto">{{ trans('ecommerce.alto') }}</label>
                                                    <input type="text" class="form-control" id="Alto" name="Alto" value="{{old('Alto', $producto->alto)}}" maxlength="200" autocomplete="off" placeholder="" >
                                                    @error('Alto')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <h1>{{ trans('ecommerce.peso') }}</h1>
                                            <div class="row pb-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('ecommerce.peso') }}</label>
                                                        <select class="form-control select2" id="UnidadPeso" name="UnidadPeso" style="width: 100%;">
                                                            <option selected disabled hidden value='-1'></option>
                                                            <?php foreach ($pesos as $row) : ?>
                                                                <option value="<?= $row->id ?>" {{ old('UnidadPeso', $producto->unidad_peso_id) == $row->id ? 'selected' : '' }}><?= $row->nombre ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        @error('UnidadPeso')
                                                        <span class="small text-danger">{{$message}}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="Peso">{{ trans('ecommerce.peso') }}</label>
                                                    <input type="text" class="form-control" id="Peso" name="Peso" value="{{old('Peso', $producto->peso)}}" maxlength="200" autocomplete="off" placeholder="" >
                                                    @error('Peso')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <h1>{{ trans('ecommerce.unidadesPorCaja') }}</h1>
                                            <div class="row pb-3">
                                                <div class="col-md-6">
                                                    <label for="UnidadesPorCaja">{{ trans('ecommerce.unidadesPorCaja') }}</label>
                                                    <input type="text" class="form-control" id="UnidadesPorCaja" name="UnidadesPorCaja" value="{{old('UnidadesPorCaja', $producto->unidades_caja)}}" maxlength="200" autocomplete="off" placeholder="" >
                                                    @error('UnidadesPorCaja')
                                                    <span class="small text-danger">{{$message}}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <input id="MedidasProductoId" name="MedidasProductoId" type="hidden" value="{{ $producto->id }}" />
                                                <button type="submit" class="btn btn-primary">{{ trans('labels.actualizar') }}</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane {{ !empty($tabName) && $tabName == 'tab-5' ? 'active' : '' }}" id="tab-5">
                                <div class="feed-activity-list">
                                    <div class="feed-element">
                                        
                                        <form role="form" name="FormRegistroRelacion" id="FormRegistroRelacion" class="wizard-big" action="{{route('productoRelaciones.store')}}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('POST')
                                            <h1>{{ trans('ecommerce.productosRelacionados') }}</h1>
                                            <select id="ProductosRelacionados[]" name="ProductosRelacionados[]" class="form-control dual_select" multiple>
                                                <?php foreach ($relacionDisponible as $row) : ?>
                                                    <option value="<?= $row->id ?>"><?= $row->nombre ?></option>
                                                <?php endforeach; ?>
                                                <?php foreach ($relacionados as $row2) : ?>
                                                    <option selected value="<?= $row2->producto_id ?>"><?= $row2->producto_nombre ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="hr-line-dashed"></div>
                                            <div>
                                                <input id="RelacionProductoId" name="RelacionProductoId" type="hidden" value="{{ $producto->id }}" />
                                                <button type="submit" class="btn btn-primary">{{ trans('labels.actualizar') }}</button>
                                            </div>
                                        </form>

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
    @include('layouts.partials.delete-confirm')
@endsection

@section('js_validation')
<script src="{{ asset('js/plugins/validate/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/plugins/validate/additional-methods.min.js') }}"></script>
@endSection

@section('js_custom_upload_file')
<script src="{{ asset('js/plugins/bs-custom-file/bs-custom-file-input.min.js') }}"></script>
@endSection

@section('js_summernote')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="{{ asset('js/plugins/summernote/summernote-ext-print.js') }}"></script>
<script src="{{ asset('js/plugins/summernote/summernote-save-button.js') }}"></script>
<script src="{{ asset('js/plugins/summernote/summernote-ext-codewrapper.js') }}"></script>
@endSection

@section('js_select2')
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
@endSection

@section('js_datatable')
<script src="{{ asset('js/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/datatables/dataTables.responsive.js') }}"></script>

<!-- Dual Listbox -->
<script src="{{ asset('js/plugins/dualListbox/jquery.bootstrap-duallistbox.js') }}"></script>

@endSection

@section('js_lightbox2')
<script src="{{ asset('js/plugins/lightbox2/lightbox.min.js') }}"></script>
@endSection


@section('script')
<script>
    $('#Resumen').keyup(function () {
      	  var max = 500;
      	  var len = $(this).val().length;
      	  if (len >= max) {
      	    $('#charNum').text(' l\u00EDmite completo de caracteres');
      	  } else {
      	    var char = max - len;
      	    $('#charNum').text(char + ' caracteres disponibles');
      	  }
      	});
    $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-white btn-sm';
    $(function() {
        function isNumeric(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }

        function onchange() {
            //Since you have JQuery, why aren't you using it?
            var pvp = $('#PrecioVentaPublico');
            var precioAnterior = $('#PrecioVentaAnterior');
            if(isNumeric(pvp.val())){
                var paramPorcentage = {{$parametros['PorcentPrecioAnterior']}}
                var valorPorcentage = (pvp.val() * paramPorcentage) / 100;
                var total = parseFloat(pvp.val()) + valorPorcentage;
                precioAnterior.val(total);
            }
            else
                precioAnterior.val(0);
        }
        $('#PrecioVentaPublico').on('change', onchange);

        bsCustomFileInput.init()
        $('.select2').select2({placeholder :'{{trans('labels.seleccioneItem')}}', allowClear: true});
        $('#loader').hide();
        $('#Detalle').summernote({
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
            height: 500,
            tabsize: 2,
            dialogsInBody: true,
            lang: 'es-ES',
            callbacks: {
                onImageUpload: function(files) {
                    var $editor = $(this);
                    var data = new FormData();
                    data.append('image', files[0]);
                    //data.append('_token', "{{csrf_token()}}");
                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{route('productoRecursos.storeImage', ['productoId'=>$producto->id] )}}', 
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
                Nombre: {
                    required: true,
                },
                PrecioVentaPublico: {
                    number: true
                },
                PrecioVentaAnterior: {
                    number: true
                },
            },
            messages: {
                Nombre: {
                    required: "{{ trans('validation.required', [ 'attribute' => 'Nombre']) }}",
                },
                PrecioVentaPublico: {
                    number: "{{ trans('validation.numeric', [ 'attribute' => 'Precio']) }}",
                },
                PrecioVentaAnterior: {
                    number: "{{ trans('validation.numeric', [ 'attribute' => 'Precio']) }}",
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
        });
        
        $('#FormRegistroImagen').validate({
            rules: {
                Tipo: {
                    required: true,
                }
            },
            messages: {
                Tipo: {
                    required: "{{ trans('validation.required', [ 'attribute' => 'Tipo']) }}",
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
        });


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
                {data:'tipo_nombre'},
                {data:'descripcion'},
                {data:'visible', width: 50},
                {data:'opc', name: 'action', orderable:false, searchable:false, className: 'text-right', width: 180},
            ]
        });
        /* End Script Datatable */

        $('#FormRegistroCategoria').validate({
            rules: {
                Categoria: {
                    required: true,
                }
            },
            messages: {
                Categoria: {
                    required: "{{ trans('validation.required', [ 'attribute' => 'Categoria']) }}",
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
        });

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
                {data:'opc', name: 'action', orderable:false, searchable:false, className: 'text-right', width: 40},
            ]
        });
        /* End Script Datatable */

        $('.dual_select').bootstrapDualListbox({
                selectorMinimalHeight: 160
        });

        $('#FormRegistroMedidasPeso').validate({
            rules: {
                Largo: {
                    number: true
                },
                Ancho: {
                    number: true
                },
                Alto: {
                    number: true
                },
                Peso: {
                    number: true
                }
            },
            messages: {
                Largo: {
                    number: "{{ trans('validation.numeric', [ 'attribute' => 'Largo']) }}",
                },
                Ancho: {
                    number: "{{ trans('validation.numeric', [ 'attribute' => 'Ancho']) }}",
                },
                Alto: {
                    number: "{{ trans('validation.numeric', [ 'attribute' => 'Alto']) }}",
                },
                Peso: {
                    number: "{{ trans('validation.numeric', [ 'attribute' => 'Peso']) }}",
                }
            }
        });


    });
</script>

@endSection