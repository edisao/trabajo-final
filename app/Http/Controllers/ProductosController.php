<?php

namespace App\Http\Controllers;

use App\Models\{Producto};
use App\Models\{ItemCatalogo, Parametro};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Helpers\Constants;
use App\Libraries\AuthUtil;
use Illuminate\Support\Str;
use App\Libraries\Util;
use Toastr;
use Carbon\Carbon;

class ProductosController extends Controller
{
    protected $dto;

    public function __construct()
    {
        $this->constants = new Constants();
    }

    public function index()
    {
        return view('ecommerce.productos.index');
    }

    public function data(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        return Producto::getProductosData($draw, $start, $rowperpage, $columnIndex_arr, $columnName_arr, $order_arr, $search_arr);
    }

    public function indexOfert()
    {
        return view('ecommerce.productos.ofert');
    }

    public function dataOfert(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        return Producto::getProductosOferta($draw, $start, $rowperpage, $columnIndex_arr, $columnName_arr, $order_arr, $search_arr);
    }

    public function indexSlider()
    {
        return view('ecommerce.productos.slider');
    }

    public function dataSlider(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        return Producto::getProductosSlider($draw, $start, $rowperpage, $columnIndex_arr, $columnName_arr, $order_arr, $search_arr);
    }

    public function indexDraft()
    {
        return view('ecommerce.productos.draft');
    }

    public function dataDraft(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        return Producto::getProductosDraft($draw, $start, $rowperpage, $columnIndex_arr, $columnName_arr, $order_arr, $search_arr);
    }

