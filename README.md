# Pollen Asset Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/asset/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)
[![PHP Supported Versions](https://img.shields.io/badge/PHP->=7.4-8892BF?style=for-the-badge&logo=php)](https://www.php.net/supported-versions.php)

Pollen **Asset** Component provides tools to manage assets in web applications.

Allows adding of inline Css styles, adding of inline JS scripts or passing PHP vars to Js global vars  to access them in your own scripts.

## Installation

```bash
composer require pollen-solutions/asset
```

## Basic Usage

```php
use Pollen\Asset\AssetManager;

$asset = new AssetManager();

// Add inline CSS
$asset->addInlineCss(
    'body {
        background-color:AliceBlue;
    }'
);

// Add inline JS
$asset->addInlineJs(
    'console.log("value1");
     console.log("value2");
     console.log("value3");'
);

// Add global JS var
// -- app namespaced
$asset->addGlobalJsVar('test1', 'test-value1');

// -- in footer
$asset->addGlobalJsVar('test2', 'test-value2', true);

// -- in footer and without namespace
$asset->addGlobalJsVar('test3', 'test-value3', true, null);

```

## Register asset

@todo

## Enqueue assets

@todo

## Cache and Minification

Coming soon
