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

### Advanced Usage

Personally, I wouldn't want all of this work to occur on every page load. What I would do is have a translation api (we know that if the translations are going to be used on the front end then the user definitely has JavaScript enabled anyway, right?).

I'd have a simple API controller, like so:

```php
namespace App\Http\Controllers\Api;

use CasaParks\ExtractTranslations\Builder;

class TranslationController extends Controller
{
    /**
     * Get the available translations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Builder $builder)
    {
        return $builder->locales('en', 'de')
            ->groups('pagination', 'validation')
            ->service();
    }
}
```

A simple API route (in `routes/api.php` or your equivalent):

```php
$router->get('/api/translations', [
    'as' => 'get::api.translations',
    'uses' => 'Api\TranslationController@list',
]);
```

Then in your front end (IE, with axios):

```js
window.axios.get('/api/translations')
    .then(translations => window.translations = translations);
```

### Bonus Usage

You can even do this on a per-language basis, as a hot-swap, something like:

```php
namespace App\Http\Controllers\Api;

use CasaParks\ExtractTranslations\Builder;

class TranslationController extends Controller
{
    /**
     * Get the available translations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($translation, Builder $builder)
    {
        $translations = $builder->locales($translation)
            ->groups('pagination', 'validation')
            ->service()
            ->toArray();

        return array_get($translations, $translation, []);
    }
}
```

```
$router->get('/api/translations/{translation}', [
    'as' => 'get::api.translation',
    'uses' => 'Api\TranslationController@get',
]);
```

```js
function translate(translation) {
    window.axios.get(`/api/translations/${translation}`)
        .then(translations => window.translations = translations);
}
```
