<?php

namespace App\Http\Controllers;

use App\Models\{Pedido, PedidoDetalle};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\Constants;
use Toastr;

class PedidoDetallesController extends Controller
{
    protected $dto;

    public function __construct()
    {
        $this->constants = new Constants();
    }

    public function data(Request $request)
    {
        $pedidoId = $request->PedidoId;
        $records = PedidoDetalle::getDetallePedidoByPedidoId($pedidoId);
        return Datatables()
            ->of($records)
            ->addColumn('opc', function ($row) {
                return '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.eliminar') . '"><a href="#" data-href="' . route('pedidoDetalles.delete', $row) . '" data-toggle="modal" data-target="#modal-confirm-delete" data-placement="top" data-name="' . $row->producto_nombre . '" class="btn btn-default"><i class="fa fa-trash"></i></a></span>'; //$this->getActionColumn($row);
            })
            ->rawColumns(['opc'])
            ->toJson();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ PedidoDetalle  $pedidoDetalle
     * @return \Illuminate\Http\Response
     */
    public function destroy(PedidoDetalle $pedidoDetalle)
    {
        try {
            $pedido = Pedido::getPedidoById($pedidoDetalle->pedido_id);
            if (isset($pedido)) {
                $data = PedidoDetalle::find($pedidoDetalle->id);
                if (isset($data)) {
                    $data->delete();
                    Toastr::success(trans('msg.msgEliminacionCorrecta'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
                } else
                    Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            } else
                Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);

            return redirect()->route('pedidos.show', $pedido);
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('pedidos.show', $pedido);
        }
    }
}
