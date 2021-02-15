# Kirby Auto Srcset
A plugin for `Kirby 3` to generate an image srcset automatically.
Based on `Processwire CMS` user `DaveP`'s forum [post](https://processwire.com/talk/topic/12036-responsive-image-breakpoints-with-field-templates/).

## Installation
### Manual download
Download and copy this repository to `site/plugins/kirby-auto-srcset`.

### Git submodiule
```
git submodule add https://github.com/arnoson/kirby-auto-srcset.git site/plugins/kirby-auto-srcset
```

### Composer
```
composer require arnoson/kirby-auto-srcset
```

## Usage
Instead of specifying the dimensions of a srcset manually
```php
$image->srcset([300, 400, 600, 1024]);
```
`kirby-auto-srcset` will calculate the sizes for you.
```php
// Instead of specifying the dimensions of a srcset manually like this:
// $image->srcset([300, 400, 600, 1024])
// `kirby-auto-srcset` will calculate the sizes for you.
$image->autoSrcset([
  'minWidth' => 300,
  'maxWidth' => 1024
]);
```
This will create the minimum and maximum dimensions as well as the dimensions in 
between. Depending on your image it will result in different in between dimensions.
`kirby-auto-srcset` will figure out the dimensions based on the image's file size 
and tries to create the resulting images in roughly 20kb file sizes steps.
20kb seems to be a good value for responsive images but you can also adjust it 
in the plugin's config.

## Configuration
All options can either be set in the configuration or passed to the `autoSrcset` 
method.
```php
// config.js
return [
  'arnoson/kirby-auto-srcset' => [
    'minWidth' => 300,
    'maxWidth' => 1000,
    'quality' => 80,
    'fileSizeStep' => 20, // in kb
    'maxSteps' => 10, // The maximum number of images to be created.
  ]
];
```
