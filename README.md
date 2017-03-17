<p align="center">
<a href="https://travis-ci.org/Casa-Parks/Extract-Translations"><img src="https://travis-ci.org/Casa-Parks/Extract-Translations.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/Casa-Parks/Extract-Translations"><img src="https://poser.pugx.org/Casa-Parks/Extract-Translations/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/Casa-Parks/Extract-Translations"><img src="https://poser.pugx.org/Casa-Parks/Extract-Translations/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/Casa-Parks/Extract-Translations"><img src="https://poser.pugx.org/Casa-Parks/Extract-Translations/license.svg" alt="License"></a>
</p>

## Introduction

Extract Translations is a simple provision of translation listing, designed for providing use to the front end (IE: in JavaScript).

## License

Extract Translations is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Installation

To get started with Extract Translations, use Composer to add the package to your project's dependencies:

    composer require casa-parks/extract-translations

### Configuration

After installing, register the `CasaParks\ExtractTranslations\ExtractTranslationsServiceProvider` in your `config/app.php` configuration file:

```php
'providers' => [
    // Other service providers...

    CasaParks\ExtractTranslations\ExtractTranslationsServiceProvider::class,
],
```

### Basic Usage

Create a simple view composer, like so:

```php
<?php

namespace App\Composers;

use CasaParks\ExtractTranslations\Builder as TranslationsExtractorBuilder;
use Illuminate\Contracts\View\View;

class TranslationsComposer
{
    /**
     * The translations extractor builder.
     *
     * @var \CasaParks\ExtractTranslations\Builder
     */
    protected $builder;

    /**
     * Whether the data is cached or not.
     *
     * @var bool
     */
    protected $cached;

    /**
     * The view data.
     *
     * @var array
     */
    protected $data;

    /**
     * Creates a new translations composer.
     *
     * @param \CasaParks\ExtractTranslations\Builder $builder
     */
    public function __construct(TranslationsExtractorBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Compose the view.
     *
     * @param \Illuminate\Contracts\View\View $view
     *
     * @return void
     */
    public function compose(View $view)
    {
        if (! $this->cached) {
            $this->cache();
        }

        $view->with($this->data);
    }

    /**
     * Cache the data.
     *
     * @return void
     */
    protected function cache()
    {
        $this->cached = true;

        $translations = $this->builder
            ->locales('en', 'de')
            ->groups('pagination', 'validation')
            ->service();

        $this->data = compact('translations');
    }
}
```

Add this view composer, into your app (or composer) service provider's `boot` method:

```php
/**
 * Register any composers for your application.
 *
 * @return void
 */
public function boot()
{
    // ...

    // assuming `layout` is your common layout template.
    $this->app['view']->composer('layout', 'App\Composers\TranslationsComposer');

    // ...
}
```

In your common `layout` template file:

```blade
<!-- ... -->
<head>
    <!-- ... -->

    <script>window.translations = {!! $translations->toJson() !!}</script>

    <!-- ... -->
</head>
<!-- ... -->
```

Then utilise as required in your JavaScript.
