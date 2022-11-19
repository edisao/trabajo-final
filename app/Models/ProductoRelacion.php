<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductoRelacion
 * 
 * @property int $id
 * @property int $producto_id
 * @property int $relacion_producto_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Producto $producto
 *
 * @package App\Models
 */
class ProductoRelacion extends Model
{
	use SoftDeletes;
	protected $table = 'producto_relacion';

	protected $casts = [
		'producto_id' => 'int',
		'relacion_producto_id' => 'int'
	];

	protected $fillable = [
		'producto_id',
		'relacion_producto_id'
	];

	public function producto()
	{
		return $this->belongsTo(Producto::class, 'relacion_producto_id');
	}
}
