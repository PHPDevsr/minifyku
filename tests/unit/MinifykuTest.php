<?php

declare(strict_types=1);

/**
 * This file is part of PHPDevsr/Minifyku.
 *
 * (c) 2025 Denny Septian Panggabean <xamidimura@gmail.com>
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
        'css' => '08bc0baf72ade3c5a02a75519a864ec2',
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

    public function testConfig(): void
    {
        $this->assertInstanceOf(MinifykuConfig::class, $this->config);

        $this->assertSame('<script defer type="text/javascript" src="%s"></script>', $this->config->tagJs);
        $this->assertSame('<link rel="stylesheet" href="%s">', $this->config->tagCss);

        $this->assertSame(['all.min.js' => ['bootstrap.js', 'jquery.js', 'main.js']], $this->config->js);
        $this->assertSame(['all.min.css' => ['bootstrap.css', 'font-awesome.css', 'main.css']], $this->config->css);
    }

    public function testDeployExceptionForIncorrectDeploymentMode(): void
    {
        $this->expectException(MinifykuException::class);
        $this->expectExceptionMessage('The "incorrect" is not correct deployment mode');

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('incorrect');
    }

    public function testLoadExceptionForWrongFileExtension(): void
    {
        $this->expectException(MinifykuException::class);
        $this->expectExceptionMessage('Wrong file extension: ".php".');

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->load('all.min.php');
    }

    public function testLoadExceptionForMissingVersioningFile(): void
    {
        $this->expectException(MinifykuException::class);
        $this->expectExceptionMessage('There is no file with versioning. Run "php spark minifyku:minify" command first.');

        if (file_exists($this->config->dirVersion . '/versions.json')) {
            unlink($this->config->dirVersion . '/versions.json');
        }

        $this->minifyku = new Minifyku($this->config);
        $this->minifyku->load('all.min.css');
    }

    public function testDeployJs(): void
    {
        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->deploy('js');

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirJS . DIRECTORY_SEPARATOR . array_key_first($this->config->js));
    }

    public function testDeployCss(): void
    {
        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->deploy('css');

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirCSS . DIRECTORY_SEPARATOR . array_key_first($this->config->css));
    }

    public function testDeployAll(): void
    {
        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->deploy('all');

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirJS . DIRECTORY_SEPARATOR . array_key_first($this->config->js));
        $this->assertFileExists($this->config->dirCSS . DIRECTORY_SEPARATOR . array_key_first($this->config->css));
    }

    public function testDeployAllWithGzip(): void
    {
        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->deploy('all', 6);

        $this->assertTrue($result);

        $this->assertFileExists($this->config->dirJS . DIRECTORY_SEPARATOR . array_key_first($this->config->js));
        $this->assertFileExists($this->config->dirCSS . DIRECTORY_SEPARATOR . array_key_first($this->config->css));
    }

    public function testDeployJsWithDirMinJs(): void
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

    public function testDeployCssWithDirMinCss(): void
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

    public function testDeployAllWithDirMinJsAndCss(): void
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

    public function testLoadJsWithDirMinJs(): void
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

    public function testLoadCssWithDirMinCss(): void
    {
        if (file_exists($this->config->dirMinCss . '/all.min.css')) {
            unlink($this->config->dirMinCss . '/all.min.css');
        }

        $this->config->dirMinCss = SUPPORTPATH . 'public/css';
        $this->minifyku          = new Minifyku($this->config);

        $this->minifyku->deploy('css');
        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('public/css/all.min.css?v=' . $this->ver['css'], $result);
    }

    public function testLoadJsWithBaseJsUrl(): void
    {
        $this->config->baseJSURL = 'http://js.localhost/';

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('js');
        $result = $this->minifyku->load('all.min.js');

        $this->assertStringContainsString('<script defer type="text/javascript"', $result);
        $this->assertStringContainsString('http://js.localhost/all.min.js?v=' . $this->ver['js'], $result);
    }

    public function testLoadCssWithBaseCssUrl(): void
    {
        $this->config->baseCSSURL = 'http://css.localhost/';

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('css');
        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('http://css.localhost/all.min.css?v=' . $this->ver['css'], $result);
    }

    public function testLoadJsWithBaseJsUrlAndDirMinJs(): void
    {
        $this->config->baseJSURL = 'http://js.localhost/';
        $this->config->dirMinJs  = SUPPORTPATH . 'public/js';

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('js');
        $result = $this->minifyku->load('all.min.js');

        $this->assertStringContainsString('<script defer type="text/javascript"', $result);
        $this->assertStringContainsString('http://js.localhost/all.min.js?v=' . $this->ver['js'], $result);
    }

    public function testLoadCssWithBaseCssUrlAndDirMinCss(): void
    {
        $this->config->baseCSSURL = 'http://css.localhost/';
        $this->config->dirMinCss  = SUPPORTPATH . 'public/css';

        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('css');
        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('http://css.localhost/all.min.css?v=' . $this->ver['css'], $result);
    }

    public function testLoadJs(): void
    {
        // Set automatically minify
        $this->config->autoMinify = true;

        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.js');

        $this->assertStringContainsString('<script defer type="text/javascript"', $result);
        $this->assertStringContainsString('assets/js/all.min.js?v=' . $this->ver['js'], $result);
    }

    public function testLoadCss(): void
    {
        // Set automatically minify
        $this->config->autoMinify = true;

        $this->minifyku = new Minifyku($this->config);

        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('assets/css/all.min.css?v=' . $this->ver['css'], $result);
    }

    public function testLoadJsWithoutAutoMinify(): void
    {
        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('js');
        $result = $this->minifyku->load('all.min.js');

        $this->assertStringContainsString('<script defer type="text/javascript"', $result);
        $this->assertStringContainsString('assets/js/all.min.js?v=' . $this->ver['js'], $result);
    }

    public function testLoadCssWithoutAutoMinify(): void
    {
        $this->minifyku = new Minifyku($this->config);

        $this->minifyku->deploy('css');
        $result = $this->minifyku->load('all.min.css');

        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('assets/css/all.min.css?v=' . $this->ver['css'], $result);
    }
}
