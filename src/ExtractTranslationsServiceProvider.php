<?php

/*
 * This file is part of Casa-Parks/Extract-Translations.
 *
 * (c) Connor S. Parks
 */

namespace CasaParks\ExtractTranslations;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class ExtractTranslationsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Builder::class, function (Container $container) {
            $translator = $container->make('translator');

            return Builder::from($translator);
        });
    }
}
