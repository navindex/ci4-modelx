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

//--------------------------------------------------------------------

if (!\function_exists('is_assoc_array')) {
	/**
	 * Checks if the array is associative.
	 *
	 * @param mixed $var Variable to check
	 *
	 * @return bool True if associative, false otherwise
	 */
	function is_assoc_array($var): bool
	{
		return \is_array($var) && \array_keys($var) !== \range(0, \count($var) - 1);
	}
}

//--------------------------------------------------------------------

if (!\function_exists('is_multi_array')) {
	/**
	 * Checks if the array is multi dimensional.
	 *
	 * @param mixed $var Variable to check
	 * @param mixed $var
	 *
	 * @return bool True if associative, false otherwise
	 */
	function is_multi_array($var): bool
	{
		if (\is_array($var)) {
			foreach ($var as $item) {
				if (\is_array($item)) {
					return true;
				}
			}
		}

		return false;
	}
}

//--------------------------------------------------------------------

if (!\function_exists('is_nonempty_array')) {
	/**
	 * Checks if the variable is array and not empty.
	 *
	 * @param mixed $var Variable to check
	 *
	 * @return bool True if the variable is an array and not empty, false otherwise
	 */
	function is_nonempty_array($var): bool
	{
		return \is_array($var) && $var != [];
	}
}

//--------------------------------------------------------------------

if (!\function_exists('array_mask')) {
	/**
	 * Masks an array by the allowed keys.
	 *
	 * @param array $array       Associative array to mask
	 * @param array $allowedKeys Allowed keys
	 *
	 * @return array Associative array having the allowed keys only
	 */
	function array_mask(array $array, array $allowedKeys): array
	{
		return \array_intersect_key($array, \array_flip($allowedKeys));
	}
}

//--------------------------------------------------------------------

if (!\function_exists('array_isset')) {
	/**
	 * Checks if the given key or index exists in the array.
	 * A faster alternative of the \array_key_exists function.
	 *
	 * @param array|\ArrayObject $search An array with keys to check
	 * @param mixed              $key    Key to check
	 *
	 * @return bool True if the array has the key, false otherwise
	 */
	function array_isset($key, array $search): bool
	{
		return isset($search[$key]) || \array_key_exists($key, $search);
	}
}

//--------------------------------------------------------------------

if (!\function_exists('array_all_keys_exist')) {
	/**
	 * Checks if all the given keys exist in the array.
	 *
	 * @param array              $keys   Keys to check
	 * @param array|\ArrayObject $search An array with keys to check
	 *
	 * @return bool True if the array has all the keys, false otherwise
	 */
	function array_all_keys_exist(array $keys, array $search): bool
	{
		return empty(\array_diff_key(\array_flip($keys), $search));
	}
}

//--------------------------------------------------------------------

if (!\function_exists('array_any_key_exists')) {
	/**
	 * Checks if at least one the given keys exists in the array.
	 *
	 * @param array              $keys   Keys to check
	 * @param array|\ArrayObject $search An array with keys to check
	 *
	 * @return bool True if the array has at least one of the keys, false otherwise
	 */
	function array_any_key_exists(array $keys, array $search): bool
	{
		return \count(array_mask($search, $keys)) > 0;
	}

	//--------------------------------------------------------------------

	if (!\function_exists('array_all_keys_set')) {
		/**
		 * Checks if all the given keys exist in the array and their values are not null.
		 *
		 * @param array              $keys   Keys to check
		 * @param array|\ArrayObject $search An array with keys to check
		 *
		 * @return bool True if the array has all the keys and values, false otherwise
		 */
		function array_all_keys_set(array $keys, array $search): bool
		{
			$masked = \array_filter(array_mask($search, $keys));

			return \count($masked) === \count($keys);
		}
	}
}
