<?php

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 *
 * Copyright (c) 2014-2022 Yuuki Takezawa
 *
 */

declare(strict_types=1);

namespace Ytake\LaravelSmarty;

use Illuminate\Contracts\View\Factory;

/**
 * Class Smarty
 *
 * @author  yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Smarty extends \Smarty\Smarty
{
    /** @var Factory */
    private Factory $viewFactory;

    /**
     * @param Factory $factory
     */
    public function setViewFactory(Factory $factory): void
    {
        $this->viewFactory = $factory;
    }

    /**
     * @return Factory
     */
    public function getViewFactory(): Factory
    {
        return $this->viewFactory;
    }
}
