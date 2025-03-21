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

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Smarty\Exception;

use const DIRECTORY_SEPARATOR;

/**
 * Class LaravelSmartyServiceProvider
 *
 * @author  yuuki.takezawa<yuuki.takezawa@comnect.jp.net>
 * @license http://opensource.org/licenses/MIT MIT
 */
class SmartyServiceProvider extends ServiceProvider
{
    /**
     * boot
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        // add Smarty Extension
        $extension = $this->app['config']->get('ytake-laravel-smarty.extension', 'tpl');
        $this->app['view']->addExtension($extension, 'smarty', function () {
            // @codeCoverageIgnoreStart
            $smarty = $this->app->make('smarty.view');

            return new Engines\SmartyEngine($smarty->getSmarty());
            // @codeCoverageIgnoreEnd
        });
    }

    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $configPath = __DIR__ . '/config/ytake-laravel-smarty.php';
        $this->mergeConfigFrom($configPath, 'ytake-laravel-smarty');
        $this->publishes([
                             $configPath => $this->resolveConfigurePath()
                                 . DIRECTORY_SEPARATOR
                                 . 'ytake-laravel-smarty.php',
                         ]);
        $this->app->singleton('smarty.view', fn ($app) => $this->createSmarty($app));
        $this->app->alias('smarty.view', SmartyFactory::class);
    }

    /**
     * @return string
     */
    protected function resolveConfigurePath(): string
    {
        return (isset($this->app['path.config']))
            ? (string)$this->app['path.config'] : $this->app->basePath() . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * @param Application $app
     * @return SmartyFactory
     * @throws \Smarty\Exception
     */
    public function createSmarty(Application $app): SmartyFactory
    {
        $smartyTemplate = new Smarty();
        $factory = new SmartyFactory(
            $app['view.engine.resolver'],
            $app['view.finder'],
            $app['events'],
            $smartyTemplate,
            $this->app['config']
        );
        // Pass the container to the factory so it can be used to resolve view composers.
        $factory->setContainer($app);
        $factory->share('app', $app);
        // resolve cache storage
        $factory->resolveSmartyCache();
        // smarty configure(use ytake-laravel-smarty.php)
        $factory->setSmartyConfigure();
        $smartyTemplate->setViewFactory($factory);
        return $factory;
    }
}
