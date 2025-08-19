<?php

declare(strict_types=1);

namespace Atoolo\Resource\Capabilities;

interface MetadataCapability
{
    /**
     * Returns the content for the HTML <title> tag.
     * Crucial for SEO and browser tab text.
     */
    public function getTitle(): string;

    /**
     * Returns the content for the <meta name="description"> tag.
     * Often used by search engines for the result snippet.
     */
    public function getDescription(): string;

    /**
     * Returns a list of keywords for the <meta name="keywords"> tag.
     * @return string[]
     */
    public function getKeywords(): array;

    /**
     * Returns the name of the resource's author.
     */
    public function getAuthor(): ?string;

    /**
     * Returns the initial publication date.
     */
    public function getPublishDate(): ?\DateTimeInterface;

    /**
     * Returns the date the resource was last modified.
     */
    public function getModifiedDate(): ?\DateTimeInterface;

    /**
     * Returns the canonical URL to prevent duplicate content issues.
     */
    public function getCanonicalUrl(): ?string;

    /**
     * Determines if search engine robots should index this page.
     */
    public function isIndexable(): bool;

    /**
     * Determines if search engine robots should follow links on this page.
     */
    public function isFollowable(): bool;

    /**
     * Returns the title for social media sharing (Open Graph, Twitter Cards).
     * Falls back to getTitle() if not specified.
     */
    public function getSocialShareTitle(): string;

    /**
     * Returns the description for social media sharing.
     * Falls back to getDescription() if not specified.
     */
    public function getSocialShareDescription(): string;

    /**
     * Returns the URL for the social media sharing image.
     */
    public function getSocialShareImageUrl(): ?string;
}
