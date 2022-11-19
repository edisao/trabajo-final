<?php

namespace App\Http\Controllers;

use App\Models\{ProductoCategoria, Producto};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Helpers\Constants;
use App\Libraries\Util;
use Toastr;

class ProductoCategoriasController extends Controller
{
    protected $dto;

    public function __construct()
    {
        $this->constants = new Constants();
    }

    public function data(Request $request)
    {
        $productoId = $request->productoId;
        $records = ProductoCategoria::getCategoriasFromProductId($productoId);
        return Datatables()
            ->of($records)
            ->addColumn('opc', function ($row) {
                return '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.eliminar') . '"><a href="#" data-href="' . route('productoCategorias.delete', $row) . '" data-toggle="modal" data-target="#modal-confirm-delete" data-placement="top" data-name="' . $row->nombre . '" class="btn btn-default"><i class="fa fa-trash"></i></a></span> ';
            })
            ->rawColumns(['opc'])
            ->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        session([$this->constants->sessionUserShowTab => 'tab-3']);
        try {
            $productoId = $request->CategoriaProductoId;
            $validator = Validator::make($request->all(), [
                'Categoria' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->route('productoCategorias.create')
                    ->withErrors($validator)
                    ->withInput();
            }
            // Obtiene la información del producto
            $producto = Producto::getProductoById($productoId);

            // Obtiene el numero de categorias registradas al producto
            $totalCategoriasProducto = ProductoCategoria::getCountCategoriasFromProductoId($productoId);

            $productoCategoria = new ProductoCategoria();
            $productoCategoria->producto_id = $productoId;
            $productoCategoria->categoria_id = $request->Categoria;
            $productoCategoria->principal = ($totalCategoriasProducto == 0) ? true : false;;
            $productoCategoria->save();

            if ($totalCategoriasProducto == 0) {
                $producto->categoria_principal_id = $request->Categoria;
                $producto->save();
            }

            Toastr::success(trans('msg.msgRegistroExitoso'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto);
        } catch (Exception $ex) {
            Log::critical("Insert ProductoCategoria Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ ProductoCategoria  $productoCategoria
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductoCategoria $productoCategoria)
    {
        session([$this->constants->sessionUserShowTab => 'tab-3']);
        $util = new Util();
        $principal = false;
        //Log::critical("Insert ProductoCategoria ProductoId: " . $productoCategoria->id);

        $productoId = $productoCategoria->producto_id;
        $producto = Producto::find($productoId);
        try {

            $data = ProductoCategoria::find($productoCategoria->id);
            if (isset($data)) {
                $principal = $data->principal;

                if ($principal) {
                    // Al ser la principal configura la nueva categoria
                    $recurso = ProductoCategoria::query()
                        ->select('producto_categoria.*')
                        ->where('producto_id', $productoId)
                        ->first();
                    if (isset($recurso)) {
                        $dataRecurso = ProductoCategoria::find($recurso->id);
                        $dataRecurso->principal = true;
                        $dataRecurso->save();

                        // Actualiza la categoria principal en la tabla Producto
                        Producto::where('id', $productoId)->update(['categoria_principal_id' => $recurso->id]);
                    } else
                        Producto::where('id', $productoId)->update(['categoria_principal_id' => null]);
                }
                $data->delete();
                Toastr::success(trans('msg.msgEliminacionCorrecta'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);

                // Verifico si existe alguna categoria adicional registrada en el producto
                if (ProductoCategoria::getCountCategoriasFromProductoId($productoId) == 0) {
                    Producto::where('id', $productoId)->update(['categoria_principal_id' => null]);
                }
            } else
                Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);

            return redirect()->route('productos.edit', $producto);
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.productos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('productos.edit', $producto);
        }
    }
}
