<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Funcionalidad
 * 
 * @property int $id
 * @property int $modulo_id
 * @property string $codigo
 * @property string $nombre
 * @property string|null $nombre_mostrar
 * @property string|null $descripcion
 * @property string|null $accion
 * @property string|null $ruta
 * @property string|null $icono_css
 * @property int|null $orden
 * @property bool|null $mostrar_en_menu
 * @property int|null $numero_accesos
 * @property bool|null $validar_acceso
 * @property bool|null $registrar_acceso
 * @property bool|null $notificar_alerta
 * @property bool|null $enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Modulo $modulo
 * @property Collection|Rol[] $rols
 *
 * @package App\Models
 */
class Funcionalidad extends Model
{
	use SoftDeletes;
	protected $table = 'funcionalidad';

	protected $casts = [
		'modulo_id' => 'int',
		'orden' => 'int',
		'mostrar_en_menu' => 'bool',
		'numero_accesos' => 'int',
		'validar_acceso' => 'bool',
		'registrar_acceso' => 'bool',
		'notificar_alerta' => 'bool',
		'enabled' => 'bool'
	];

	protected $fillable = [
		'modulo_id',
		'codigo',
		'nombre',
		'nombre_mostrar',
		'descripcion',
		'accion',
		'ruta',
		'icono_css',
		'orden',
		'mostrar_en_menu',
		'numero_accesos',
		'validar_acceso',
		'registrar_acceso',
		'notificar_alerta',
		'enabled'
	];

	public static function getFunctionalityByModuleId($moduleId)
	{
		$sqlData = Funcionalidad::query()
			->select('funcionalidad.id', 'funcionalidad.codigo', 'funcionalidad.nombre_mostrar', 'funcionalidad.orden', 'funcionalidad.ruta', 'funcionalidad.notificar_alerta', 'modulo.codigo AS modulo_codigo', 'modulo.nombre AS modulo_nombre')
			->join('modulo', 'funcionalidad.modulo_id', '=', 'modulo.id')
			->where('funcionalidad.modulo_id', $moduleId)
			->where('funcionalidad.enabled', true)
			->where('funcionalidad.mostrar_en_menu', true)
			->orderBy('funcionalidad.orden', 'asc')
			->get();
		return $sqlData;
	}

	public static function registrarAccesoFuncionalidad($funcionalidadId)
	{
		$data = Funcionalidad::Find($funcionalidadId);
		if (isset($data)) {
			$data->numero_accesos = $data->numero_accesos + 1;
			$data->save();
		}
	}

	public static function getFuncionalidadByRolIdFuncionalidadId($rolId, $funcionalidadId)
	{
		$sqlData = RolFuncionalidad::query()
			->select('rol_funcionalidad.id')
			->where('rol_funcionalidad.rol_id', $rolId)
			->where('rol_funcionalidad.funcionalidad_id', $funcionalidadId)
			->first();
		return $sqlData;
	}

	public static function getModuloByRolId($rolId)
	{
		$sqlData = RolFuncionalidad::query()
			->select('modulo.id', 'modulo.codigo', 'modulo.nombre', 'modulo.orden', 'modulo.icono_css')
			->join('funcionalidad', 'rol_funcionalidad.funcionalidad_id', '=', 'funcionalidad.id')
			->join('modulo', 'funcionalidad.modulo_id', '=', 'modulo.id')
			->where('rol_funcionalidad.rol_id', $rolId)
			->where('modulo.enabled', true)
			->orderBy('modulo.orden', 'asc')
			->distinct()
			->get();
		return $sqlData;
	}

	public function modulo()
	{
		return $this->belongsTo(Modulo::class);
	}

	public function rols()
	{
		return $this->belongsToMany(Rol::class, 'rol_funcionalidad')
			->withPivot('id', 'deleted_at')
			->withTimestamps();
	}
}
