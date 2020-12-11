<?php

/*
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

if (!\function_exists('extract_properties')) {
	/**
	 * Masks an array by the allowed keys.
	 *
	 * @param object $object     Object
	 * @param array  $properties Property names to retrieve
	 * @param bool   $allowNull  True for keeping NULL items in the result array
	 *
	 * @return array Associative array having the allowed properties
	 */
	function extract_properties(object $object, array $properties, bool $allowNull = true): array
	{
		$extract = [];
		foreach ($properties as $property) {
			$extract[$property] = $object->{$property} ?? null;
		}

		return $allowNull ? $extract : \array_filter($extract);
	}
}

//--------------------------------------------------------------------

if (!\function_exists('all_properties_exist')) {
	/**
	 * Checks if all the given keys exist in the array.
	 *
	 * @param array  $properties Property names to check
	 * @param object $search     An object with properties to check
	 *
	 * @return bool True if the object has all the properties, false otherwise
	 */
	function all_properties_exist(array $properties, object $search): bool
	{
		foreach ($properties as $property) {
			if (!isset($search->{$property}) && !\property_exists($search, $property)) {
				return false;
			}
		}

		return true;
	}
}

//--------------------------------------------------------------------

if (!\function_exists('any_property_exists')) {
	/**
	 * Checks if at least one the given keys exists in the array.
	 *
	 * @param array  $properties Property names to check
	 * @param object $search     An object with properties to check
	 *
	 * @return bool True if the array has at least one of the properties, false otherwise
	 */
	function any_property_exists(array $properties, object $search): bool
	{
		foreach ($properties as $property) {
			if (isset($search->{$property}) || \property_exists($search, $property)) {
				return true;
			}
		}

		return false;
	}
}
