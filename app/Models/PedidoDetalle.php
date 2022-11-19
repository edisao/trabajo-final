<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PedidoDetalle
 * 
 * @property int $id
 * @property int $pedido_id
 * @property int $estado_id
 * @property int|null $producto_id
 * @property string|null $producto_nombre
 * @property int|null $marca_id
 * @property string|null $marca_nombre
 * @property bool|null $oferta
 * @property int|null $cantidad
 * @property float|null $precio
 * @property float|null $total
 * @property string|null $url_imagen
 * @property string $selector
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property ItemCatalogo $item_catalogo
 * @property MarcaProducto|null $marca_producto
 * @property Pedido $pedido
 * @property Producto|null $producto
 *
 * @package App\Models
 */
class PedidoDetalle extends Model
{
	use SoftDeletes;
	protected $table = 'pedido_detalle';

	protected $casts = [
		'pedido_id' => 'int',
		'estado_id' => 'int',
		'producto_id' => 'int',
		'marca_id' => 'int',
		'oferta' => 'bool',
		'cantidad' => 'int',
		'precio' => 'float',
		'total' => 'float'
	];

	protected $fillable = [
		'pedido_id',
		'estado_id',
		'producto_id',
		'producto_nombre',
		'marca_id',
		'marca_nombre',
		'oferta',
		'cantidad',
		'precio',
		'total',
		'url_imagen',
		'selector'
	];

	public function item_catalogo()
	{
		return $this->belongsTo(ItemCatalogo::class, 'estado_id');
	}

	public function marca_producto()
	{
		return $this->belongsTo(MarcaProducto::class, 'marca_id');
	}

	public function pedido()
	{
		return $this->belongsTo(Pedido::class);
	}

	public function producto()
	{
		return $this->belongsTo(Producto::class);
	}
}
