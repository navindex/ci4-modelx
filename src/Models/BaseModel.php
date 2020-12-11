<?php

/**
 * Navindex:ModelX.
 * Model extension for CodeIgniter 4.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Navindex Pty Ltd
 * @copyright Copyright (c) 2020 Navindex Pty Ltd
 */

namespace Navindex\ModelX\Models;

use CodeIgniter\Database\BaseResult;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Exceptions\DataException;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;
use Navindex\ModelX\Exceptions\ModelException;

/**
 * Abstract class BaseModel.
 *
 * The BaseModel class provides a number of convenient features that
 * extend CodeIgniter's Model class.
 */
abstract class BaseModel extends Model
{
	/**
	 * Database Connection.
	 *
	 * @var \CodeIgniter\Database\ConnectionInterface
	 */
	protected $db;

	/**
	 * The table's primary key. An array of fields for composite key.
	 *
	 * @var array|string
	 */
	protected $primaryKey = 'id';

	/**
	 * The table's alternate keys (unique keys). Each can be single or multi fields.
	 * For composite alternate key, the array element will be sub-array.
	 *
	 * @var array
	 */
	protected $altKeys = [];

	/**
	 * The $deletedField value when the record is deleted.
	 * If null it follows the original CodeIgniter logic.
	 *
	 * @var string
	 */
	protected $deletedValue;

	/**
	 * The $deletedField value when the record is deleted.
	 * If null it follows the original CodeIgniter logic.
	 *
	 * @var string
	 */
	protected $notDeletedValue;

	/**
	 * For composite primary keys, the field to use for auto increment.
	 * This field must be part of the composite primary key.
	 *
	 * @var string
	 */
	protected $autoIncrementField;

	/**
	 * Last insert ID or array of IDs for composite key.
	 *
	 * @var array|int|string
	 */
	protected $insertID;

	//--------------------------------------------------------------------

	/**
	 * Model constructor.
	 *
	 * @param ConnectionInterface $db
	 * @param ValidationInterface $validation
	 */
	public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
	{
		parent::__construct($db, $validation);
		helper(['array', 'object']);
	}

	//--------------------------------------------------------------------

	/**
	 * Fetches the row of database from $this->table with a primary key
	 * matching $id.
	 * Changed to support composite keys.
	 * Changed to support deleted field values.
	 *
	 * @param null|array|int|string $id A primary key or an array of primary keys
	 *                                  For composite primary key, array or multi-dimensional array
	 *
	 * @return null|array|object the resulting row of data, or null
	 */
	public function find($id = null)
	{
		$composite = \is_array($this->primaryKey);

		if ($composite) {
			$singleton = false;

			if (\is_numeric($id) || \is_string($id)) {
				// Single column partial key
				$key = $this->primaryKey[0];
				$composite = false;
			} elseif (\is_array($id)) {
				$keyCount = \count($this->primaryKey);
				$idCount = \count($id);

				if ($keyCount > $idCount) {
					// Composite partial key
					$key = \array_slice($this->primaryKey, 0, $idCount);
					$singleton = false;
				} else {
					// Ignore the extra $id items if they exist
					$id = \array_slice($id, 0, $keyCount);
					$key = $this->primaryKey;
					$singleton = is_multi_array($id);
				}
			}
		} else {
			$singleton = \is_numeric($id) || \is_string($id);
		}

		if ($this->tempAllowCallbacks) {
			// Call the before event and check for a return
			$eventData = $this->trigger('beforeFind', [
				'id'        => $id,
				'method'    => 'find',
				'singleton' => $singleton,
			]);

			if (!empty($eventData['returnData'])) {
				return $eventData['data'];
			}
		}

		$builder = $this->builder();

		if ($this->tempUseSoftDeletes) {
			$builder->where($this->prefixed($this->deletedField), $this->deletedValue ?? null);
		}

		// Prepare the query and get the result(s)
		if ($composite) {
			if ($singleton) {
				// Add the whole primary key in an associative array
				$builder->where(\array_combine($this->prefixed($key), $id));
			} else {
				// Add the key columns one by one
				foreach ($key as $k=>$column) {
					$value = $id[$k];
					$column = $this->prefixed($column);
					\is_array($value)
						? $builder->whereIn($column, $value)
						: $builder->where($column, $value);
				}
			}
		} elseif ($singleton) {
			// Single column with a single value
			$builder->where($this->prefixed($key), $id);
		} elseif (\is_array($id)) {
			// Single column with an array of values
			$builder->whereIn($this->prefixed($key), $id);
		}

		// Retrieve the result
		$result = $singleton
			? $builder->get()->getFirstRow($this->tempReturnType)
			: $builder->get()->getResult($this->tempReturnType);

		$eventData = [
			'id'        => $id,
			'data'      => $result,
			'method'    => 'find',
			'singleton' => $singleton,
		];

		// Call the after event
		if ($this->tempAllowCallbacks) {
			$eventData = $this->trigger('afterFind', $eventData);
		}

		$this->tempReturnType = $this->returnType;
		$this->tempUseSoftDeletes = $this->useSoftDeletes;
		$this->tempAllowCallbacks = $this->allowCallbacks;

		return $eventData['data'];
	}

