<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PersonaDireccion
 * 
 * @property int $id
 * @property int $persona_id
 * @property int $pais_id
 * @property string|null $provincia
 * @property string|null $ciudad
 * @property string|null $direccion
 * @property string|null $zip
 * @property string|null $telefono
 * @property bool|null $principal
 * @property bool|null $enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property ItemCatalogo $item_catalogo
 * @property Persona $persona
 *
 * @package App\Models
 */
class PersonaDireccion extends Model
{
	use SoftDeletes;
	protected $table = 'persona_direccion';

	protected $casts = [
		'persona_id' => 'int',
		'pais_id' => 'int',
		'principal' => 'bool',
		'enabled' => 'bool'
	];

	protected $fillable = [
		'persona_id',
		'pais_id',
		'provincia',
		'ciudad',
		'direccion',
		'zip',
		'telefono',
		'principal',
		'enabled'
	];

	public function item_catalogo()
	{
		return $this->belongsTo(ItemCatalogo::class, 'pais_id');
	}

	public function persona()
	{
		return $this->belongsTo(Persona::class);
	}
}
