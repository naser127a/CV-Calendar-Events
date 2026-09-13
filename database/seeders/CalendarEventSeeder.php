<?php

namespace Database\Seeders;

use App\Models\CalendarEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CalendarEventSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->value('id') ?? User::value('id');

        if (! $creatorId) {
            return;
        }

        $events = [
            [
                'title' => 'اجتماع الإدارة العامة',
                'description' => 'اجتماع لمناقشة خطة العمل الأسبوعية.',
                'start_at' => Carbon::parse('2026-07-28 10:00:00'),
                'end_at' => Carbon::parse('2026-07-28 11:00:00'),
                'all_day' => false,
                'color' => '#0d6efd',
                'status' => true,
            ],
            [
                'title' => 'ورشة تدريب الموظفين',
                'description' => 'ورشة داخلية لتدريب الموظفين الجدد.',
                'start_at' => Carbon::parse('2026-07-29 09:00:00'),
                'end_at' => Carbon::parse('2026-07-29 13:00:00'),
                'all_day' => false,
                'color' => '#198754',
                'status' => true,
            ],
            [
                'title' => 'إجازة رسمية',
                'description' => 'يوم عطلة رسمية في المؤسسة.',
                'start_at' => Carbon::parse('2026-08-01 00:00:00'),
                'end_at' => Carbon::parse('2026-08-01 23:59:59'),
                'all_day' => true,
                'color' => '#6c757d',
                'status' => true,
            ],
            [
                'title' => 'موعد مراجعة التقارير',
                'description' => 'مراجعة التقارير الشهرية قبل الإرسال.',
                'start_at' => Carbon::parse('2026-08-03 14:00:00'),
                'end_at' => Carbon::parse('2026-08-03 15:30:00'),
                'all_day' => false,
                'color' => '#ffc107',
                'status' => true,
            ],
            [
                'title' => 'صيانة النظام',
                'description' => 'إيقاف جزئي للنظام أثناء الصيانة.',
                'start_at' => Carbon::parse('2026-08-05 22:00:00'),
                'end_at' => Carbon::parse('2026-08-06 02:00:00'),
                'all_day' => false,
                'color' => '#dc3545',
                'status' => false,
            ],
        ];

        foreach ($events as $event) {
            CalendarEvent::firstOrCreate(
                [
                    'title' => $event['title'],
                    'start_at' => $event['start_at'],
                ],
                [
                    'description' => $event['description'],
                    'end_at' => $event['end_at'],
                    'all_day' => $event['all_day'],
                    'color' => $event['color'],
                    'created_by' => $creatorId,
                    'status' => $event['status'],
                ]
            );
        }
    }
}