	//--------------------------------------------------------------------

	/**
	 * Works with the current Query Builder instance to return
	 * all results, while optionally limiting them.
	 * Changed to support deleted field values.
	 *
	 * @param int $limit  The maximum number of results that will be returned
	 * @param int $offset From where to start returning data
	 *
	 * @return array Array of records
	 */
	public function findAll(int $limit = 0, int $offset = 0)
	{
		if ($this->tempAllowCallbacks) {
			// Call the before event and check for a return
			$eventData = $this->trigger('beforeFind', [
				'method'    => 'findAll',
				'limit'     => $limit,
				'offset'    => $offset,
				'singleton' => false,
			]);

			if (!empty($eventData['returnData'])) {
				return $eventData['data'];
			}
		}

		$builder = $this->builder();

		if ($this->tempUseSoftDeletes) {
			$builder->where($this->prefixed($this->deletedField), $this->deletedValue ?? null);
		}

		$row = $builder->limit($limit, $offset)
			->get()
			->getResult($this->tempReturnType)
		;

		$eventData = [
			'data'      => $row,
			'limit'     => $limit,
			'offset'    => $offset,
			'method'    => 'findAll',
			'singleton' => false,
		];

		if ($this->tempAllowCallbacks) {
			$eventData = $this->trigger('afterFind', $eventData);
		}

		$this->tempReturnType = $this->returnType;
		$this->tempUseSoftDeletes = $this->useSoftDeletes;
		$this->tempAllowCallbacks = $this->allowCallbacks;

		return $eventData['data'];
	}

	//--------------------------------------------------------------------

