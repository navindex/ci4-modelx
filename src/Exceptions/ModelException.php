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
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Navindex Pty Ltd
 * @copyright Copyright (c) 2020 Navindex Pty Ltd
 */

namespace Navindex\ModelX\Exceptions;

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
