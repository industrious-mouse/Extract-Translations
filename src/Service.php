<?php

/*
 * This file is part of Casa-Parks/Extract-Translations.
 *
 * (c) Connor S. Parks
 */

namespace CasaParks\ExtractTranslations;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Translation\LoaderInterface as TranslationLoader;
use Illuminate\Translation\Translator;
use JsonSerializable;

class Service implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * The available translations.
     *
     * @var array
     */
    protected $translations;

    /**
     * Constructs a new translation extracter service.
     *
     * @param array $translations
     */
    public function __construct(array $translations = [])
    {
        $this->translations = $translations;
    }

    /**
     * Constructs a new translation extracter service from a translator service.
     *
     * @param \Illuminate\Translation\Translator $translator
     * @param array                              $locales
     * @param array                              $groups
     *
     * @return \CasaParks\ExtractTranslations\Service
     */
    public static function from(Translator $translator, array $locales = [], array $groups = [])
    {
        if (empty($locales)) {
            $locales = [$translator->getLocale(), $translator->getFallback()];
        }

        $locales = array_unique($locales);
        $groups = array_unique($groups);

        if (empty($locales) || empty($groups)) {
            return new static();
        }

        $translations = static::extract($translator->getLoader(), $locales, $locales[0], $groups);

        return new static($translations);
    }

    /**
     * Extract translations from the loader.
     *
     * @param \Illuminate\Translation\LoaderInterface $loader
     * @param array                                   $locales
     * @param string                                  $fallback
     * @param array                                   $groups
     *
     * @return array
     */
    protected static function extract(TranslationLoader $loader, array $locales, $fallback, array $groups)
    {
        $translations = [];

        foreach ($locales as $locale) {
            foreach ($groups as $group) {
                $loaded = $loader->load($locale, $group);

                if (empty($loaded)) {
                    $translations[$locale][$group] = $fallback === $locale ? [] : $translations[$fallback][$group];

                    continue;
                }

                $translations[$locale][$group] = $loaded;
            }
        }

        return $translations;
    }

    /**
     * Convert the translations to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->translations;
    }

    /**
     * Convert the translations to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return '{}';
        }

        return $json;
    }

    /**
     * Convert the translations into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
