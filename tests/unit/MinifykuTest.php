<?php

declare(strict_types=1);

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2023 Denny Septian Panggabean <xamidimura@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
        'js'  => '0561b3110b4682b4c0a67ea9741be28d',
        'css' => '621c512df406dc8d923a3fa756087d9d',
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
        $this->config->autoMinify = false;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

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
        $this->config->autoMinify = false;
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

    public function testLoadExceptionForMissingVersioningFile()
    {
        $this->expectException(MinifykuException::class);
        $this->expectExceptionMessage('There is no file with versioning. Run "php spark minifyku:minify" command first.');

        if (file_exists($this->config->dirVersion . '/versions.json')) {
            unlink($this->config->dirVersion . '/versions.json');
        }

        $this->minifyku = new Minifyku($this->config);
        $this->minifyku->load('all.min.css');
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

    public function testLoadJsWithDirMinJs()
    {
        if (file_exists($this->config->dirMinJs . '/all.min.js')) {
            unlink($this->config->dirMinJs . '/all.min.js');
        }

        $this->config->dirMinJs = SUPPORTPATH . 'public/js';
        $this->minifyku         = new Minifyku($this->config);

        $this->minifyku->deploy('js');
        $result = $this->minifyku->load('all.min.js');

        $this->assertStringContainsString('<script defer type="text/javascript"', $result);
        $this->assertStringContainsString('public/js/all.min.js?v=' . $this->ver['js'], $result);
    }

    public function testLoadCssWithDirMinCss()
    {
        if (file_exists($this->config->dirMinJs . '/all.min.js')) {
            unlink($this->config->dirMinJs . '/all.min.js');
        }

        $this->config->dirMinCss = SUPPORTPATH . 'public/css';
        $this->minifyku          = new Minifyku($this->config);

        $this->minifyku->deploy('css');
        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('public/css/all.min.css?v=' . $this->ver['css'], $result);
    }

    public function testLoadJsWithBaseJsUrl()
    {
        $this->config->baseJSURL = 'http://js.localhost/';

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('js');
        $result = $this->minifyku->load('all.min.js');

        $this->assertStringContainsString('<script defer type="text/javascript"', $result);
        $this->assertStringContainsString('http://js.localhost/all.min.js?v=' . $this->ver['js'], $result);
    }

    public function testLoadCssWithBaseCssUrl()
    {
        $this->config->baseCSSURL = 'http://css.localhost/';

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('css');
        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('http://css.localhost/all.min.css?v=' . $this->ver['css'], $result);
    }

    public function testLoadJsWithBaseJsUrlAndDirMinJs()
    {
        $this->config->baseJSURL = 'http://js.localhost/';
        $this->config->dirMinJs  = SUPPORTPATH . 'public/js';

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('js');
        $result = $this->minifyku->load('all.min.js');

        $this->assertStringContainsString('<script defer type="text/javascript"', $result);
        $this->assertStringContainsString('http://js.localhost/all.min.js?v=' . $this->ver['js'], $result);
    }

    public function testLoadCssWithBaseCssUrlAndDirMinCss()
    {
        $this->config->baseCSSURL = 'http://css.localhost/';
        $this->config->dirMinCss  = SUPPORTPATH . 'public/css';

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('css');
        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('http://css.localhost/all.min.css?v=' . $this->ver['css'], $result);
    }

    public function testLoadJs()
    {
        // Set automatically minify
        $this->config->autoMinify = true;

        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.js');

        $this->assertStringContainsString('<script defer type="text/javascript"', $result);
        $this->assertStringContainsString('assets/js/all.min.js?v=' . $this->ver['js'], $result);
    }

    public function testLoadCss()
    {
        // Set automatically minify
        $this->config->autoMinify = true;

        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('assets/css/all.min.css?v=' . $this->ver['css'], $result);
    }

    public function testLoadJsWithoutAutoMinify()
    {
        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('js');
        $result = $this->minifyku->load('all.min.js');

        $this->assertStringContainsString('<script defer type="text/javascript"', $result);
        $this->assertStringContainsString('assets/js/all.min.js?v=' . $this->ver['js'], $result);
    }

    public function testLoadCssWithoutAutoMinify()
    {
        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('css');
        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('assets/css/all.min.css?v=' . $this->ver['css'], $result);
    }
}
