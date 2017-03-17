<?php

/*
 * This file is part of Casa-Parks/Extract-Translations.
 *
 * (c) Connor S. Parks
 */

namespace CasaParks\ExtractTranslations;

use Illuminate\Translation\Translator;

class Builder
{
    /**
     * The illuminate translator service.
     *
     * @var \Illuminate\Translation\Translator
     */
    protected $translator = null;

    /**
     * The allowed locales.
     *
     * @var array|null
     */
    protected $locales = null;

    /**
     * The allowed groups.
     *
     * @var array
     */
    protected $groups = [];

    /**
     * Create a new builder from a translator.
     *
     * @param \Illuminate\Translation\Translator $translator
     *
     * @return \CasaParks\ExtractTranslations\Builder
     */
    public static function from(Translator $translator)
    {
        $builder = new static();

        $builder->translator = $translator;

        return $builder;
    }

    /**
     * Limit to specific locales.
     *
     * @param ...string $locales
     *
     * @return $this
     */
    public function locales(...$locales)
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * Limit to specific message groups.
     *
     * @param ...string $groups
     *
     * @return $this
     */
    public function groups(...$groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Create the service.
     *
     * @return \CasaParks\ExtractTranslations\Service
     */
    public function service()
    {
        return Service::from($this->translator, $this->locales, $this->groups);
    }
}
