# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

`atoolo/resource-bundle` is a **PHP Symfony bundle** that handles pre-produced aggregated resource data from the Sitepark/IES content management system. Resources are PHP files on the filesystem that represent content objects (pages, articles, etc.) with hierarchical relationships.

## Common Commands

```bash
# Install dependencies
composer install

# Run all tests with coverage
composer test
# or directly:
./tools/phpunit.phar -c phpunit.xml --coverage-text

# Run a single test file
./tools/phpunit.phar -c phpunit.xml test/Loader/SiteKitLoaderTest.php

# Run a single test method
./tools/phpunit.phar -c phpunit.xml --filter testMethodName test/Loader/SiteKitLoaderTest.php

# Run all static analysis (lint, phpstan, cs-fixer, compatibility check)
composer analyse

# Fix code style
composer cs-fix

# Run mutation testing
composer test:infection
```

## Architecture

### Core Concept

Resources are PHP files on the filesystem. `SiteKitLoader` evaluates these PHP files and wraps their data in a `Resource` object. Resources have:
- A **location** (filesystem path relative to resource root)
- An **ID** (numeric, maps to path via `IdPathMapper`)
- A **language** (`ResourceLanguage`)
- A **DataBag** for nested data access (dot-notation: `'base.trees.navigation.parents'`)

### Key Abstractions

**`ResourceLoader` (interface)** — Contract for loading resources by location. `SiteKitLoader` is the primary implementation; `CachedResourceLoader` wraps it as a decorator for in-memory caching.

**`ResourceHierarchyLoader` (interface, extends `ResourceLoader`)** — Adds parent/child navigation. `SiteKitResourceHierarchyLoader` reads hierarchy from `base.trees.{treeName}.parents/.children` in resource data. `SiteKitNavigationHierarchyLoader` specializes this for the `navigation` tree.

**`ResourceHierarchyWalker`** — Stateful tree traversal using an internal stack. Call `init()` to set the starting point, then navigate with `down()`, `up()`, `next()`, `nextSibling()`, etc. `ResourceHierarchyFinder` builds on this for predicate-based search.

**`IdPathMapper` (interface)** — Strategy for mapping between numeric IDs and filesystem paths. `FixedDecimalGroupingIdPathMapper` groups IDs into a fixed-depth directory hierarchy (e.g., ID `1000` → `000/001/000`). `NoopIdPathMapper` is the null object used when ID mapping is disabled. `IdPathMapperFactory` creates the right implementation based on `resourceChannel.attributes.resourcePathType`.

**`ResourceChannel`** — Configuration entity loaded lazily via `SiteKitResourceChannelFactory` which reads `context.php` from the resource root. Contains channel metadata, locale, `ResourceTenant`, and a `DataBag` of attributes.

**`LangPathService`** — Parses language from two path patterns:
1. `/lang/path/` (language prefix)
2. `/path.translations/lang_COUNTRY.php` (translation file)

### Service Registration

Services in `config/services.yaml` use Symfony autowiring. Key lazy services created via factories:
- `atoolo_resource.resource_channel` — created by `ResourceChannelFactory::create()`
- `atoolo_resource.id_path_mapper` — created by `IdPathMapperFactory::create()`

### Test Structure

Tests mirror `src/` under `test/`. Test resources (fixture PHP files) live under `test/resources/`. The `TestResourceFactory` helper creates `Resource` objects in tests. Test namespace: `Atoolo\Resource\Test\`.

### Code Style

- PHPStan level 9 (strict analysis)
- PHP-CS-Fixer for formatting (config: `.php-cs-fixer.dist.php`)
- PHP_CodeSniffer for compatibility checks
