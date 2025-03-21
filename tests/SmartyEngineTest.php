<?php

namespace Tests;

use Smarty\Exception;

class SmartyEngineTest extends SmartyTestCase
{
    /** @var \Ytake\LaravelSmarty\Engines\SmartyEngine */
    protected $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new \Ytake\LaravelSmarty\Engines\SmartyEngine(
            $this->factory->getSmarty()
        );
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf('Ytake\\LaravelSmarty\\Engines\\SmartyEngine', $this->engine);
    }

    public function testShouldReturnSameValue(): void
    {
        $this->assertSame('hello', $this->engine->get('test.tpl'));
        $this->assertSame('helloSmarty', $this->engine->get('test.tpl', ['value' => 'Smarty']));
    }

    public function testException(): void
    {
        $this->expectException(Exception::class);
        $this->engine->get('testing.tpl');
    }
}