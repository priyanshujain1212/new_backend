<?php

use App\Models\FooterMenuSection;
use Illuminate\Database\Seeder;

class FooterMenuSectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FooterMenuSection::insert([
            [
                'name' => 'About',
            ],
            [
                'name' => 'Services',
            ],
            [
                'name' => 'For Users',
            ],
        ]);
    }
}
