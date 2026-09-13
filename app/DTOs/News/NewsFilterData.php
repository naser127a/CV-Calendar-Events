<?php

namespace App\DTOs\News;

use App\Http\Requests\News\NewsFilterRequest;
use DateTime;

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
        public readonly ?DateTime $publishedFrom = null,
        public readonly ?DateTime $publishedTo = null,
        public readonly ?string $sortBy = null,
        public readonly ?string $sortOrder = null,
        public readonly ?int $perPage = 10,
        public readonly ?int $page = 1

    ) {}

    public static function fromRequest(NewsFilterRequest $request): self
    {
        return new self(
            search: $request['search'] ?? null,
            type: $request['type'] ?? null,
            sourceType: $request['source_type'] ?? null,
            status: $request['status'] ?? null,
            isBreaking: $request['is_breaking'] ?? null,
            createdBy: $request['created_by'] ?? null,
            publishedFrom: $request['published_from'] ?? null,
            publishedTo: $request['published_to'] ?? null,
            sortBy: $request['sort_by'] ?? 'published_at',
            sortOrder: $request['sort_order'] ?? 'desc',
            perPage: $request['per_page'] ?? 10,
            page: $request['page'] ?? 1
        );
    }
}
