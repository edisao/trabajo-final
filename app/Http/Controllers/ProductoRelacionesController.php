<?php

namespace App\Http\Controllers;

use App\Models\{ProductoRelacion, Producto};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\Constants;
use Toastr;

class ProductoRelacionesController extends Controller
{
    protected $dto;

    public function __construct()
    {
        $this->constants = new Constants();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        session([$this->constants->sessionUserShowTab => 'tab-5']);
        try {
            $productoId = $request->RelacionProductoId;
            // Obtiene la información del producto
            $producto = Producto::getProductoById($productoId);

            // Elimina las relaciones previamente generadas
            $deleteRelations = ProductoRelacion::deleteRelationsByProductId($productoId);

            $arrProductosRelacionados = $request->ProductosRelacionados;
            if (isset($arrProductosRelacionados)) {
                foreach ($arrProductosRelacionados as $row) {
                    $productoRelacion = new ProductoRelacion();
                    $productoRelacion->producto_id = $productoId;
                    $productoRelacion->relacion_producto_id = $row;
                    $productoRelacion->save();
                    //Log::critical("Insert ProductoRelacion: " . 'producto_id: ' . $productoId . ' --- ' . 'relacion_producto_id: ' . $row);
                }
            }
            Toastr::success(trans('msg.msgRegistroExitoso'), trans('ecommerce.productosRelacionados'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto);
        } catch (Exception $ex) {
            Log::critical("Insert ProductoRelacion Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productosRelacionados'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto)->withInput();
        }
    }

    public function data(Request $request)
    {
        $productoId = $request->productoId;
        $records = Producto::getProductosRelacionadosByProductoId($productoId);
        return Datatables()
            ->of($records)
            ->toJson();
    }
}
