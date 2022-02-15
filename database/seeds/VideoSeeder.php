<?php

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run()
    {
        factory(Video::class, 100)->create();
    }
}
