@extends('layouts.main')
@section('page_title', trans('msg.msgEditarInformacion') )

@section('css_select2')
<link rel="stylesheet" href="{{ asset('css/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/select2/select2-bootstrap4.min.css') }}">
@endSection

@section('page_content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ trans('labels.contenidoRecurso') }}</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">{{ trans('labels.inicio') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('productos.edit', $productoRecurso->producto_id) }}">{{ trans('labels.contenidoRecurso') }}</a>
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
                    
                    <form role="form" name="FormRegistro" id="FormRegistro" action="{{route('productoRecursos.update',$productoRecurso)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="Nombre">{{ trans('labels.nombre') }}</label>
                            <input type="text" class="form-control" id="Nombre" name="Nombre" value="{{old('Nombre', $productoRecurso->nombre )}}" maxlength="100" autocomplete="off" placeholder="" required>
                            @error('Nombre')
                            <span class="small text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="Imagen">{{ trans('labels.imagen') }}</label>
                            <div class="form-group">
                                <div class="custom-file">
                                    <input id="Imagenes[]" name="Imagenes[]" class="custom-file-input" type="file" accept="image/*" value="{{old('Imagen')}}" multiple>
                                    <label for="Imagen" class="custom-file-label">{{ trans('labels.seleccioneImagen') }}...</label>
                                    @error('Imagen')
                                    <span class="small text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('labels.tipos') }}</label>
                            <select class="form-control select2" id="Tipo" name="Tipo" style="width: 100%;">
                                <option selected disabled hidden value='-1'></option>
                                <?php foreach ($tipos as $tipo) : ?>
                                    <option value="{{$tipo->id}}" {{ old('Tipo', $productoRecurso->tipo_id) == $tipo->id ? 'selected' : '' }}>{{$tipo->nombre}}</option>
                                <?php endforeach; ?> 
                            </select>
                            <span class="small text-danger"><?= session('errors.Tipo') ?></span>
                        </div>
                        <div class="form-group">
                            <label for="Descripcion">{{ trans('labels.descripcion') }}</label>
                            <input type="text" class="form-control" id="Descripcion" name="Descripcion" value="{{old('Descripcion', $productoRecurso->descripcion)}}" maxlength="200" autocomplete="off" placeholder="" >
                            @error('Descripcion')
                            <span class="small text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="Visible" name="Visible" @if(old('Visible', $productoRecurso->visible)) checked @endif>
                            <label class="form-check-label" for="Visible">{{ trans('labels.visible') }}</label>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div>
                            <button type="submit" class="btn btn-primary">{{ trans('labels.actualizar') }}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_validation')
<script src="{{ asset('js/plugins/validate/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/plugins/validate/additional-methods.min.js') }}"></script>
@endSection

@section('js_custom_upload_file')
<script src="{{ asset('js/plugins/bs-custom-file/bs-custom-file-input.min.js') }}"></script>
@endSection

@section('js_select2')
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
@endSection

@section('script')
<script>
    $(function() {
        bsCustomFileInput.init()
        $('.select2').select2({placeholder :'{{trans('labels.seleccioneItem')}}', allowClear: true});

        $('#FormRegistro').validate({
            rules: {
                Nombre: {
                    required: true,
                },
                Tipo: {
                    required: true,
                }
            },
            messages: {
                Nombre: {
                    required: "{{ trans('validation.required', [ 'attribute' => 'Nombre']) }}",
                },
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
    });
</script>

@endSection