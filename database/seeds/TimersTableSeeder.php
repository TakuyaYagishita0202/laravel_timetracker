<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('timers')->insert([
            [
                'id' => 1,
                'name' => 'オンライン英会話',
                'memo' => 'QQEnglishで講座を受講する',
                'user_id' => '1',
                'started_at' => '2020-4-23 10:30:00',
                'stopped_at' => '2020-4-23 12:00:00',
                'created_at' => '2020-4-23 10:30:00',
                'updated_at' => '2020-4-23 12:00:00',
                'category_id' => '3',
                'category_name' => 'English',
                'category_color' => '#FA0202'
            ],
            [
                'id' => 2,
                'name' => 'Progate Ruby V',
                'memo' => '「Dateクラスとクラスメソッド」まで',
                'user_id' => '1',
                'started_at' => '2020-4-23 18:30:00',
                'stopped_at' => '2020-4-23 20:00:00',
                'created_at' => '2020-4-23 18:30:00',
                'updated_at' => '2020-4-23 20:00:00',
                'category_id' => '2',
                'category_name' => 'Programming',
                'category_color' => '#FFC46D'
            ],
            [
                'id' => 3,
                'name' => '瞬間英作文',
                'memo' => '100sentences終わらせる',
                'user_id' => '1',
                'started_at' => '2020-4-23 20:00:00',
                'stopped_at' => '2020-4-23 21:00:00',
                'created_at' => '2020-4-23 20:00:00',
                'updated_at' => '2020-4-23 21:00:00',
                'category_id' => '3',
                'category_name' => 'English',
                'category_color' => '#FA0202'
            ],
            [
                'id' => 4,
                'name' => 'UdemyでUI UXの勉強',
                'memo' => 'コース名：Learn Figma: User Interface Design Essentials - UI/UX Design', 'user_id' => '1',
                'started_at' => '2020-4-23 22:00:00',
                'stopped_at' => '2020-4-24 0:00:00',
                'created_at' => '2020-4-23 22:00:00',
                'updated_at' => '2020-4-24 0:00:00',
                'category_id' => '1',
                'category_name' => 'UI UX Design',
                'category_color' => '#5735CE'
            ],
            [
                'id' => 5,
                'name' => 'オンライン英会話',
                'memo' => 'QQEnglishで講座を受講する',
                'user_id' => '1',
                'started_at' => '2020-4-24 14:30:00',
                'stopped_at' => '2020-4-24 16:00:00',
                'created_at' => '2020-4-24 14:30:00',
                'updated_at' => '2020-4-24 16:00:00',
                'category_id' => '3',
                'category_name' => 'English',
                'category_color' => '#FA0202'
            ],
            [
                'id' => 6,
                'name' => 'Progate Ruby on rails 5 Ⅰ',
                'memo' => '「目標物を完成させよう」まで',
                'user_id' => '1',
                'started_at' => '2020-4-24 16:30:00',
                'stopped_at' => '2020-4-24 18:00:00',
                'created_at' => '2020-4-24 16:30:00',
                'updated_at' => '2020-4-24 18:00:00',
                'category_id' => '2',
                'category_name' => 'Programming',
                'category_color' => '#FFC46D'
            ],
            [
                'id' => 7,
                'name' => 'chot.designでデザインの勉強',
                'memo' => '「はじめてのデザイン 3. 色と配色の基本」まで',
                'user_id' => '1',
                'started_at' => '2020-4-24 18:00:00',
                'stopped_at' => '2020-4-24 19:00:00',
                'created_at' => '2020-4-24 18:00:00',
                'updated_at' => '2020-4-24 19:00:00',
                'category_id' => '1',
                'category_name' => 'UI UX Design',
                'category_color' => '#5735CE'
            ],
            [
                'id' => 8,
                'name' => 'Progate Ruby on rails 5 Ⅱ',
                'memo' => '「データベースを使ってみよう」まで',
                'user_id' => '1',
                'started_at' => '2020-4-24 20:00:00',
                'stopped_at' => '2020-4-24 22:00:00',
                'created_at' => '2020-4-24 20:00:00',
                'updated_at' => '2020-4-24 22:00:00',
                'category_id' => '2',
                'category_name' => 'Programming',
                'category_color' => '#FFC46D'
            ],
            [
                'id' => 9,
                'name' => 'TOEICの勉強',
                'memo' => 'スタディサプリで学んだ英単語を復習',
                'user_id' => '1',
                'started_at' => '2020-4-25 18:30:00',
                'stopped_at' => '2020-4-25 19:00:00',
                'created_at' => '2020-4-25 18:30:00',
                'updated_at' => '2020-4-25 19:00:00',
                'category_id' => '3',
                'category_name' => 'English',
                'category_color' => '#FA0202'
            ],
            [
                'id' => 10,
                'name' => 'Progate jQuery 中級編 道場コース',
                'memo' => '「レッスン一覧を作ろう」まで',
                'user_id' => '1',
                'started_at' => '2020-4-25 20:00:00',
                'stopped_at' => '2020-4-25 21:45:00',
                'created_at' => '2020-4-25 20:00:00',
                'updated_at' => '2020-4-25 21:45:00',
                'category_id' => '2',
                'category_name' => 'Programming',
                'category_color' => '#FFC46D'
            ],
            [
                'id' => 11,
                'name' => 'UdemyでFigmaの学習',
                'memo' => 'コース名：Learn Figma: User Interface Design Essentials - UI/UX Design',
                'user_id' => '1',
                'started_at' => '2020-4-25 22:00:00',
                'stopped_at' => '2020-4-25 23:00:00',
                'created_at' => '2020-4-25 22:00:00',
                'updated_at' => '2020-4-25 23:00:00',
                'category_id' => '1',
                'category_name' => 'UI UX Design',
                'category_color' => '#5735CE'
            ]
        ]);
    }
}
