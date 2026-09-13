<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        // نحتاج إلى مستخدم لربطه بحقل created_by
        $user = User::first();

        if (!$user) {
            $this->command->warn('الرجاء إنشاء مستخدم أولاً (UserSeeder) قبل تشغيل NewsSeeder.');
            return;
        }

        $newsData = [
            [
                'title' => 'إطلاق النظام الجديد بنجاح',
                'summary' => 'تم بحمد الله إطلاق النسخة الجديدة من النظام بمميزات عصرية.',
                'content' => 'محتوى تفصيلي للخبر يتحدث عن مميزات النظام وسرعته وكيفية الاستفادة منه...',
                'type' => 'announcement',
                'source_type' => 'local',
                'published_at' => Carbon::now(),
                'status' => true,
                'is_breaking' => true,
                'breaking_until' => Carbon::now()->addDays(2),
                'created_by' => $user->id,
            ],
            [
                'title' => 'تحديثات أمنية هامة',
                'summary' => 'مجموعة من التحديثات الأمنية لضمان سلامة بيانات المستخدمين.',
                'content' => 'نوصي جميع المستخدمين بتغيير كلمات المرور الخاصة بهم دورياً...',
                'type' => 'news',
                'source_type' => 'local',
                'published_at' => Carbon::now()->subDays(1),
                'status' => true,
                'is_breaking' => false,
                'created_by' => $user->id,
            ]
        ];

        foreach ($newsData as $news) {
            News::create($news);
        }
    }
}
