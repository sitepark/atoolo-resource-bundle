<?php

declare(strict_types=1);

namespace Atoolo\Resource\Service;

use Atoolo\Resource\LangPath;
use Atoolo\Resource\ResourceChannel;
use Atoolo\Resource\ResourceLanguage;
use Locale;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsAlias(id: 'atoolo_resource.lang_path_service')]
class LangPathService
{
    /**
     * Created on first use, so that the resource channel is not loaded while
     * the container is built or warmed up.
     *
     * @var ?array<string, string> $langLocaleMap
     */
    private ?array $langLocaleMap = null;

    public function __construct(
        #[Autowire(service: 'atoolo_resource.resource_channel')]
        private readonly ResourceChannel $resourceChannel,
    ) {}

    public function langToLocale(ResourceLanguage $lang): string
    {
        if ($lang === ResourceLanguage::default()) {
            return '';
        }

        return $this->getLangLocaleMap()[$lang->code] ?? '';
    }

    public function parse(string $path): LangPath
    {
        // Pattern 1: /lang/... oder /lang/ oder /lang
        $langPath = $this->parseLanguagePath($path);
        if ($langPath !== null) {
            return $langPath;
        }

        // Pattern 2: ...translations/languageCode_countryCode.php
        $langPath = $this->parseTranslationPath($path);
        return $langPath ?? new LangPath(null, null, $path);
    }

    private function parseLanguagePath(string $path): ?LangPath
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));

        if (empty($segments)) {
            return null;
        }

        $potentialLang = $segments[0];

        $langLocaleMap = $this->getLangLocaleMap();
        if (!isset($langLocaleMap[$potentialLang])) {
            return null;
        }

        $remainingPath = implode('/', array_slice($segments, 1));

        return new LangPath(
            lang: $potentialLang,
            locale: $langLocaleMap[$potentialLang],
            path: '/' . $remainingPath,
        );
    }

    private function parseTranslationPath(string $path): ?LangPath
    {
        if (!str_contains($path, '.translations/')) {
            return null;
        }

        if (!preg_match('#(.*?)\.translations/(\w+)_[A-Z]{2}\.php$#', $path, $matches)) {
            return null;
        }

        $potentialLang = $matches[2];

        $langLocaleMap = $this->getLangLocaleMap();
        if (!isset($langLocaleMap[$potentialLang])) {
            return null;
        }

        return new LangPath(
            lang: $potentialLang,
            locale: $langLocaleMap[$potentialLang],
            path: $matches[1] . '.php',
        );
    }

    /**
     * @return array<string, string>
     */
    private function getLangLocaleMap(): array
    {
        return $this->langLocaleMap ??= $this->createLangLocaleMap();
    }

    /**
     * @return array<string, string>
     */
    private function createLangLocaleMap(): array
    {
        $map = [];
        foreach (
            $this->resourceChannel->translationLocales as $availableLocale
        ) {
            $primaryLang = Locale::getPrimaryLanguage($availableLocale);
            $map[$primaryLang] = $availableLocale;
        }
        return $map;
    }

}
