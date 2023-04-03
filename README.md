[![PHPUnit](https://github.com/PHPDevsr/minifyku/workflows/PHPUnit/badge.svg)](https://github.com/PHPDevsr/minifyku/actions/workflows/test-phpunit.yml)
[![PHPStan](https://github.com/PHPDevsr/minifyku/actions/workflows/test-phpstan.yml/badge.svg)](https://github.com/PHPDevsr/minifyku/actions/workflows/test-phpstan.yml)
[![Coverage Status](https://coveralls.io/repos/github/PHPDevsr/minifyku/badge.svg?branch=dev)](https://coveralls.io/github/PHPDevsr/minifyku?branch=dev)
[![Downloads](https://poser.pugx.org/phpdevsr/minifyku/downloads)](https://packagist.org/packages/phpdevsr/minifyku)
[![GitHub release (latest by date)](https://img.shields.io/github/v/release/PHPDevsr/minifyku)](https://packagist.org/packages/phpdevsr/minifyku)
[![GitHub stars](https://img.shields.io/github/stars/PHPDevsr/minifyku)](https://packagist.org/packages/phpdevsr/minifyku)
[![GitHub license](https://img.shields.io/github/license/PHPDevsr/minifyku)](https://github.com/PHPDevsr/minifyku/blob/dev/LICENSE)

# What is Minifyku?

Minifyku is helper versioning and minification your assets with Codeigniter 4, Can be automatically use ```base_url()```.

# Installation

install with composer
```bash
$ composer require phpdevsr/minifyku
```

# Configuration

```bash
$ php spark minify:publish
```

This command will copy a config file to your app namespace. Then you can adjust it to your needs. By default, file will be present in ```app/Config/Minifyku.php```.

```php
public array $js = [
    'all.min.js' => [
        'bootstrap.js', 'jquery.js', 'main.js'
    ],
];

public array $css = [
    'all.min.css' => [
        'bootstrap.css', 'font-awesome.css', 'main.css'
    ],
];
```

This configuration will be minify and combine file ```bootstrap.js```,```jquery.js```,```main.js``` to ```all.min.js```. Or minify and combine file ```bootstrap.css```,```font-awesome.css```,```main.css``` to ```all.min.css```.

# Usage

Run command for minification your all assets:

```bash
$ php spark minify:all
```

This will prepare everything and will set up a versioning. Make sure to load a minifier helper in your controller, by calling:

```php
helper('minifyku');
```

Now to generate a proper tag with desired file to load, you have to make a simple call in your code:

```php
minifyku('all.min.js');
```

or

```php
minifyku('all.min.css');
```

Helper will be produce:

```html
<script defer type="text/javascript" src="http://example.com/assets/js/all.min.js?v=bc3d0dc779f1a0b521b69ed3a2b85de8"></script>
```

or

```html
<link rel="stylesheet" href="http://localhost/assets/css/all.min.css?v=ec8d57dd8de143d7ef822a90fca20957">
```

# License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

# Contrib

We does accept and encourage contributions from the community in any shape. It doesn't matter whether you can code, write documentation, or help find bugs, all contributions are welcome.

<a href="https://github.com/PHPDevsr/minifyku/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=PHPDevsr/minifyku" />
</a>

Made with [contrib.rocks](https://contrib.rocks).