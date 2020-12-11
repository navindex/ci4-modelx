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

namespace App\Helpers;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\ForObjectHelperTest;

/**
 * @internal
 * @coversNothing
 */
class ObjectHelperTest extends CIUnitTestCase
{
	/**
	 * Test object.
	 *
	 * @var \stdClass
	 */
	protected $testObject;

	//--------------------------------------------------------------------

	protected function setUp(): void
	{
		parent::setUp();
		helper('object');

		$this->testObject = new ForObjectHelperTest();

		$this->publicProperties = [
			'publicString',
			'publicInt',
			'publicNull',
			'publicArray',
		];

		$this->protectedProperties = [
			'protectedString',
			'protectedInt',
			'protectedNull',
			'protectedArray',
			'protectedEmptyArray',
		];

		$this->allProperties = $this->publicProperties + $this->protectedProperties;
	}

	//--------------------------------------------------------------------

	/**
	 *  @dataProvider providerExtractProperties
	 *
	 * @param object $object
	 * @param array  $properties
	 * @param bool   $allowNull
	 * @param array  $expected
	 */
	public function testExtractProperties(object $object, array $properties, bool $allowNull, array $expected)
	{
		$this->assertSame($expected, extract_properties($object, $properties));
	}

	//--------------------------------------------------------------------

	/**
	 * @dataProvider providerAllPropertiesExist
	 *
	 * @param object $object
	 * @param array  $properties
	 * @param bool   $expected
	 */
	public function testAllPropertiesExist(object $object, array $properties, bool $expected)
	{
		$this->assertSame($expected, all_properties_exist($properties, $object));
	}

	//--------------------------------------------------------------------

	/**
	 * @dataProvider providerAnyPropertyExists
	 *
	 * @param object $object
	 * @param array  $properties
	 * @param bool   $expected
	 */
	public function testAnyPropertyExists(object $object, array $properties, bool $expected)
	{
		$this->assertSame($expected, any_property_exists($properties, $object));
	}

	//--------------------------------------------------------------------

	/**
	 * Data provider.
	 */
	public function providerExtractProperties()
	{
		return [
			[null, [],  true, []],
			[null, [], false, []],
			[null, [],  null, []],

			[$this->testObject, [],  true, []],
			[$this->testObject, [], false, []],
			[$this->testObject, [],  null, []],

			[$this->testObject, $this->publicProperties,  true, []],
			[$this->testObject, $this->publicProperties, false, []],
			[$this->testObject, $this->publicProperties,  null, []],

			[$this->testObject, $this->protectedProperties,  true, []],
			[$this->testObject, $this->protectedProperties, false, []],
			[$this->testObject, $this->protectedProperties,  null, []],

			[$this->testObject, $this->allProperties,  true, []],
			[$this->testObject, $this->allProperties, false, []],
			[$this->testObject, $this->allProperties,  null, []],
		];
	}

	//--------------------------------------------------------------------

	/**
	 * Data provider.
	 */
	public function providerAllPropertiesExist()
	{
		return [
			[null, [], true],
			[$this->testObject, [], true],
			[$this->testObject, $this->publicProperties, true],
			[$this->testObject, $this->protectedProperties, true],
			[$this->testObject, $this->allProperties, true],
		];
	}

	//--------------------------------------------------------------------

	/**
	 * Data provider.
	 */
	public function providerAnyPropertyExists()
	{
		return [
			[null, [], true],
			[$this->testObject, [], true],
			[$this->testObject, $this->publicProperties, true],
			[$this->testObject, $this->protectedProperties, true],
			[$this->testObject, $this->allProperties, true],
		];
	}
}