	/**
	 * Fetches the row of database from $this->table with an alternate key
	 * (unique key) matching $altValue.
	 * If this method was called with the primary key, the find() method will be
	 * executed.
	 *
	 * findAlt('column', 'value') is the same as
	 * findAlt(['column' => 'value']);
	 *
	 * findAlt('column', ['value1', 'value2']) is the same as
	 * findAlt('column' => ['value1', 'value2']);
	 *
	 * findAlt(['column1', 'column2'], ['value1', 'value2']) is the same as
	 * findAlt(['column1' => 'value1', 'column2' => 'value2']);
	 *
	 * findAlt(['column1', 'column2'], [['value1', 'value2'], ['value3', 'value4']]) is the same as
	 * findAlt(['column1' => ['value1', 'value2'], 'column2' => ['value3', 'value4']]);
	 *
	 * @param array|string     $altKey Alternate key column, array of columns, or a key=>value array
	 * @param null|array|mixed $value  A value or an array of values
	 *
	 * @throws \App\Exceptions\ModelException
	 *
	 * @return null|array|object the resulting row of data, or null
	 */
	public function findAlt($altKey, $value = null)
	{
		// Separate key and value(s) if it was called with an associative array
		if (\is_null($value) && \is_array($altKey)) {
			$value = \array_values($altKey);
			$altKey = \array_keys($altKey);
		}

		// Call find() method if it's the primary key
		if (
			$this->primaryKey == $altKey ||
			(\is_array($this->primaryKey) && empty(\array_diff($this->primaryKey, $altKey)))
		) {
			return $this->find($value);
		}

		// Check whether altKey is valid
		if (\is_array($altKey)) {
			// Check if the key is valid composite key
			foreach ($this->altKeys as $k) {
				if (empty(\array_diff($k, $altKey))) {
					$found = true;

					break;
				}
			}
			if (!$found) {
				throw ModelException::forNotUniqueKey(\implode(', ', $altKey), \get_class($this));
			}
		} elseif (!\in_array($altKey, $this->altKeys)) {
			throw ModelException::forNotUniqueKey((string) $altKey, \get_class($this));
		}

		// Temporarily replace the primary key with the alternate key
		$tempPrimaryKey = $this->primaryKey;
		$this->primaryKey = $altKey;

		// Call find()
		$result = $this->find($value);

		// Restore the original primary key
		$this->primaryKey = $tempPrimaryKey;

		return $result;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the first row of the result set. Will take any previous
	 * Query Builder calls into account when determining the result set.
	 *
	 * @return null|array|object
	 */
	public function first()
	{
		if ($this->tempAllowCallbacks) {
			// Call the before event and check for a return
			$eventData = $this->trigger('beforeFind', [
				'method'    => 'first',
				'singleton' => true,
			]);

			if (!empty($eventData['returnData'])) {
				return $eventData['data'];
			}
		}

		$builder = $this->builder();

		if ($this->tempUseSoftDeletes) {
			$builder->where($this->prefixed($this->deletedField), $this->deletedValue ?? null);
		} else {
			if ($this->useSoftDeletes && empty($builder->QBGroupBy) && $this->primaryKey) {
				$builder->groupBy($this->prefixed($this->primaryKey));
			}
		}

		// Some databases, like PostgreSQL, need order
		// information to consistently return correct results.
		if ($builder->QBGroupBy && empty($builder->QBOrderBy) && $this->primaryKey) {
			$orderBy = \is_array($this->primaryKey)
				? \implode(' ASC, ', $this->prefixed($this->primaryKey)) . ' ASC'
				: $this->prefixed($this->primaryKey) . ' ASC';
			$builder->orderBy($orderBy);
		}

		$eventData = [
			'data'      => $builder->limit(1, 0)->get()->getFirstRow($this->tempReturnType),
			'method'    => 'first',
			'singleton' => true,
		];

		if ($this->tempAllowCallbacks) {
			$eventData = $this->trigger('afterFind', $eventData);
		}

		$this->tempReturnType = $this->returnType;
		$this->tempUseSoftDeletes = $this->useSoftDeletes;
		$this->tempAllowCallbacks = $this->allowCallbacks;

		return $eventData['data'];
	}

	//--------------------------------------------------------------------

	/**
	 * A convenience method that will attempt to determine whether the
	 * data should be inserted or updated. Will work with either
	 * an array or object. When using with custom class objects,
	 * you must ensure that the class will provide access to the class
	 * variables, even if through a magic method.
	 *
	 * @param array|object $data
	 *
	 * @throws ReflectionException
	 *
	 * @return bool
	 */
	public function save($data): bool
	{
		if (empty($data)) {
			return true;
		}

		// Call the parent for single column
		if (!\is_array($this->primaryKey)) {
			return parent::save($data);
		}

		// Populate the where clouse with the composite key.
		$primaryKey = \is_object($data)
			? extract_properties($data, $this->primaryKey)
			: array_mask($data, $this->primaryKey);

		// Remove null values
		$primaryKey = \array_filter($primaryKey);

		// When useAutoIncrement feature is disabled, update if
		// the given record already exists, otherwise insert
		if (
			!$this->useAutoIncrement &&
			array_all_keys_set($this->primaryKey, $primaryKey) &&
			1 === $this->where($primaryKey)->countAllResults()
		) {
			$response = $this->update($primaryKey, $data);
		} else {
			$response = $this->insert($data, false);

			if ($response instanceof BaseResult) {
				$response = false !== $response->resultID;
			} elseif (false !== $response) {
				$response = true;
			}
		}

		return $response;
	}

	//--------------------------------------------------------------------

	/**
	 * Takes a class and returns an array of it's public and protected
	 * properties as an array suitable for use in creates and updates.
	 *
	 * @param object|string     $data
	 * @param null|array|string $primaryKey
	 * @param string            $dateFormat
	 * @param bool              $onlyChanged
	 *
	 * @throws \ReflectionException
	 *
	 * @return array
	 */
	public static function classToArray($data, $primaryKey = null, string $dateFormat = 'datetime', bool $onlyChanged = true): array
	{
		if (\method_exists($data, 'toRawArray')) {
			$properties = $data->toRawArray($onlyChanged);
			$keys = empty($primaryKey) ? [] : (array) $primaryKey;

			// Always grab the primary key otherwise updates will fail.
			if (
				!empty($properties) &&
				!empty($primaryKey) &&
				!array_all_keys_exist($keys, $properties) &&
				any_property_exists($keys, $data)
			) {
				// Populate the available primary key columns, some might be null
				$properties = \array_merge($properties, extract_properties($data, $keys));
			}
		} else {
			$mirror = new \ReflectionClass($data);
			$props = $mirror->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

			$properties = [];

			// Loop over each property,
			// saving the name/value in a new array we can return.
			foreach ($props as $prop) {
				// Must make protected values accessible.
				$prop->setAccessible(true);
				$properties[$prop->getName()] = $prop->getValue($data);
			}
		}

		// Convert any Time instances to appropriate $dateFormat
		if ($properties) {
			foreach ($properties as $key => $value) {
				if ($value instanceof Time) {
					switch ($dateFormat) {
						case 'datetime':
							$converted = $value->format('Y-m-d H:i:s');

							break;
						case 'date':
							$converted = $value->format('Y-m-d');

							break;
						case 'int':
							$converted = $value->getTimestamp();

							break;
						default:
							$converted = (string) $value;
					}

					$properties[$key] = $converted;
				}
			}
		}

		return $properties;
	}

	//--------------------------------------------------------------------

	/**
	 * Inserts data into the current table. If an object is provided,
	 * it will attempt to convert it to an array.
	 *
	 * @param array|object $data
	 * @param bool         $returnID Whether insert ID should be returned or not
	 *
	 * @throws ReflectionException
	 *
	 * @return BaseResult|false|int|string
	 */
	public function insert($data = null, bool $returnID = true)
	{
		$escape = null;

		$this->insertID = 0;

		if (empty($data)) {
			$data = $this->tempData['data'] ?? null;
			$escape = $this->tempData['escape'] ?? null;
			$this->tempData = [];
		}

		if (empty($data)) {
			throw DataException::forEmptyDataset('insert');
		}

		// If $data is using a custom class with public or protected
		// properties representing the table elements, we need to grab
		// them as an array.
		if (\is_object($data) && !$data instanceof \stdClass) {
			$data = static::classToArray($data, $this->primaryKey, $this->dateFormat, false);
		}

		// If it's still a stdClass, go ahead and convert to
		// an array so doProtectFields and other model methods
		// don't have to do special checks.
		if (\is_object($data)) {
			$data = (array) $data;
		}

		if (empty($data)) {
			throw DataException::forEmptyDataset('insert');
		}

		// Validate data before saving.
		if (!$this->skipValidation && !$this->cleanRules()->validate($data)) {
			return false;
		}

		// Must be called first so we don't
		// strip out created_at values.
		$data = $this->doProtectFields($data);

		// Set created_at and updated_at with same time
		$date = $this->setDate();

		if ($this->useTimestamps && $this->createdField && !\array_key_exists($this->createdField, $data)) {
			$data[$this->createdField] = $date;
		}

		if ($this->useTimestamps && $this->updatedField && !\array_key_exists($this->updatedField, $data)) {
			$data[$this->updatedField] = $date;
		}

		$eventData = ['data' => $data];

		if ($this->tempAllowCallbacks) {
			$eventData = $this->trigger('beforeInsert', $eventData);
		}

		// Require non empty primaryKey when
		// not using auto-increment feature
		if (!$this->useAutoIncrement && !array_all_keys_set($this->primaryKey, $eventData['data'])) {
			throw DataException::forEmptyPrimaryKey('insert');
		}

		// Must use the set() method to ensure objects get converted to arrays
		$result = $this->builder()
			->set($eventData['data'], '', $escape)
			->insert()
		;

		// If insertion succeeded then save the insert ID
		if ($result->resultID) {
			if (!$this->useAutoIncrement) {
				$this->insertID = array_mask($eventData['data'], $this->primaryKey);
			} else {
				$this->insertID = $this->db->insertID(); // @phpstan-ignore-line
			}
		}

		$eventData = [
			'id'     => $this->insertID,
			'data'   => $eventData['data'],
			'result' => $result,
		];

		if ($this->tempAllowCallbacks) {
			// Trigger afterInsert events with the inserted data and new ID
			$this->trigger('afterInsert', $eventData);
		}

		$this->tempAllowCallbacks = $this->allowCallbacks;

		// If insertion failed, get out of here
		if (!$result) {
			return $result;
		}

		// otherwise return the insertID, if requested.
		return $returnID ? $this->insertID : $result;
	}

	//--------------------------------------------------------------------

	/**
	 * Compiles batch insert strings and runs the queries, validating each row prior.
	 *
	 * @param array $set       An associative array of insert values
	 * @param bool  $escape    Whether to escape values and identifiers
	 * @param int   $batchSize The size of the batch to run
	 * @param bool  $testing   True means only number of records is returned, false will execute the query
	 *
	 * @return bool|int Number of rows inserted or FALSE on failure
	 */
	public function insertBatch(array $set = null, bool $escape = null, int $batchSize = 100, bool $testing = false)
	{
		if (\is_array($set)) {
			foreach ($set as &$row) {
				// If $data is using a custom class with public or protected
				// properties representing the table elements, we need to grab
				// them as an array.
				if (\is_object($row) && !$row instanceof \stdClass) {
					$row = static::classToArray($row, $this->primaryKey, $this->dateFormat, false);
				}

				// If it's still a stdClass, go ahead and convert to
				// an array so doProtectFields and other model methods
				// don't have to do special checks.
				if (\is_object($row)) {
					$row = (array) $row;
				}

				// Validate every row..
				if (!$this->skipValidation && !$this->cleanRules()->validate($row)) {
					return false;
				}

				// Must be called first so we don't
				// strip out created_at values.
				$row = $this->doProtectFields($row);

				// Require non empty primaryKey when
				// not using auto-increment feature
				if (!$this->useAutoIncrement && !array_all_keys_set($this->primaryKey, $row)) {
					throw DataException::forEmptyPrimaryKey('insertBatch');
				}

				// Set created_at and updated_at with same time
				$date = $this->setDate();

				if ($this->useTimestamps && $this->createdField && !\array_key_exists($this->createdField, $row)) {
					$row[$this->createdField] = $date;
				}

				if ($this->useTimestamps && $this->updatedField && !\array_key_exists($this->updatedField, $row)) {
					$row[$this->updatedField] = $date;
				}
			}
		}

		return $this->builder()->testMode($testing)->insertBatch($set, $escape, $batchSize);
	}

	//--------------------------------------------------------------------

	/**
	 * Updates a single record in $this->table. If an object is provided,
	 * it will attempt to convert it into an array.
	 *
	 * @param null|array|int|string $id
	 * @param null|array|object     $data
	 *
	 * @throws \ReflectionException
	 *
	 * @return bool
	 */
	public function update($id = null, $data = null): bool
	{
		$escape = null;

		if (\is_numeric($id) || \is_string($id)) {
			$id = [$id];
		}

		if (empty($data)) {
			$data = $this->tempData['data'] ?? null;
			$escape = $this->tempData['escape'] ?? null;
			$this->tempData = [];
		}

		if (empty($data)) {
			throw DataException::forEmptyDataset('update');
		}

		// If $data is using a custom class with public or protected
		// properties representing the table elements, we need to grab
		// them as an array.
		if (\is_object($data) && !$data instanceof \stdClass) {
			$data = static::classToArray($data, $this->primaryKey, $this->dateFormat);
		}

		// If it's still a stdClass, go ahead and convert to
		// an array so doProtectFields and other model methods
		// don't have to do special checks.
		if (\is_object($data)) {
			$data = (array) $data;
		}

		// If it's still empty here, means $data is no change or is empty object
		if (empty($data)) {
			throw DataException::forEmptyDataset('update');
		}

		// Validate data before saving.
		if (!$this->skipValidation && !$this->cleanRules(true)->validate($data)) {
			return false;
		}

		// Must be called first so we don't
		// strip out updated_at values.
		$data = $this->doProtectFields($data);

		if ($this->useTimestamps && $this->updatedField && !\array_key_exists($this->updatedField, $data)) {
			$data[$this->updatedField] = $this->setDate();
		}

		$eventData = [
			'id'   => $id,
			'data' => $data,
		];

		if ($this->tempAllowCallbacks) {
			$eventData = $this->trigger('beforeUpdate', $eventData);
		}

		$builder = $this->builder();

		if ($id) {
			if (\is_array($this->primaryKey) && array_all_keys_set($this->primaryKey, $id)) {
				foreach ($this->primaryKey as $k=>$column) {
					if (\array_key_exists($k, $id)) {
						\is_array($id[$k])
							? $builder->whereIn($this->prefixed($column), $id[$k])
							: $builder->where($this->prefixed($column), $id[$k]);
					}
				}
			} else {
				$builder = $builder->whereIn($this->prefixed($this->primaryKey), $id);
			}
		}

		// Must use the set() method to ensure objects get converted to arrays
		$result = $builder
			->set($eventData['data'], '', $escape)
			->update()
		;

		$eventData = [
			'id'     => $id,
			'data'   => $eventData['data'],
			'result' => $result,
		];

		if ($this->tempAllowCallbacks) {
			$this->trigger('afterUpdate', $eventData);
		}

		$this->tempAllowCallbacks = $this->allowCallbacks;

		return $result;
	}

	//--------------------------------------------------------------------

	/**
	 * Deletes a single record from $this->table where $id matches
	 * the table's primaryKey.
	 *
	 * @param null|array|int|string $id    The rows primary key(s)
	 * @param bool                  $purge allows overriding the soft deletes setting
	 *
	 * @throws DatabaseException
	 *
	 * @return BaseResult|bool
	 */
	public function delete($id = null, bool $purge = false)
	{
		if ($id && (\is_numeric($id) || \is_string($id))) {
			$id = [$id];
		}

		$builder = $this->builder();

		if ($id) {
			if (\is_array($this->primaryKey) && array_all_keys_set($this->primaryKey, $id)) {
				foreach ($this->primaryKey as $k=>$column) {
					if (\array_key_exists($k, $id)) {
						\is_array($id[$k])
							? $builder->whereIn($this->prefixed($column), $id[$k])
							: $builder->where($this->prefixed($column), $id[$k]);
					}
				}
			} else {
				$builder = $builder->whereIn($this->prefixed($this->primaryKey), $id);
			}
		}

		$eventData = [
			'id'    => $id,
			'purge' => $purge,
		];

		if ($this->tempAllowCallbacks) {
			$this->trigger('beforeDelete', $eventData);
		}

		if ($this->useSoftDeletes && !$purge) {
			if (empty($builder->getCompiledQBWhere())) {
				if (CI_DEBUG) {
					throw new DatabaseException(lang('Database.deleteAllNotAllowed'));
				}

				return false; // @codeCoverageIgnore
			}

			$set[$this->deletedField] = $this->setDate();

			if ($this->useTimestamps && $this->updatedField) {
				$set[$this->updatedField] = $this->setDate();
			}

			$result = $builder->update($set);
		} else {
			$result = $builder->delete();
		}

		$eventData = [
			'id'     => $id,
			'purge'  => $purge,
			'result' => $result,
			'data'   => null,
		];

		if ($this->tempAllowCallbacks) {
			$this->trigger('afterDelete', $eventData);
		}

		$this->tempAllowCallbacks = $this->allowCallbacks;

		return $result;
	}

	//--------------------------------------------------------------------
	//--------------------------------------------------------------------

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	//--------------------------------------------------------------------
	//--------------------------------------------------------------------

	/**
	 * Permanently deletes all rows that have been marked as deleted
	 * through soft deletes (deleted = 1)
	 * Changed to strict deleteField.
	 *
	 * @return bool|mixed
	 */
	public function purgeDeleted()
	{
		if (!$this->useSoftDeletes) {
			return true;
		}

		return $this->builder()->where($this->prefixed($this->deletedField), $this->deletedValue ?? ' IS NOT NULL')->delete();
	}

	//--------------------------------------------------------------------

	/**
	 * Works with the find* methods to return only the rows that
	 * have been deleted.
	 * Changed to strict deleteField.
	 *
	 * @return $this
	 */
	public function onlyDeleted(): self
	{
		$this->tempUseSoftDeletes = false;
		$this->builder()->where($this->prefixed($this->deletedField), $this->deletedValue ?? ' IS NOT NULL');

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Override countAllResults to account for soft deleted accounts.
	 * Changed to strict deleteField.
	 *
	 * @param bool $reset
	 * @param bool $test
	 *
	 * @return mixed
	 */
	public function countAllResults(bool $reset = true, bool $test = false)
	{
		$this->builder = $this->whereNotDeleted();

		// When $reset === false, the $tempUseSoftDeletes will be
		// dependant on $useSoftDeletes value because we don't
		// want to add the same "where" condition for the second time
		$this->tempUseSoftDeletes = (true === $reset)
			? $this->useSoftDeletes
			: (true === $this->useSoftDeletes
				? false
				: $this->useSoftDeletes);

		return $this->builder()->testMode($test)->countAllResults($reset);
	}

	//--------------------------------------------------------------------

	/**
	 * Prefix the field name(s) with a string or the table name.
	 *
	 * @param string/array $field  Field name or array of field names
	 * @param null|string  $prefix String or null for table name
	 *
	 * @return array|string
	 */
	protected function prefixed($field, string $prefix = null)
	{
		$prefix = \rtrim(($prefix ?? $this->table), '.') . '.';

		if (\is_array($field)) {
			\array_walk($field, function (&$value, $key) use ($prefix) {
				$value = $prefix . $value;
			});

			return $field;
		}

		return $prefix . $field;
	}
}
