<?php

namespace Tests\Stubs\Models;

use App\Models\Traits\uploadFile;
use Illuminate\Database\Eloquent\Model;

class UploadFilesStub extends Model
{
   use uploadFile;
   public static $fileFields=['file1','file2'];

   protected function uploadDir(){
    return "1";
   }
}