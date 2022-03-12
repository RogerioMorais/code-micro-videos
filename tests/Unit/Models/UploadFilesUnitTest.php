<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Tests\Stubs\Models\UploadFilesStub;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ../vendor/bin/phpunit Unit/Models/UploadFilesUnitTest.php
class UploadFilesUnitTest extends TestCase
{
    private $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new UploadFilesStub();
    }

    public function testUploadFile(){
        Storage::fake();
        $file=UploadedFile::fake()->create('video.jpg');
        $this->object->uploadFile($file);
        Storage::assertExists("1/{$file->hashName()}");
    }
 
    public function testUploadFiles(){
        Storage::fake();
        $file1=UploadedFile::fake()->create('video.jpg');
        $file2=UploadedFile::fake()->create('video.jpg');

        $this->object->uploadFiles([$file1,$file2]);
        Storage::assertExists("1/{$file1->hashName()}");
        Storage::assertExists("1/{$file2->hashName()}");

    }

    public function testDeleteFile(){
        Storage::fake();
        $file=UploadedFile::fake()->create('video.jpg');
        $this->object->uploadFile($file);
        $fileName=$file->hashName();
        $this->object->deleteFile($fileName);
        Storage::assertMissing("1/{$fileName}");
    }
    
    public function testDeleteFiles(){
        Storage::fake();
        $file1=UploadedFile::fake()->create('video.jpg');
        $file2=UploadedFile::fake()->create('video.jpg');
        $this->object->uploadFiles([$file1,$file2]);

        $this->object->deleteFiles([$file1->hashName(),$file2]);
        Storage::assertMissing("1/{$file1->hashName()}");
        Storage::assertMissing("1/{$file2->hashName()}");
    }

    public function testExtractFiles(){
        $attributes=[];
        $files=UploadFilesStub::extractFiles($attributes);
        $this->assertCount(0,$attributes);
        $this->assertCount(0,$files);

        $attributes=['file1'=>'teste'];
        $files=UploadFilesStub::extractFiles($attributes);
        $this->assertCount(1,$attributes);
        $this->assertEquals(['file1'=>'teste'],$attributes);
        $this->assertCount(0,$files);

        $attributes=['file1'=>'teste','file2'=>'teste'];
        $files=UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2,$attributes);
        $this->assertEquals(['file1'=>'teste','file2'=>'teste'],$attributes);
        $this->assertCount(0,$files);

        $file1=UploadedFile::fake()->create('video.jpg');
        $attributes=['file1'=>$file1,'other'=>'teste'];
        $files=UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2,$attributes);
        $this->assertEquals(['file1'=>$file1->hashName(),'other'=>'teste'],$attributes);
        $this->assertEquals([$file1],$files);
        $this->assertCount(1,$files);

        $attributes=['file1'=>$file1,'file2'=>'teste'];
        $files=UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2,$attributes);
        $this->assertEquals(['file1'=>$file1->hashName(),'file2'=>'teste'],$attributes);
        $this->assertEquals([$file1],$files);
        $this->assertCount(1,$files);

        $file2=UploadedFile::fake()->create('video.jpg');
        $attributes=['file1'=>$file1,'file2'=>$file2,'other'=>'teste'];
        $files=UploadFilesStub::extractFiles($attributes);
        $this->assertCount(3,$attributes);
        $this->assertEquals(['file1'=>$file1->hashName(),
                            'file2'=>$file2->hashName(),
                            'other'=>'teste'
                            ],$attributes);
        $this->assertEquals([$file1,$file2],$files);
        $this->assertCount(2,$files);

    }
}
