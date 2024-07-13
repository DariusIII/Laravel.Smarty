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

namespace Ytake\LaravelSmarty\Engines;

use Illuminate\View\View;
use Smarty\Template;
use Ytake\LaravelSmarty\Smarty;
use Ytake\LaravelSmarty\SmartyFactory;

use function str_replace;

/**
 * Class SmartyTemplate
 *
 * @author  yuuki.takezawa <yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class SmartyTemplate extends Template
{
    /** @var string|null  */
    private ?string $templateResourceName = null;

    /**
     * {@inheritdoc}
     * @throws \Smarty\Exception
     */
    public function _subTemplateRender(
        $template,
        $cache_id,
        $compile_id,
        $caching,
        $cache_lifetime,
        $data,
        $scope,
        $forceTplCache,
        $uid = null,
        $content_func = null
    ): void {
        $this->templateResourceName = $template;
        $this->renderSubTemplate(
            $template,
            $cache_id,
            $compile_id,
            $caching,
            $cache_lifetime,
            $data,
            $scope,
            $forceTplCache,
            $uid,
            $content_func
        );
    }

    /**
     * {@inheritdoc}
     * @throws \Smarty\Exception
     */
    public function _subTemplateRegister(): void
    {
        foreach ($this->getCompiled()->includes as $name => $count) {
            // @codeCoverageIgnoreStart
            if (isset($this->smarty->getCached()['subTplInfo'][$name])) {
                $this->smarty->getCached()['subTplInfo'][$name] += $count;
            } else {
                $this->smarty->getCached()['subTplInfo'][$name] = $count;
            }
            // @codeCoverageIgnoreEnd
        }
        if ($this->templateResourceName) {
            $parseResourceName = $this->smarty->addTemplateDir(
                $this->templateResourceName,
                $this->smarty->default_resource_type
            );
            $this->dispatch($this, $parseResourceName[0]);
        }
    }

    /**
     * @param Template $template
     * @param string $name
     */
    protected function dispatch(
        Template $template,
        string $name
    ): void {
        /** @var SmartyFactory $viewFactory */
        $viewFactory = $this->smarty->getViewFactory();
        $name = $this->normalizeName($name, $viewFactory);
        $view = new View(
            $viewFactory,
            $viewFactory->getEngineResolver()->resolve('smarty'),
            $name,
            $template->getSource()->getFilepath(),
            []
        );
        $viewFactory->callCreator($view);
        $viewFactory->callComposer($view);
        foreach ($view->getData() as $key => $data) {
            $this->assign($key, $data);
        }
        unset($template);
    }

    /**
     * @param string $name
     * @param SmartyFactory $viewFactory
     *
     * @return string|string[]
     */
    protected function normalizeName(
        string $name,
        SmartyFactory $viewFactory
    ): array|string {
	    return str_replace(['.'.$viewFactory->getSmartyFileExtension(), '/'], ['', '.'], $name);
    }
}
