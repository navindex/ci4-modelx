<?php

namespace Navindex\ModelX;

use CodeIgniter\Exceptions\ModelException as BaseException;

/**
 * Model Exceptions.
 */
class ModelException extends BaseException
{
	/**
	 * Invalid unique key.
	 *
	 * @param string $columnName Column name
	 * @param string $modelName  Model name
	 */
	public static function forNotUniqueKey(string $columnName, string $modelName)
	{
		return new static(lang('Database.notUniqueKey', [$columnName, $modelName]));
	}

	//--------------------------------------------------------------------

	/**
	 * Invalid composite ID.
	 *
	 * @param string $primaryKeyList Comma separated list of the composite key field names
	 * @param string $modelName      Model name
	 */
	public static function forInvalidCompositeId(string $primaryKeyList, string $modelName)
	{
		return new static(lang('Database.invalidCompositeId', [$primaryKeyList, $modelName]));
	}
}
