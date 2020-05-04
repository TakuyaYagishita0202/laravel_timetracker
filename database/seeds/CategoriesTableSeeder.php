<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'UI UX Design' => '#5735CE',
            'Programming' => '#FFC46D',
            'English' => '#FA0202',
        ];

        foreach ($categories as $name => $key) {
            DB::table('categories')->insert([
                'name' => $name,
                'color' => $key,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
