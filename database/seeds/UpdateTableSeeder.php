<?php

use App\Enums\UpdateStatus;
use App\Models\Update;
use Illuminate\Database\Seeder;


class UpdateTableSeeder extends Seeder
{
    public function run()
    {
        Update::create(['version' => '1.0', 'status' => UpdateStatus::SUCCESS, 'log' => '<h5>+ [Install] Initial Release</h5>' ]);
    }

}
