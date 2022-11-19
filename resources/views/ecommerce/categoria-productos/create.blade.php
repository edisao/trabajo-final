@extends('layouts.main')
@section('page_title', trans('msg.msgNuevoRegistro'))

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
                    <strong>{{ trans('labels.nuevo') }}</strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12 white-bg">
                <div class="wrapper wrapper-content">
                    
                    <form role="form" name="FormRegistro" id="FormRegistro" action="{{ route('categoriaProductos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="Nombre">{{ trans('labels.nombre') }}</label>
                            <input type="text" class="form-control" id="Nombre" name="Nombre" value="{{old('Nombre')}}" maxlength="50" autocomplete="off" placeholder="" required>
                            @error('Nombre')
                            <span class="small text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="custom-file">
                                <input id="Imagen" name="Imagen" class="custom-file-input" type="file" accept="image/*" value="{{old('Imagen')}}">
                                <label for="Imagen" class="custom-file-label">{{ trans('labels.seleccioneImagen') }}...</label>
                                @error('Imagen')
                                <span class="small text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Resumen">{{ trans('labels.resumen') }}</label>
                            <input type="text" class="form-control" id="Resumen" name="Resumen" value="{{old('Resumen')}}" maxlength="200" autocomplete="off" placeholder="" >
                            @error('Resumen')
                            <span class="small text-danger">{{$message}}</span>
                            @enderror
                        </div>
                        
                        <div>
                            <button type="submit" class="btn btn-primary">{{ trans('labels.registrar') }}</button>
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

@section('script')
<script>
    $(function() {
        bsCustomFileInput.init()
        
        $('#FormRegistro').validate({
            rules: {
                Nombre: {
                    required: true,
                }
            },
            messages: {
                Nombre: {
                    required: "{{ trans('validation.required', [ 'attribute' => 'Nombre']) }}",
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