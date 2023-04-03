<?php

declare(strict_types=1);

namespace Tests\unit;

use CodeIgniter\Test\CIUnitTestCase;
use PHPDevsr\Minifyku\Config\Minifyku as MinifykuConfig;
use PHPDevsr\Minifyku\Exceptions\MinifykuException;
use PHPDevsr\Minifyku\Minifyku;

/**
 * @internal
 */
final class MinifykuTest extends CIUnitTestCase
{
    private MinifykuConfig $config;
    private Minifyku $minifyku;
    private array $ver = [
        'js'  => 'bc3d0dc779f1a0b521b69ed3a2b85de8',
        'css' => 'ec8d57dd8de143d7ef822a90fca20957',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new MinifykuConfig();

        $this->config->dirJS      = SUPPORTPATH . 'assets/js';
        $this->config->dirCSS     = SUPPORTPATH . 'assets/css';
        $this->config->dirMinJs   = SUPPORTPATH . 'assets/js';
        $this->config->dirMinCss  = SUPPORTPATH . 'assets/css';
        $this->config->dirVersion = SUPPORTPATH . 'assets';
        $this->config->js         = [
            'all.min.js' => [
                'bootstrap.js', 'jquery.js', 'main.js',
            ],
        ];
        $this->config->css = [
            'all.min.css' => [
                'bootstrap.css', 'font-awesome.css', 'main.css',
            ],
        ];

        if (file_exists($this->config->dirJS . '/new.js')) {
            unlink($this->config->dirJS . '/new.js');
        }

        if (file_exists($this->config->dirCSS . '/new.css')) {
            unlink($this->config->dirCSS . '/new.css');
        }

        /*
        if (file_exists($this->config->dirVersion . '/versions.js')) {
            unlink($this->config->dirVersion . '/versions.js');
        }
        */
    }

