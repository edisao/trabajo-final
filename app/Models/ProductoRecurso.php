<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductoRecurso
 * 
 * @property int $id
 * @property int $producto_id
 * @property int $tipo_id
 * @property string $nombre
 * @property string|null $archivo
 * @property string|null $url
 * @property string|null $url_thum
 * @property string|null $url_thum_adicional
 * @property string|null $descripcion
 * @property bool|null $visible
 * @property int|null $orden
 * @property bool|null $principal
 * @property string|null $selector
 * @property bool|null $enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Producto $producto
 * @property ItemCatalogo $item_catalogo
 *
 * @package App\Models
 */
class ProductoRecurso extends Model
{
	use SoftDeletes;
	protected $table = 'producto_recurso';

	protected $casts = [
		'producto_id' => 'int',
		'tipo_id' => 'int',
		'visible' => 'bool',
		'orden' => 'int',
		'principal' => 'bool',
		'enabled' => 'bool'
	];

	protected $fillable = [
		'producto_id',
		'tipo_id',
		'nombre',
		'archivo',
		'url',
		'url_thum',
		'url_thum_adicional',
		'descripcion',
		'visible',
		'orden',
		'principal',
		'selector',
		'enabled'
	];

	public static function getCountImagesFromProductoId($productoId)
	{
		$data = ProductoRecurso::query()
			->select('producto_recurso.id')
			->join('item_catalogo', 'producto_recurso.tipo_id', '=', 'item_catalogo.id')
			->where('item_catalogo.codigo', 'IMAGEN')
			->where('producto_recurso.producto_id', $productoId)
			->where('producto_recurso.enabled', true)
			->count();
		return $data;
	}

	public static function getImagesFromProductId($productoId)
	{
		$data = ProductoRecurso::query()
			->select('producto_recurso.id', 'producto_recurso.nombre', 'producto_recurso.archivo', 'producto_recurso.url', 'producto_recurso.descripcion', 'producto_recurso.producto_id', 'producto_recurso.principal', 'producto_recurso.enabled', 'producto_recurso.visible', 'item_catalogo.nombre AS tipo_nombre')
			->join('item_catalogo', 'producto_recurso.tipo_id', '=', 'item_catalogo.id')
			//->where('item_catalogo.codigo', 'IMAGEN')
			->where('producto_recurso.producto_id', $productoId)
			->orderBy('producto_recurso.orden', 'asc')
			->get();
		return $data;
	}

	public function producto()
	{
		return $this->belongsTo(Producto::class);
	}

	public function item_catalogo()
	{
		return $this->belongsTo(ItemCatalogo::class, 'tipo_id');
	}
}
