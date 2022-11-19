<?php

namespace App\Http\Controllers;

use App\Models\{ProductoRecurso, Producto};
use App\Models\{ItemCatalogo};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Helpers\Constants;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use App\Libraries\AuthUtil;
use App\Libraries\Util;
use App\Libraries\UtilEcommerce;
use Illuminate\Support\Facades\Config;
use URL;
use Toastr;
use Image;


class ProductoRecursosController extends Controller
{
    protected $dto;

    public function __construct()
    {
        $this->constants = new Constants();
    }

    public function data(Request $request)
    {
        $utilEcommerce = new UtilEcommerce();
        $authUtil = new AuthUtil();

        $productoId = $request->productoId;
        $records = ProductoRecurso::getImagesFromProductId($productoId);
        $arr = [];
        $inc = 0;
        foreach ($records as $row) {
            $urlImage = $utilEcommerce->getUrlImageProduct($productoId, $row->archivo);
            $previewImage = '<a href="' . $urlImage . '" data-lightbox="producto-' . $row->producto_id . '" data-title="' . $row->nombre . '"><img alt="' . $row->nombre . '" class="" width="80" src="' . $urlImage . '"></a>';
            $jsonArrayObject = (array('id' => $row->id, 'nombre' => $row->nombre, 'tipo_nombre' => $row->tipo_nombre, 'preview' => $previewImage, 'descripcion' => $row->descripcion, 'visible' => ($row->visible) ? '<span class="label label-info float-right">SI</span>' : '<span class="label label-warning float-right">NO</span>', 'opc' => $this->getActionColumn($row)));
            $arr[$inc] = $jsonArrayObject;
            $inc++;
        }

        return Datatables()
            ->of($arr)
            ->rawColumns(['nombre', 'preview', 'opc', 'visible'])
            ->toJson();
    }

