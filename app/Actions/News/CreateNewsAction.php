<?php

namespace App\Actions\News;

use App\DTOs\News\CreateNewsData;
use App\Models\News;
use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Traits\HandlesFileUpload;

class CreateNewsAction
{
    use HandlesFileUpload;
    /**
     * Create a new class instance.
     */
    public function __construct(
        private NewsRepositoryInterface $news
    ) {
        //
    }

    public function execute(CreateNewsData $data, int $userId): News
    {
        if ($data->image) {
            $image = $this->uploadFile($data->image, 'news');
        }
        if ($data->sourceType === 'external' && $data->external_Url && $data->external_Id) {
            $external_id = $data->external_Id;
            $external_url = $data->external_Url;
        } else {
            $external_id = null;
            $external_url = null;
        }

        $news = $this->news->create([
            'title' => $data->title,
            'summary' => $data->summary,
            'content' => $data->content,
            'image' => $image ?? null,
            'type' => $data->type,
            'source_type' => $data->sourceType,
            'external_url' => $external_url,
            'external_id' => $external_id,
            'published_at' => $data->publishedAt,
            'status' => $data->status,
            'is_breaking' => $data->isBreaking,
            'breaking_until' => $data->breakingUntil,
            'created_by' => $userId
        ]);

        return $news;
    }
}
