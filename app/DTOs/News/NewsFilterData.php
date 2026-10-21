<?php

namespace App\DTOs\News;

use App\Http\Requests\News\NewsFilterRequest;
use DateTimeImmutable;
use DateTimeInterface;

class NewsFilterData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $type = null,
        public readonly ?string $sourceType = null,
        public readonly ?bool $status = null,
        public readonly ?bool $isBreaking = null,
        public readonly ?int $createdBy = null,
        public readonly ?DateTimeInterface $publishedFrom = null,
        public readonly ?DateTimeInterface $publishedTo = null,
        public readonly ?string $sortBy = null,
        public readonly ?string $sortOrder = null,
        public readonly ?int $perPage = 10,
        public readonly ?int $page = 1

    ) {}

    public static function fromRequest(NewsFilterRequest $request): self
    {
        return new self(
            search: $request->input('search'),
            type: $request->input('type'),
            sourceType: $request->input('source_type'),
            status: $request->has('status') ? $request->boolean('status') : null,
            isBreaking: $request->has('is_breaking') ? $request->boolean('is_breaking') : null,
            createdBy: $request->filled('created_by') ? $request->integer('created_by') : null,
            publishedFrom: $request->filled('published_from')
                ? new DateTimeImmutable($request->input('published_from'))
                : null,
            publishedTo: $request->filled('published_to')
                ? new DateTimeImmutable($request->input('published_to'))
                : null,
            sortBy: $request->input('sort_by', 'published_at'),
            sortOrder: $request->input('sort_order', 'desc'),
            perPage: $request->integer('per_page', 10),
            page: $request->integer('page', 1)
        );
    }
}
