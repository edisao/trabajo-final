<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Helpers\Constants;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Libraries\AuthUtil;
use App\Libraries\Util;
use App\Libraries\UtilEcommerce;
use URL;
use Toastr;

class CategoriaProductosController extends Controller
{
    protected $dto;

    public function __construct()
    {
        $this->constants = new Constants();
    }

    public function index()
    {
        return view('ecommerce.categoria-productos.index');
    }

    public function data(Request $request)
    {
        $records = CategoriaProducto::getCategoriaProductos();
        return Datatables()
            ->of($records)
            ->addColumn('opc', function ($row) {
                return '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.ver') . '"><a href="' . route('categoriaProductos.show', $row) . '" class="btn btn-default"><i class="fa fa-eye"></i></a></span>'; //$this->getActionColumn($row);
            })
            ->rawColumns(['opc'])
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

        //$options = '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.ver') . '"><a href="' . route('categoriaProductos.show', $row) . '" class="btn btn-default"><i class="fa fa-eye"></i></a></span> ';
        $options = '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . $tooltipActivo . '"><a href="' . route('categoriaProductos.enabled', $row) . '" class="btn btn-default"><i class="' . $imageEstado . '"></i></a></span> ';
        $options .= '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.editar') . '"><a href="' . route('categoriaProductos.edit', $row) . '" class="btn btn-default"><i class="fa fa-pencil"></i></a></span> ';
        $options .= '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.eliminar') . '"><a href="#" data-href="' . route('categoriaProductos.delete', $row) . '" data-toggle="modal" data-target="#modal-confirm-delete" data-placement="top" data-name="' . $row->nombre . '" class="btn btn-default"><i class="fa fa-trash"></i></a></span>';
        return $options;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('ecommerce.categoria-productos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $authUtil = new AuthUtil();
        $constants = new Constants();
        $utilEcommerce = new UtilEcommerce();
        try {

            $validator = Validator::make($request->all(), [
                'Nombre' => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->route('categoriaProductos.create')
                    ->withErrors($validator)
                    ->withInput();
            }

            $categoriaProducto = new CategoriaProducto();
            $categoriaProducto->sitio_id = $authUtil->getServiceContextValue($constants->contextSitioId);
            $categoriaProducto->nombre = $request->Nombre;
            $categoriaProducto->resumen = $request->Resumen;
            $categoriaProducto->slug = Str::slug($request->Nombre, $constants->separatorSlug);
            $categoriaProducto->orden = 1;
            $categoriaProducto->total_productos = 0;
            $categoriaProducto->enabled = true;
            $categoriaProducto->selector = $this->generateSelector();
            if ($request->file('Imagen')) {
                $basepath = $utilEcommerce->getCurrentBaseEcommercePath() . 'category/';

                $fileName = Str::random(15) . '.' . $request->file('Imagen')->extension();
                $request->Imagen->move(public_path($basepath), $fileName);
                if (file_exists(public_path($basepath) . $fileName)) {
                    $urlFile = URL::to('/') . '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'category/' . $fileName;
                    //$urlFile = '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'category/' . $fileName;
                    $categoriaProducto->imagen = $fileName;
                    $categoriaProducto->url_imagen = $urlFile;
                }
            }
            $categoriaProducto->save();
            Toastr::success(trans('msg.msgRegistroExitoso'), trans('ecommerce.categoriaProductos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('categoriaProductos.index');
        } catch (Exception $ex) {
            Log::critical("Insert CategoriaProducto Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.categoriaProductos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('categoriaProductos.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ CategoriaProducto  $categoriaProducto
     * @return \Illuminate\Http\Response
     */
    public function show(CategoriaProducto $categoriaProducto)
    {
        return view('ecommerce.categoria-productos.show')->with('categoriaProducto', $categoriaProducto);
        //return redirect()->route('categoriaProductos.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ CategoriaProducto  $categoriaProducto
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoriaProducto $categoriaProducto)
    {
        return view('ecommerce.categoria-productos.edit')->with('categoriaProducto', $categoriaProducto);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ CategoriaProducto  $categoriaProducto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CategoriaProducto $categoriaProducto)
    {
        $utilEcommerce = new UtilEcommerce();
        try {

            $validator = Validator::make($request->all(), [
                'Nombre' => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->route('categoriaProductos.edit', $categoriaProducto)
                    ->withErrors($validator)
                    ->withInput();
            }
            $categoriaProducto->nombre = $request->Nombre;
            $categoriaProducto->resumen = $request->Resumen;
            if ($request->file('Imagen')) {
                $basepath = $utilEcommerce->getCurrentBaseEcommercePath() . 'category/';

                $fileName = Str::random(15) . '.' . $request->file('Imagen')->extension();
                $request->Imagen->move(public_path($basepath), $fileName);
                if (file_exists(public_path($basepath) . $fileName)) {
                    // Elimina el archivo anterior
                    if (!empty($categoriaProducto->imagen) && file_exists(public_path($basepath) . $categoriaProducto->imagen))
                        File::delete(public_path($basepath) . $categoriaProducto->imagen);

                    // Configura los nuevos datos
                    $urlFile = URL::to('/') . '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'category/' . $fileName;
                    //$urlFile = '/' . $utilEcommerce->getCurrentBaseEcommercePath() . 'category/' . $fileName;
                    $categoriaProducto->imagen = $fileName;
                    $categoriaProducto->url_imagen = $urlFile;
                }
            }
            $categoriaProducto->save();
            Toastr::success(trans('msg.msgActualizacionExitosa'), trans('ecommerce.categoriaProductos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('categoriaProductos.index');
        } catch (Exception $ex) {
            Log::critical("Update Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.categoriaProductos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('categoriaProductos.edit', $categoriaProducto)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ CategoriaProducto  $categoriaProducto
     * @return \Illuminate\Http\Response
     */
    public function destroy(CategoriaProducto $categoriaProducto)
    {
        $utilEcommerce = new UtilEcommerce();
        try {
            $data = CategoriaProducto::getCategoriaProductoById($categoriaProducto->id);
            if (isset($data)) {
                $basePath = $utilEcommerce->getCurrentBaseEcommercePath() . 'category/';

                if (file_exists(public_path($basePath) . $data->imagen)) {
                    // Verifica y elimina si existe la imagen anterior
                    if (!empty($data->imagen) && file_exists(public_path($basePath) . $data->imagen))
                        File::delete(public_path($basePath) . $data->imagen);
                }

                $data->delete();
                Toastr::success(trans('msg.msgEliminacionCorrecta'), trans('ecommerce.categoriaProductos'), $this->constants->notificationDefaultOptions);
            } else
                Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.categoriaProductos'), $this->constants->notificationDefaultOptions);

            return redirect()->route('categoriaProductos.index');
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.categoriaProductos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('categoriaProductos.index');
        }
    }

    /**
     * Enable/disable a record
     * @param  \App\Models\ CategoriaProducto  $categoriaProducto
     */
    public function enabled(CategoriaProducto $categoriaProducto)
    {
        try {
            $data = CategoriaProducto::find($categoriaProducto->id);
            $data->enabled = !$categoriaProducto->enabled;
            $data->save();
            Toastr::success(trans('msg.msgActualizacionExitosa'), trans('ecommerce.categoriaProductos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('categoriaProductos.index');
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.categoriaProductos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('categoriaProductos.index');
        }
    }

    protected function generateSelector()
    {
        $response = '';
        $util = new Util();
        $time = now()->timestamp;
        $selector = $util->GetSelector(25);
        $data = CategoriaProducto::select('id')->where('selector', $selector)->first();
        $response = (!isset($data)) ? $selector : $selector . $time;
        return $response;
    }
}
