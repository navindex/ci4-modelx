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

namespace Tests\Support;

/**
 * Test object for ObjectHelperTest.
 *
 * @internal
 * @coversNothing
 */
class ForObjectHelperTest
{
	public $publicString = 'hello';

	public $publicInt = 41;

	public $publicNull;

	public $publicArray = ['first', 2, null, 'fourth' => 4];

	public $publicEmptyArray = [];

	//--------------------------------------------------------------------

	protected $protectedString = 'hello';

	protected $protectedInt = 41;

	protected $protectedNull;

	protected $protectedArray = ['first', 2, null, 'fourth' => 4];

	protected $protectedEmptyArray = [];
}
