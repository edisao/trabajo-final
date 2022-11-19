<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductoCategorium
 * 
 * @property int $id
 * @property int $producto_id
 * @property int $categoria_id
 * @property bool|null $principal
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property CategoriaProducto $categoria_producto
 * @property Producto $producto
 *
 * @package App\Models
 */
class ProductoCategoria extends Model
{
	use SoftDeletes;
	protected $table = 'producto_categoria';

	protected $casts = [
		'producto_id' => 'int',
		'categoria_id' => 'int',
		'principal' => 'bool'
	];

	protected $fillable = [
		'producto_id',
		'categoria_id',
		'principal'
	];

	public static function getCategoriasFromProductId($productoId)
	{
		$data = ProductoCategoria::query()
			->select('producto_categoria.id', 'producto_categoria.producto_id', 'producto_categoria.categoria_id', 'producto_categoria.principal', 'categoria_producto.nombre')
			->join('categoria_producto', 'producto_categoria.categoria_id', '=', 'categoria_producto.id')
			->where('producto_categoria.producto_id', $productoId)
			->orderBy('categoria_producto.nombre', 'asc')
			->get();
		return $data;
	}

	public static function getCountCategoriasFromProductoId($productoId)
	{
		$data = ProductoCategoria::query()
			->select('producto_categoria.id')
			->where('producto_categoria.producto_id', $productoId)
			->count();
		return $data;
	}

	public function categoria_producto()
	{
		return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
	}

	public function producto()
	{
		return $this->belongsTo(Producto::class);
	}
}
