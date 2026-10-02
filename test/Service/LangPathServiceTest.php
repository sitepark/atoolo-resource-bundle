<?php

declare(strict_types=1);

namespace Atoolo\Resource\Test\Service;

use Atoolo\Resource\DataBag;
use Atoolo\Resource\LangPath;
use Atoolo\Resource\ResourceChannel;
use Atoolo\Resource\ResourceLanguage;
use Atoolo\Resource\ResourceTenant;
use Atoolo\Resource\Service\LangPathService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LangPathService::class)]
class LangPathServiceTest extends TestCase
{
    private LangPathService $service;

    protected function setUp(): void
    {
        $channel = $this->createResourceChannel(['de_DE', 'en_US']);
        $this->service = new LangPathService($channel);
    }

    public function testLangToLocaleReturnsEmptyStringForDefaultLang(): void
    {
        $this->assertEquals(
            '',
            $this->service->langToLocale(ResourceLanguage::default()),
            'Default language should map to an empty locale string',
        );
    }

    public function testLangToLocaleReturnsLocaleForKnownLang(): void
    {
        $this->assertEquals(
            'de_DE',
            $this->service->langToLocale(ResourceLanguage::of('de')),
            'Known language code "de" should resolve to locale "de_DE"',
        );
    }

    public function testLangToLocaleReturnsEmptyStringForUnknownLang(): void
    {
        $this->assertEquals(
            '',
            $this->service->langToLocale(ResourceLanguage::of('fr')),
            'Unknown language code not in translation locales should return empty string',
        );
    }

    public function testParseReturnsLangPathForLanguagePrefix(): void
    {
        $this->assertEquals(
            new LangPath('de', 'de_DE', '/some/path'),
            $this->service->parse('/de/some/path'),
            'Path starting with a supported language prefix should extract lang and remaining path',
        );
    }

    public function testParseReturnsLangPathForLanguagePrefixOnly(): void
    {
        $this->assertEquals(
            new LangPath('de', 'de_DE', '/'),
            $this->service->parse('/de'),
            'Path containing only a language prefix should return "/" as the remaining path',
        );
    }

    public function testParseReturnsPathOnlyForUnknownLanguagePrefix(): void
    {
        $this->assertEquals(
            new LangPath(null, null, '/fr/some/path'),
            $this->service->parse('/fr/some/path'),
            'Path starting with an unsupported language prefix should be returned as-is without lang',
        );
    }

    public function testParseReturnsLangPathForTranslationPath(): void
    {
        $this->assertEquals(
            new LangPath('de', 'de_DE', '/some/file.php'),
            $this->service->parse('/some/file.translations/de_DE.php'),
            'Translation file path with supported lang should resolve to base path with lang info',
        );
    }

    public function testParseReturnsPathOnlyForTranslationPathWithUnknownLang(): void
    {
        $this->assertEquals(
            new LangPath(null, null, '/some/file.translations/fr_FR.php'),
            $this->service->parse('/some/file.translations/fr_FR.php'),
            'Translation file path with unsupported lang should be returned as-is without lang',
        );
    }

    public function testParseReturnsPathOnlyForTranslationPathWithInvalidPattern(): void
    {
        $this->assertEquals(
            new LangPath(null, null, '/some/file.translations/invalid.php'),
            $this->service->parse('/some/file.translations/invalid.php'),
            'Translation path not matching lang_COUNTRY.php pattern should be returned as-is',
        );
    }

    public function testParseReturnsPathOnlyForRegularPath(): void
    {
        $this->assertEquals(
            new LangPath(null, null, '/some/regular/path.php'),
            $this->service->parse('/some/regular/path.php'),
            'Regular path without any language pattern should be returned as-is without lang',
        );
    }

    public function testParseReturnsPathOnlyForEmptyPath(): void
    {
        $this->assertEquals(
            new LangPath(null, null, ''),
            $this->service->parse(''),
            'Empty path should be returned as-is without lang',
        );
    }

    /**
     * @param string[] $translationLocales
     */
    private function createResourceChannel(array $translationLocales): ResourceChannel
    {
        return ResourceChannel::create([
            'translationLocales' => $translationLocales,
        ]);
    }
}
