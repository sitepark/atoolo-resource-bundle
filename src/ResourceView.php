<?php

declare(strict_types=1);

namespace Atoolo\Resource;

use Atoolo\Resource\Exception\MissingFeatureException;
use Atoolo\Resource\ResourceFeature\ResourceFeature;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * A view of a resource, composed of features provided by contributors.
 *
 * Features are created lazily via factories and cached once resolved.
 */
final class ResourceView implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var array<class-string<ResourceFeature>, ResourceFeature>
     */
    private array $resolved = [];

    /**
     * @param array<class-string<ResourceFeature>, callable():ResourceFeature> $factories
     */
    public function __construct(
        private array $factories,
    ) {}

    /**
     * Returns the feature of the given type, constructing it if necessary.
     * Throws an error if the feature is unavailable.
     *
     * @template T of ResourceFeature
     * @param class-string<T> $feature Fully-qualified class name of the feature.
     * @return T
     * @throws MissingFeatureException if the feature is not registered.
     * @throws \Throwable if feature construction fails.
     */
    public function get(string $feature): ResourceFeature
    {
        $resolved = $this->tryGet($feature);
        if ($resolved === null) {
            throw new MissingFeatureException($feature, $this);
        }
        return $resolved;
    }

    /**
     *  Returns the feature of the given type, constructing it if necessary. If unavailable, returns null.
     *
     * @template T of ResourceFeature
     * @param class-string<T> $feature
     * @return T|null
     */
    public function tryGet(string $feature): ?ResourceFeature
    {
        if (!isset($this->resolved[$feature]) && isset($this->factories[$feature])) {
            $this->resolved[$feature] = ($this->factories[$feature])();
        }
        /** @var ?T $resolved */
        $resolved = $this->resolved[$feature] ?? null;
        return $resolved;
    }

    /**
     * Checks if the view contains a factory for the given feature type.
     *
     * @template T of ResourceFeature
     * @param class-string<T> $feature
     */
    public function has(string $feature): bool
    {
        return isset($this->factories[$feature]);
    }

    /**
     * Returns true if the view has at least one of the given features.
     *
     * @template T of ResourceFeature
     * @param class-string<T>[] $features
     */
    public function hasAny(array $features): bool
    {
        foreach ($features as $feature) {
            if ($this->has($feature)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Instantiates and resolves all registered features eagerly.
     * Useful for early failure detection or warm-up.
     */
    public function preloadAll(): void
    {
        foreach ($this->factories as $feature => $callback) {
            if (isset($this->resolved[$feature])) {
                continue;
            }
            try {
                $this->resolved[$feature] = $callback();
            } catch (\Throwable $th) {
                $this->logger?->warning('failed to preload resource feature', [
                    'feature' => $feature,
                    'error' => $th,
                ]);
            }
        }
    }
}
