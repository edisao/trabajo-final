<?php

namespace App\Http\Controllers;

use App\Models\{Pedido, PedidoDetalle};
use App\Models\{ItemCatalogo};
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Helpers\Constants;
use Toastr;
use Carbon\Carbon;

class PedidosController extends Controller
{
    protected $dto;

    public function __construct()
    {
        $this->constants = new Constants();
    }

    public function index()
    {
        return view('ecommerce.pedidos.index');
    }

    public function data(Request $request)
    {
        $records = Pedido::getPedidosActivos();
        return Datatables()
            ->of($records)
            ->addColumn('numero', function ($row) {
                return '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.ver') . '"><strong><a href="' . route('pedidos.show', $row) . '" class="">' . 'PED-' . $row->id . '</a></strong></span> ';
            })
            ->addColumn('estado', function ($row) {
                return '<span class="badge badge-info">' . $row->estado_nombre . '</span>';
            })
            ->addColumn('cliente', function ($row) {
                return $row->nombres . ' ' . $row->apellidos;
            })
            ->addColumn('fecha', function ($row) {
                $fechaFinPedido = (!empty($row->fecha_fin_pedido) ? 'Fin: ' . Carbon::parse($row->fecha_fin_pedido)->isoFormat('DD MMM YYYY') : '');
                return 'Inicio: ' . Carbon::parse($row->fecha_inicia_pedido)->isoFormat('DD MMM YYYY') . '<br />' . $fechaFinPedido;
            })
            ->addColumn('opc', function ($row) {
                return '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.ver') . '"><a href="' . route('pedidos.show', $row) . '" class="btn btn-default"><i class="fa fa-eye"></i></a></span> '; //$this->getActionColumn($row);
            })
            ->rawColumns(['opc', 'numero', 'estado', 'fecha'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pedidos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'Nombre' => 'required',
                'Codigo' => 'required|unique:pedido,codigo',
            ]);
            if ($validator->fails()) {
                return redirect()->route('pedidos.create')
                    ->withErrors($validator)
                    ->withInput();
            }

            $pedido = new Pedido();
            $pedido->nombre = $request->Nombre;
            $pedido->codigo = $request->Codigo;
            $pedido->descripcion = $request->Descripcion;
            $pedido->save();
            Toastr::success(trans('msg.msgRegistroExitoso'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('pedidos.index');
        } catch (Exception $ex) {
            Log::critical("Insert Pedido Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('pedidos.create')->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function show(Pedido $pedido)
    {
        $dataPedido = Pedido::getPedidoById($pedido->id);
        if (isset($dataPedido)) {
            $detallePedido = PedidoDetalle::getDetallePedidoByPedidoId($pedido->id);
            return view('ecommerce.pedidos.show')
                ->with('pedido', $dataPedido)
                ->with('detallePedido', $detallePedido);
        } else {
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('pedidos.edit', $pedido)->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function edit(Pedido $pedido)
    {
        $dataPedido = Pedido::getPedidoById($pedido->id);
        if (isset($dataPedido)) {
            $estadoPedido = ItemCatalogo::getItemCatalogosByCatalogoCode("ESTADO_PEDIDO_STORE");
            return view('ecommerce.pedidos.edit')
                ->with('estados', $estadoPedido)
                ->with('pedido', $dataPedido);
        } else {
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('pedidos.index')->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pedido $pedido)
    {
        try {

            $validator = Validator::make($request->all(), [
                'Estado' => 'required'
            ]);
            if ($validator->fails()) {
                return redirect()->route('pedidos.edit', $pedido)
                    ->withErrors($validator)
                    ->withInput();
            }
            $pedido->estado_id = $request->Estado;
            $pedido->informacion_adicional = $request->ObservacionesAdicionales;
            $pedido->save();
            Toastr::success(trans('msg.msgActualizacionExitosa'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('pedidos.show', $pedido);
        } catch (Exception $ex) {
            Log::critical("Update Pedido Exception: " . $ex->getMessage() . '---' . $ex);
            Toastr::error(trans('msg.errProcesarInformacion') . ': ' . $ex->getMessage(), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('pedidos.edit', $pedido)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ Pedido  $pedido
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pedido $pedido)
    {
        try {
            $data = Pedido::find($pedido->id);
            if (isset($data)) {
                $data->delete();
                Toastr::success(trans('msg.msgEliminacionCorrecta'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            } else
                Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);

            return redirect()->route('pedidos.index');
        } catch (Exception $e) {
            Log::critical("Exception: " . $e);
            Toastr::error(trans('msg.errProcesarInformacion'), trans('ecommerce.pedidos'), $this->constants->notificationDefaultOptions);
            return redirect()->route('pedidos.index');
        }
    }

    public function indexOther()
    {
        return view('ecommerce.pedidos.other');
    }

    public function dataOther(Request $request)
    {
        $records = Pedido::getPedidosSitio();
        return Datatables()
            ->of($records)
            ->addColumn('numero', function ($row) {
                return '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.ver') . '"><strong><a href="' . route('pedidos.show', $row) . '" class="">' . 'PED-' . $row->id . '</a></strong></span> ';
            })
            ->addColumn('estado', function ($row) {
                return '<span class="badge badge-info">' . $row->estado_nombre . '</span>';
            })
            ->addColumn('cliente', function ($row) {
                return $row->nombres . ' ' . $row->apellidos;
            })
            ->addColumn('fecha', function ($row) {
                $fechaFinPedido = (!empty($row->fecha_fin_pedido) ? 'Fin: ' . Carbon::parse($row->fecha_fin_pedido)->isoFormat('DD MMM YYYY') : '');
                return 'Inicio: ' . Carbon::parse($row->fecha_inicia_pedido)->isoFormat('DD MMM YYYY') . '<br />' . $fechaFinPedido;
            })
            ->addColumn('opc', function ($row) {
                return '<span class="spacerMenu" data-toggle="tooltip" data-placement="top" title="' . trans('labels.ver') . '"><a href="' . route('pedidos.show', $row) . '" class="btn btn-default"><i class="fa fa-eye"></i></a></span> '; //$this->getActionColumn($row);
            })
            ->rawColumns(['opc', 'numero', 'estado', 'fecha'])
            ->toJson();
    }
}