    protected function getActionColumn($row)
    {
        $imageEstado = "fa fa-times";
        $tooltipActivo = trans('labels.inactivo');
        if ($row->enabled) {
            $tooltipActivo = trans('labels.activo');
            $imageEstado = "fa fa-check";
        }

        // Valida si es Imagen Principal del contenido
        if ($row->principal) {
            $icoPrincipal = 'fa fa-bell';
            $toolTipPrincipal = trans('labels.principal');
        } else {
            $icoPrincipal = 'fa fa-bell-o';
            $toolTipPrincipal = trans('labels.noPrincipal');
        }

        $options = '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . $toolTipPrincipal . '"><a href="' . route('productoRecursos.principal', $row) . '" class="btn btn-default"><i class="' . $icoPrincipal . '"></i></a></span> ';
        $options .= '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . $tooltipActivo . '"><a href="' . route('productoRecursos.enabled', $row) . '" class="btn btn-default"><i class="' . $imageEstado . '"></i></a></span> ';
        $options .= '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.editar') . '"><a href="' . route('productoRecursos.edit', $row) . '" class="btn btn-default"><i class="fa fa-pencil"></i></a></span> ';
        $options .= '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.eliminar') . '"><a href="#" data-href="' . route('productoRecursos.delete', $row) . '" data-toggle="modal" data-target="#modal-confirm-delete" data-placement="top" data-name="' . $row->nombre . '" class="btn btn-default"><i class="fa fa-trash"></i></a></span>';
        return $options;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*
    public function create()
    {
        return view('productoRecursos.create');
    }
    */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        session([$this->constants->sessionUserShowTab => 'tab-2']);
        try {
            $utilEcommerce = new UtilEcommerce();
            $authUtil = new AuthUtil();
            $constants = new Constants();

            // Obtiene la información del producto
            $producto = Producto::getProductoById($request->ImagenProductoId);

            $validator = Validator::make($request->all(), [
                'Tipo' => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->route('productos.edit', $producto)
                    ->withErrors($validator)
                    ->withInput();
            }

            $imageTipoId = ItemCatalogo::getItemCatalogoIdByCatalogoCodeItemCode("TIPO_IMAGEN_PRODUCTO", "IMAGEN");
            $basepath = $utilEcommerce->getCurrentBaseEcommercePath($authUtil->getServiceContextValue($constants->contextSitioId)) . 'product/' . $producto->selector . '/';

            if ($request->hasfile('Imagenes')) {
                $images = $request->file('Imagenes');

                foreach ($images as $file) {
                    $fileName = Str::random(15) . '.' . $file->extension();
                    $file->move(public_path($basepath), $fileName);
                    if (file_exists(public_path($basepath) . $fileName)) {
                        $selector = $this->generateSelector();
                        $urlFile = URL::to('/') . '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/' . $fileName;
                        //$urlFile = '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/' . $fileName;

                        // Registrar recurso tipo imagen
                        $productoRecurso = new ProductoRecurso();
                        $productoRecurso->producto_id = $request->ImagenProductoId;
                        $productoRecurso->tipo_id = $request->Tipo;
                        $productoRecurso->nombre = $producto->nombre;
                        $productoRecurso->archivo = $fileName;
                        $productoRecurso->url = $urlFile;
                        $productoRecurso->url_thum = null;
                        $productoRecurso->url_thum_adicional = null;
                        $productoRecurso->descripcion = null;
                        $productoRecurso->visible = true;
                        $productoRecurso->orden = 1;
                        $productoRecurso->principal = (ProductoRecurso::getCountImagesFromProductoId($request->ImagenProductoId) == 0) ? true : false;
                        $productoRecurso->selector = $selector;
                        $productoRecurso->enabled = true;
                        $productoRecurso->save();

                        if ($productoRecurso->principal)
                            Producto::where('id', $request->ImagenProductoId)->update(['imagen_portada' => $fileName]);
                    }
                }
            }

            Toastr::success(trans('msg.msgRegistroExitoso'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto);
        } catch (Exception $ex) {
            Log::critical("Insert ProductoRecurso Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.index')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ ProductoRecurso  $productoRecurso
     * @return \Illuminate\Http\Response
     */
    public function show(ProductoRecurso $productoRecurso)
    {
        return view('productoRecursos.show')->with('productoRecurso', $productoRecurso);
        //return redirect()->route('productoRecursos.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ ProductoRecurso  $productoRecurso
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductoRecurso $productoRecurso)
    {
        //dd($productoRecurso);
        $tipos = ItemCatalogo::getItemCatalogosByCatalogoCode("TIPO_IMAGEN_PRODUCTO");
        return view('ecommerce.producto-recursos.edit', compact('productoRecurso', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ ProductoRecurso  $productoRecurso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductoRecurso $productoRecurso)
    {
        session([$this->constants->sessionUserShowTab => 'tab-2']);
        try {
            $utilEcommerce = new UtilEcommerce();

            $validator = Validator::make($request->all(), [
                'Nombre' => 'required',
                'Tipo' => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->route('productoRecursos.edit', $productoRecurso)
                    ->withErrors($validator)
                    ->withInput();
            }

            // Obtiene la información del producto
            $producto = Producto::getProductoById($productoRecurso->producto_id);
            $basePath = $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/';

            $productoRecurso->nombre = $request->Nombre;
            $productoRecurso->tipo_id = $request->Tipo;
            $productoRecurso->descripcion = $request->Descripcion;
            $productoRecurso->visible = ($request->Visible != null) ? true : false;
            if ($request->file('Imagen')) {
                $fileName = Str::random(15) . '.' . $request->file('Imagen')->extension();
                $request->Imagen->move(public_path($basePath), $fileName);
                // valida que se haya cargado correctamente la nueva imagen

                if (file_exists(public_path($basePath) . $fileName)) {
                    $urlFile = URL::to('/') . '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/' . $fileName;
                    //$urlFile = '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/' . $fileName;

                    // Verifica y elimina si existe la imagen anterior
                    if (!empty($productoRecurso->archivo) && file_exists(public_path($basePath) . $productoRecurso->archivo))
                        File::delete(public_path($basePath) . $productoRecurso->archivo);

                    // Actualiza la nueva imagen
                    $productoRecurso->archivo = $fileName;
                    $productoRecurso->url = $urlFile;
                }
            }
            $productoRecurso->save();

            Toastr::success(trans('msg.msgActualizacionExitosa'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto);
        } catch (Exception $ex) {
            Log::critical("Update Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productoRecursos.edit', $productoRecurso)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ ProductoRecurso  $productoRecurso
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductoRecurso $productoRecurso)
    {
        session([$this->constants->sessionUserShowTab => 'tab-2']);
        $utilEcommerce = new UtilEcommerce();
        $principal = false;
        try {
            $productoId = $productoRecurso->producto_id;
            $producto = Producto::find($productoId);

            $data = ProductoRecurso::find($productoRecurso->id);
            if (isset($data)) {
                $principal = $data->principal;

                $basePath = $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/';
                // Verifica y elimina si existe la imagen anterior
                if (!empty($data->archivo) && file_exists(public_path($basePath) . $data->archivo))
                    File::delete(public_path($basePath) . $data->archivo);
                $data->delete();

                if ($principal) {
                    // Al ser la imagen principal configura la nueva imagen
                    $recurso = ProductoRecurso::query()
                        ->select('producto_recurso.*')
                        ->where('producto_id', $productoId)
                        ->where('enabled', true)
                        ->where('visible', true)
                        ->first();
                    if (isset($recurso)) {
                        $dataRecurso = ProductoRecurso::find($recurso->id);
                        $dataRecurso->principal = true;
                        $dataRecurso->save();

                        // Actualiza la imagen principal en la tabla Producto
                        Producto::where('id', $productoId)->update(['imagen_portada' => $recurso->archivo]);
                    } else
                        Producto::where('id', $productoId)->update(['imagen_portada' => null]);
                }

                // Elimina el registro
                $data->delete();
                Toastr::success(trans('msg.msgEliminacionCorrecta'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            } else
                Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);

            return redirect()->route('productos.edit', $producto);
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.index');
        }
    }

    /**
     * Enable/disable a record
     * @param  \App\Models\ ProductoRecurso  $productoRecurso
     */
    public function enabled(ProductoRecurso $productoRecurso)
    {
        session([$this->constants->sessionUserShowTab => 'tab-2']);
        try {
            $data = ProductoRecurso::find($productoRecurso->id);
            $data->enabled = !$productoRecurso->enabled;
            $data->save();
            Toastr::success(trans('msg.msgActualizacionExitosa'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $productoRecurso->producto_id);
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.index');
        }
    }

    protected function generateSelector()
    {
        $response = '';
        $util = new Util();
        $time = now()->timestamp;
        $selector = $util->GetSelector(25);
        $data = ProductoRecurso::select('id')->where('selector', $selector)->first();
        $response = (!isset($data)) ? $selector : $selector . $time;
        return $response;
    }

    public function principal(ProductoRecurso $productoRecurso)
    {
        session([$this->constants->sessionUserShowTab => 'tab-2']);
        try {
            //Log::critical("Producto ID: " . $productoRecurso->producto_id);
            // Encera los datos del contenido
            ProductoRecurso::where('producto_id', $productoRecurso->producto_id)->update(['principal' => false]);

            // Actualizo el recurso como principal
            $data = ProductoRecurso::find($productoRecurso->id);
            $data->principal = true; //!$productoRecurso->principal;
            $data->save();

            // Actualiza la imagen principal en la tabla Producto
            Producto::where('id', $productoRecurso->producto_id)->update(['imagen_portada' => $productoRecurso->archivo]);

            Toastr::success(trans('msg.msgActualizacionExitosa'), trans('labels.contenidoRecurso'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $productoRecurso->producto_id);
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('labels.contenidoRecurso'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $productoRecurso->producto_id);
        }
    }

    public function order(Producto $producto)
    {
        //Log::critical("ORDER: " . $producto->id);
        $productoRecursos = ProductoRecurso::query()
            ->select('id', 'nombre', 'archivo', 'url')
            ->where('enabled', true)
            ->where('visible', true)
            ->where('producto_id', $producto->id)
            ->orderBy('orden', 'ASC')
            ->get();
        return view('ecommerce.producto-recursos.order')
            ->with('producto', $producto)
            ->with('productoRecursos', $productoRecursos);
    }

    public function updateOrder(Request $request)
    {
        session([$this->constants->sessionUserShowTab => 'tab-2']);
        try {
            $ord = 1;
            $arrRecursos = $request->order;
            if ($arrRecursos != null) {
                foreach ($arrRecursos as $row) {
                    $objRecursos = ProductoRecurso::find($row['id']);
                    if (isset($objRecursos)) {
                        $objRecursos->orden = $ord;
                        $objRecursos->save();
                        $ord = $ord + 1;
                    }
                }
            }
            echo '<h5>' . trans('msg.msgRecursosOrdenadosOk') . '</h5>';
        } catch (Exception $ex) {
            Log::critical("Update order Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.index');
        }
    }

    public function storeImage(Request $request)
    {
        $linkStorage = '';
        try {
            $productoId = $request->productoId;
            //Log::critical("Recurso PRODUCTO ID: " . $productoId);
            $producto = Producto::find($productoId);

            $authUtil = new AuthUtil();
            $constants = new Constants();
            $utilEcommerce = new UtilEcommerce();
            $urlFile = '';
            $userId = $authUtil->getServiceContextValue($constants->contextUserId);
            $imageTipoId = ItemCatalogo::getItemCatalogoIdByCatalogoCodeItemCode("TIPO_RECURSO", "IMAGEN");
            $basepath = $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/';
            if (!File::exists($basepath)) {
                File::makeDirectory($basepath, 0777, true, true);
            }

            if ($request->file('image')) {
                $fileName = Str::random(15) . '.' . $request->file('image')->extension();
                $image = $request->file('image');
                $destinationPath = public_path($basepath);
                $img = Image::make($image->getRealPath());
                // Obtiene el tamaño de la imagen
                $width = $img->width();
                $height = $img->height();
                $maxImageSize = (!empty(Config::get('microtess.sizes.resource_image')) ? Config::get('microtess.sizes.resource_image') : "1200");
                if ($width > $maxImageSize) {
                    // Como la imagen es mayor a 1200
                    // crea una nueva imagen para cargarla
                    $img->orientate()
                        ->resize($maxImageSize, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->save($destinationPath . '/' . $fileName);
                } else {
                    // La imagen en menor de 1200
                    // La carga tal cual.
                    $img->save($destinationPath . '/' . $fileName);
                }

                if (file_exists(public_path($basepath) . $fileName)) {
                    $urlFile = URL::to('/') . '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/' . $fileName;
                    //$urlFile = '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'product/' . $producto->selector . '/' . $fileName;
                    // Registrar recurso tipo imagen
                    $productoRecurso = new ProductoRecurso();
                    $productoRecurso->producto_id = $productoId;
                    $productoRecurso->tipo_id = $imageTipoId;
                    $productoRecurso->nombre = $fileName;
                    $productoRecurso->archivo = $fileName;
                    $productoRecurso->url = $urlFile;
                    $productoRecurso->url_thum = null;
                    $productoRecurso->url_thum_adicional = null;
                    $productoRecurso->descripcion = null;
                    //$productoRecurso->general = false;
                    $productoRecurso->visible = false;
                    $productoRecurso->selector = $this->generateSelector();
                    $productoRecurso->enabled = true;
                    $productoRecurso->save();
                }
            }
            return asset($urlFile);
        } catch (Exception $ex) {
            Log::critical("Insert Producto Recurso StoreImage Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('labels.usuarioRecurso'), $this->constants->notificationDefaultOptions);
            return asset($urlFile);
        }
    }
}
