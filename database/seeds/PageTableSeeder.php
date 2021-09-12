<?php

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Page::insert([
            [
                'title'                  => 'Terms & Conditions',
                'slug'                   => 'terms-and-condition',
                'description'            => 'Terms Conditions Description',
                'footer_menu_section_id' => 1,
                'template_id'            => 1,
                'creator_type'           => User::class,
                'editor_type'            => User::class,
                'creator_id'             => 1,
                'editor_id'              => 1,
            ],
            [
                'title'                  => 'Privacy',
                'slug'                   => 'privacy',
                'description'            => 'Privacy Description',
                'footer_menu_section_id' => 2,
                'template_id'            => 2,
                'creator_type'           => User::class,
                'editor_type'            => User::class,
                'creator_id'             => 1,
                'editor_id'              => 1,
            ],
        ]);
    }
}
