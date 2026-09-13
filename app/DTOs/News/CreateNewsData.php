<?php

namespace App\DTOs\News;

use App\Http\Requests\News\StoreNewsRequest;
use DateTime;
use Illuminate\Http\UploadedFile;

class CreateNewsData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $title,
        public readonly ?string $summary,
        public readonly string $content,
        public readonly ?UploadedFile $image,
        public readonly string $type,
        public readonly string $sourceType,
        public readonly ?string $external_Url,
        public readonly ?int $external_Id,
        public readonly ?DateTime $publishedAt,
        public readonly ?bool $status,
        public readonly ?bool $isBreaking,
        public readonly ?DateTime $breakingUntil
    )
    {
        //
    }

    public static function fromRequest(StoreNewsRequest $request): self
    {
        return new self(
            title: $request->input('title'),
            summary: $request->input('summary'),
            content: $request->input('content'),
            image: $request->file('image'),
            type: $request->input('type'),
            sourceType: $request->input('source_type'),
            external_Url: $request->input('external_url'),
            external_Id: $request->input('external_id'),
            publishedAt: $request->input('published_at') ? new DateTime($request->input('published_at')) : null,
            status: $request->input('status'),
            isBreaking: $request->input('is_breaking'),
            breakingUntil: $request->input('breaking_until') ? new DateTime($request->input('breaking_until')) : null
        );
    }
}
