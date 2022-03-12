<?php

namespace Tests\Feature\Models;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\Video;
use Illuminate\Database\QueryException;

class VideoTest extends TestCase
{
    use DatabaseMigrations;

    public function testRollbackStore()
    {
        $hasError=false;
        try {
            Video::create( [
                'title' => 'title test',
                'description' => 'description test',
                'year_launched' => 2020,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'opened' => true,
                'categories_id'=>[0,1,2]
            ]);
        } catch (QueryException $e) {
            $this->assertCount(0, Video::all());
            $hasError=true;
        }
        $this->assertTrue($hasError);
    }
    public function testRollbackUpdate()
    {   $hasError=false;
        $video=factory(Video::class)->create();
        $oldTitle=$video->title;
        try {
            $video->update([
                'title' => 'title test',
                'description' => 'description test',
                'year_launched' => 2020,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'opened' => true,
                'categories_id'=>[0,1,2]
            ]);
        } catch (QueryException $e) {
            $this->assertDataBaseHas('videos',['title'=>$oldTitle]);
            $hasError=true;
        }
        $this->assertTrue($hasError);
    }
}