    public function testConfig()
    {
        $this->assertInstanceOf(MinifykuConfig::class, $this->config);

        $this->assertSame('<script defer type="text/javascript" src="%s"></script>', $this->config->tagJs);
        $this->assertSame('<link rel="stylesheet" href="%s">', $this->config->tagCss);

        $this->assertSame(['all.min.js' => ['bootstrap.js', 'jquery.js', 'main.js']], $this->config->js);
        $this->assertSame(['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css']], $this->config->css);
    }

    public function testDeployExceptionForIncorrectDeploymentMode()
    {
        $this->expectException(MinifykuException::class);
        $this->expectExceptionMessage('The "incorrect" is not correct deployment mode');

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('incorrect');
    }

    public function testLoadExceptionForWrongFileExtension()
    {
        $this->expectException(MinifykuException::class);
        $this->expectExceptionMessage('Wrong file extension: ".php".');

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->load('all.min.php');
    }

    public function testDeployJs()
    {
        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->deploy('js');

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirJS . DIRECTORY_SEPARATOR . array_key_first($this->config->js));
    }

    public function testDeployCss()
    {
        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->deploy('css');

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirCSS . DIRECTORY_SEPARATOR . array_key_first($this->config->css));
    }

    public function testDeployAll()
    {
        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->deploy('all');

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirJS . DIRECTORY_SEPARATOR . array_key_first($this->config->js));
        $this->assertFileExists($this->config->dirCSS . DIRECTORY_SEPARATOR . array_key_first($this->config->css));
    }

    public function testLoadJs()
    {
        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.js');

        $this->assertSame('<script defer type="text/javascript" src="https://example.com/' . SUPPORTPATH . 'assets/js/all.min.js?v=' . $this->ver['js'] . '"></script>' . PHP_EOL, $result);
    }

    public function testLoadCss()
    {
        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.css');

        $this->assertSame('<link rel="stylesheet" href="https://example.com/' . SUPPORTPATH . 'assets/css/all.min.css?v=' . $this->ver['css'] . '">' . PHP_EOL, $result);
    }

    public function testLoadJsWithDirMinJs()
    {
        $this->config->dirMinJs = SUPPORTPATH . 'public/js';
        $this->minifyku         = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.js');

        $this->assertSame('<script defer type="text/javascript" src="https://example.com/' . SUPPORTPATH . 'public/js/all.min.js?v=' . $this->ver['js'] . '"></script>' . PHP_EOL, $result);
    }

    public function testLoadCssWithDirMinCss()
    {
        $this->config->dirMinCss = SUPPORTPATH . 'public/css';
        $this->minifyku          = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.css');

        $this->assertSame('<link rel="stylesheet" href="https://example.com/' . SUPPORTPATH . 'public/css/all.min.css?v=' . $this->ver['css'] . '">' . PHP_EOL, $result);
    }

    public function testLoadJsWithBaseJsUrl()
    {
        $this->config->baseJSURL = 'http://js.localhost/';

        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.js');

        $this->assertSame('<script defer type="text/javascript" src="http://js.localhost/all.min.js?v=' . $this->ver['js'] . '"></script>' . PHP_EOL, $result);
    }

    public function testLoadCssWithBaseCssUrl()
    {
        $this->config->baseCSSURL = 'http://css.localhost/';

        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.css');

        $this->assertSame('<link rel="stylesheet" href="http://css.localhost/all.min.css?v=' . $this->ver['css'] . '">' . PHP_EOL, $result);
    }

    public function testLoadJsWithBaseJsUrlAndDirMinJs()
    {
        $this->config->baseJSURL = 'http://js.localhost/';
        $this->config->dirMinJs  = SUPPORTPATH . 'public/js';

        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.js');

        $this->assertSame('<script defer type="text/javascript" src="http://js.localhost/all.min.js?v=' . $this->ver['js'] . '"></script>' . PHP_EOL, $result);
    }

    public function testLoadCssWithBaseCssUrlAndDirMinCss()
    {
        $this->config->baseCSSURL = 'http://css.localhost/';
        $this->config->dirMinCss  = SUPPORTPATH . 'public/css';

        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.css');

        $this->assertSame('<link rel="stylesheet" href="http://css.localhost/all.min.css?v=' . $this->ver['css'] . '">' . PHP_EOL, $result);
    }

    public function testDeployJsWithDirMinJs()
    {
        if (file_exists($this->config->dirMinJs . '/all.min.js')) {
            unlink($this->config->dirMinJs . '/all.min.js');
        }

        $this->config->dirMinJs = SUPPORTPATH . 'public/js';
        $this->minifyku         = new Minifyku($this->config);

        $result = $this->minifyku->deploy('js');

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirMinJs . DIRECTORY_SEPARATOR . array_key_first($this->config->js));
    }

    public function testDeployCssWithDirMinCss()
    {
        if (file_exists($this->config->dirMinCss . '/all.min.css')) {
            unlink($this->config->dirMinCss . '/all.min.css');
        }

        $this->config->dirMinCss = SUPPORTPATH . 'public/css';
        $this->minifyku          = new Minifyku($this->config);

        $result = $this->minifyku->deploy('css');

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirMinCss . DIRECTORY_SEPARATOR . array_key_first($this->config->css));
    }

    public function testDeployAllWithDirMinJsAndCss()
    {
        if (file_exists($this->config->dirMinJs . '/all.min.js')) {
            unlink($this->config->dirMinJs . '/all.min.js');
        }

        if (file_exists($this->config->dirMinCss . '/all.min.css')) {
            unlink($this->config->dirMinCss . '/all.min.css');
        }

        $this->config->dirMinJs  = SUPPORTPATH . 'public/js';
        $this->config->dirMinCss = SUPPORTPATH . 'public/css';
        $this->minifyku          = new Minifyku($this->config);

        $result = $this->minifyku->deploy('all');

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirMinJs . DIRECTORY_SEPARATOR . array_key_first($this->config->js));
        $this->assertFileExists($this->config->dirMinCss . DIRECTORY_SEPARATOR . array_key_first($this->config->css));
    }
}
