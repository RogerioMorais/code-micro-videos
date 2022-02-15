<?php

namespace Tests\Unit\Models;

use App\Models\Video;
use App\Models\Traits\Uuid;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoUnitTest extends TestCase
{
    public function testFillable()
    {
        $fillable =  [
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration'
        ];
        $video = new Video();
        $this->assertEquals($fillable, $video->getFillable());
    }

    public function testCasts()
    {
        $casts = [
            'id' => 'string',
            'opened' => 'boolean',
            'year_launched' => 'integer',
            'duration' => 'integer'
        ];
        $video = new Video();
        $this->assertEquals($casts, $video->getCasts());
    }

    public function testIncrementing()
    {
        $video = new Video();
        $this->assertFalse($video->getIncrementing());
    }

    public function testDates()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $video = new Video();
        foreach ($dates as $date) {
            $this->assertContains($date, $video->getDates());
        }
        $this->assertCount(count($dates), $video->getDates());
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $videoTraits = array_keys(class_uses(Video::class));
        $this->assertEquals($traits, $videoTraits);
    }
}
