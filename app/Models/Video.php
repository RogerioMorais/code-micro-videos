<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\Traits\uploadFile;
use Exception;

use function Psy\debug;

class Video extends Model
{
    use SoftDeletes, Uuid;
    use uploadFile;

    const RATING_LIST = ['L', '10', '12', '14', '16', '18'];

    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    public static $fileFields=['video_file'];
    public static function create(array $attributes=[]){
        \Log::debug($attributes);
        $files=self::extractFiles($attributes);
        try{
            DB::beginTransaction();
            /** @var Video $obj */
            $obj=static::query()->create($attributes);
            static::handleRelations($obj,$attributes);
            $obj->uploadFiles($files);
            DB::commit();
            return $obj;
        }catch(Exception $e){
            if(isset($obj)){
                ///Excluir arquivos
            }
            \Log::debug($e);
            DB::rollBack();
            throw $e;
        }
    }

    public function update (array $attributes=[],array $options=[]){
        try{
            DB::beginTransaction();
            $saved=parent::update($attributes,$options);
            static::handleRelations($this,$attributes);
            if($saved){
            ///Uploada aqui
                //excluir os antigos
            }
            DB::commit();
            return $saved;
        }catch(Exception $e){
            ///Excluir arquivos de upload
            DB::rollBack();
            throw $e;
        }
    }

    public static function handleRelations(Video $video, array $attributes)
    {
        if(isset($attributes['categories_id'])){
            $video->categories()->sync($attributes['categories_id']);
        }
        if(isset($attributes['genres_id'])){
            $video->genres()->sync($attributes['genres_id']);
        }
    }

    public $incrementing = false;

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }

    protected function uploadDir()
    {
        return $this->id;
    }
}