    protected function getActionColumn($row)
    {
        $imageEstado = "fa fa-times";
        $tooltipActivo = trans('labels.inactivo');
        if ($row->enabled) {
            $tooltipActivo = trans('labels.activo');
            $imageEstado = "fa fa-check";
        }

        //$options = '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.ver') . '"><a href="' . route('productos.show', $row) . '" class="btn btn-default"><i class="fa fa-eye"></i></a></span> ';
        $options = '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . $tooltipActivo . '"><a href="' . route('productos.enabled', $row) . '" class="btn btn-default"><i class="' . $imageEstado . '"></i></a></span> ';
        $options .= '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.editar') . '"><a href="' . route('productos.edit', $row) . '" class="btn btn-default"><i class="fa fa-pencil"></i></a></span> ';
        $options .= '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.eliminar') . '"><a href="#" data-href="' . route('productos.delete', $row) . '" data-toggle="modal" data-target="#modal-confirm-delete" data-placement="top" data-name="' . $row->nombre . '" class="btn btn-default"><i class="fa fa-trash"></i></a></span>';
        return $options;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('ecommerce.productos.create');
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
        try {

            $validator = Validator::make($request->all(), [
                'Nombre' => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->route('productos.create')
                    ->withErrors($validator)
                    ->withInput();
            }

            $producto = new Producto();
            $producto->sitio_id = $authUtil->getServiceContextValue($constants->contextSitioId);
            $producto->nombre = $request->Nombre;
            $producto->slug = Str::slug($request->Nombre, $constants->separatorSlug);
            $producto->resumen = $request->Resumen;
            $producto->selector = $this->generateSelector();
            $producto->stock = 0;
            $producto->registra_comentarios = false;
            $producto->mostrar_slider = false;
            $producto->en_oferta = false;
            $producto->precio_costo = 0;
            $producto->mostrar_precio = true;
            $producto->pvp = 0;
            $producto->incluye_impuestos = false;
            $producto->mostrar_pvp_anterior = false;
            $producto->pvp_anterior = 0;
            $producto->enabled = true;
            $producto->published = false;
            $producto->recomendado = false;
            $producto->longitud = 0;
            $producto->ancho = 0;
            $producto->alto = 0;
            $producto->peso = 0;
            $producto->save();
            Toastr::success(trans('msg.msgRegistroExitoso'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto);
        } catch (Exception $ex) {
            Log::critical("Insert Producto Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function show($selector)
    {
        $data = Producto::getProductoBySelector($selector);
        if (isset($data))
            return view('ecommerce.productos.show')->with('producto', $data);
        else {
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.index');
        }
        //return redirect()->route('productos.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function edit(Producto $producto)
    {
        $tabName = '';
        if (session()->exists($this->constants->sessionUserShowTab)) {
            $tabName = session($this->constants->sessionUserShowTab);
            session()->forget($this->constants->sessionUserShowTab);
        }

        $producto = Producto::getProductoById($producto->id);
        if (isset($producto)) {
            $medidas = ItemCatalogo::getItemCatalogosByCatalogoCode("UNIDAD_MEDIDA");
            $pesos = ItemCatalogo::getItemCatalogosByCatalogoCode("UNIDAD_PESO");
            $marcas = Producto::getMarcasDisponibles();
            $categorias = Producto::getCategoriasDisponibles($producto->id);
            $relacionDisponible = Producto::getProductosDisponiblesRelacionadosByProductoId($producto->id);
            $relacionados = Producto::getProductosRelacionadosByProductoId($producto->id);
            $tiposImagen = ItemCatalogo::getItemCatalogosByCatalogoCode("TIPO_IMAGEN_PRODUCTO");

            // Obtiene el parametro configurado para obtener
            // El precio de venta anterior a partir del precio de venta al publico
            // Este dato es informativo ya que puede ser cambiado por el usuario al 
            // editar la información del producto. Se setea el 10% en caso de que no 
            // esté configurado el parámetro.
            $arrParametroPorcentPrecioAnterior = Parametro::getParametroByCode('PORCENT_VALOR_ANT');
            $valueParametroPorcentPrecioAnterior = 10; // 10% porcentaje por defecto, informativo.
            if (isset($arrParametroPorcentPrecioAnterior))
                $valueParametroPorcentPrecioAnterior = $arrParametroPorcentPrecioAnterior['valor'];

            $parametros = array(
                "PorcentPrecioAnterior" => $valueParametroPorcentPrecioAnterior,
                "Iva" => "12",
            );
            //dd(json_encode($categorias));
            return view('ecommerce.productos.edit', compact(
                'tabName',
                'marcas',
                'medidas',
                'pesos',
                'categorias',
                'relacionDisponible',
                'relacionados',
                'parametros',
                'producto',
                'tiposImagen'
            ));
        } else {
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producto $producto)
    {
        $constants = new Constants();
        try {

            $validator = Validator::make($request->all(), [
                'Nombre' => 'required',
                'PrecioVentaPublico' => 'numeric',
                'PrecioVentaAnterior' => 'numeric',
            ]);
            if ($validator->fails()) {
                return redirect()->route('productos.edit', $producto)
                    ->withErrors($validator)
                    ->withInput();
            }

            // Verifica si el producto va a ser publicado
            $publicarProducto = ($request->PublicarProducto != null) ? true : false;

            $producto->nombre = $request->Nombre;
            $producto->nombre_alterno = $request->NombreAlterno;
            $producto->slug = Str::slug($request->Nombre, $constants->separatorSlug);
            $producto->sku = $request->Sku;
            $producto->codigo = $request->Codigo;
            $producto->serie = $request->Serie;
            $producto->marca_id = $request->Marca;
            $producto->registra_comentarios = ($request->RegistraComentarios != null) ? true : false;
            $producto->mostrar_slider = ($request->MostrarSlider != null) ? true : false;
            $producto->en_oferta = ($request->Oferta != null) ? true : false;
            $producto->mostrar_precio = ($request->MostrarPrecio != null) ? true : false;
            $producto->pvp = $request->PrecioVentaPublico;
            $producto->incluye_impuestos = ($request->IncluyeImpuestos != null) ? true : false;
            $producto->mostrar_pvp_anterior = ($request->MostrarPrecioVentaAnterior != null) ? true : false;
            $producto->pvp_anterior = $request->PrecioVentaAnterior;
            $producto->resumen = $request->Resumen;
            $producto->detalle = $request->Detalle;
            $producto->published = ($request->PublicarProducto != null) ? true : false;
            $producto->published_at = Carbon::now();
            $producto->save();

            Toastr::success(trans('msg.msgActualizacionExitosa'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto);
        } catch (Exception $ex) {
            Log::critical("Update Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producto $producto)
    {
        try {
            $data = Producto::getProductoById($producto->id);
            if (isset($data)) {
                $data->delete();
                Toastr::success(trans('msg.msgEliminacionCorrecta'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            } else
                Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);

            return redirect()->route('productos.index');
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.index');
        }
    }

    /**
     * Enable/disable a record
     * @param  \App\Models\ Producto  $producto
     */
    public function enabled(Producto $producto)
    {
        try {
            $data = Producto::getProductoById($producto->id);
            if (isset($data)) {
                $data->enabled = !$producto->enabled;
                $data->save();
                Toastr::success(trans('msg.msgActualizacionExitosa'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            } else
                Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);

            return redirect()->route('productos.show', $producto->selector);
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.index');
        }
    }

    public function dataProductType(Request $request)
    {
        $records = Producto::getProductosDatatable();
        return Datatables()
            ->of($records)
            ->addColumn('opc', function ($row) {
                return '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.ver') . '"><a href="' . route('productos.show', $row) . '" class="btn btn-default"><i class="fa fa-eye"></i></a></span>'; //$this->getActionColumn($row);
            })
            ->rawColumns(['opc'])
            ->toJson();
    }

    protected function generateSelector()
    {
        $response = '';
        $util = new Util();
        $time = now()->timestamp;
        $selector = $util->GetSelector(25);
        $data = Producto::select('id')->where('selector', $selector)->first();
        $response = (!isset($data)) ? $selector : $selector . $time;
        return $response;
    }

    public function updateMedidas(Request $request, Producto $producto)
    {
        session([$this->constants->sessionUserShowTab => 'tab-4']);
        try {
            $validator = Validator::make($request->all(), [
                'Largo' => 'numeric',
                'Ancho' => 'numeric',
                'Alto' => 'numeric',
                'Peso' => 'numeric',
            ]);
            if ($validator->fails()) {
                return redirect()->route('productos.edit', $producto)
                    ->withErrors($validator)
                    ->withInput();
            }

            $producto->unidad_medida_id = $request->UnidadMedida;
            $producto->longitud = $request->Largo;
            $producto->ancho = $request->Ancho;
            $producto->alto = $request->Alto;
            $producto->unidad_peso_id = $request->UnidadPeso;
            $producto->peso = $request->Peso;
            $producto->unidades_caja = $request->UnidadesPorCaja;
            $producto->save();
            Toastr::success(trans('msg.msgActualizacionExitosa'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto);
        } catch (Exception $ex) {
            Log::critical("Update Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto)->withInput();
        }
    }

    public function orderSlider(Producto $producto)
    {
        //Log::critical("ORDER: " . $producto->id);
        $productos = Producto::getOrderProductosSlider();
        return view('ecommerce.productos.slider-order')
            ->with('productos', $productos);
    }

    public function updateOrderSlider(Request $request)
    {
        try {
            $ord = 1;
            $arrRecursos = $request->order;
            if ($arrRecursos != null) {
                foreach ($arrRecursos as $row) {
                    $objProducto = Producto::find($row['id']);
                    if (isset($objProducto)) {
                        $objProducto->orden_slider = $ord;
                        $objProducto->save();
                        $ord = $ord + 1;
                    }
                }
            }
            echo '<h5>' . trans('msg.msgRecursosOrdenadosOk') . '</h5>';
        } catch (Exception $ex) {
            Log::critical("Update order Slider Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.slider');
        }
    }

    public function orderOfert(Producto $producto)
    {
        $productos = Producto::getOrderProductosOfert();
        return view('ecommerce.productos.ofert-order')
            ->with('productos', $productos);
    }

    public function updateOrderOfert(Request $request)
    {
        try {
            $ord = 1;
            $arrRecursos = $request->order;
            if ($arrRecursos != null) {
                foreach ($arrRecursos as $row) {
                    $objProducto = Producto::find($row['id']);
                    if (isset($objProducto)) {
                        $objProducto->orden = $ord;
                        $objProducto->save();
                        $ord = $ord + 1;
                    }
                }
            }
            echo '<h5>' . trans('msg.msgRecursosOrdenadosOk') . '</h5>';
        } catch (Exception $ex) {
            Log::critical("Update order Ofert Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.ofert');
        }
    }
}
