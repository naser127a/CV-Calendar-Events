<?php

namespace App\Actions\News;

use App\DTOs\News\UpdateNewsData;
use App\Models\News;
use App\Repositories\Contracts\NewsRepositoryInterface;
use App\Traits\HandlesFileUpload;

class UpdateNewsAction
{
    use HandlesFileUpload;
    public function __construct(
        private NewsRepositoryInterface $news
    ) {
        //
    }

    public function execute(News $news, UpdateNewsData $data): News
    {
        if ($data->image) {
            $image = $this->replaceFile($news->image, $data->image, 'news');
        }
        return $this->news->update($news, [
            'title' => $data->title,
            'summary' => $data->summary,
            'content' => $data->content,
            'image' => $image ?? $news->image,
            'type' => $news->type,
            'published_at' => $data->publishedAt,
            'status' => $data->status ?? true,
            'is_breaking' => $data->isBreaking ?? false,
            'breaking_until' => $data->breakingUntil
        ]);
    }
}
