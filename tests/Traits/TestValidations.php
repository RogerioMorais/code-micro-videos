<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;

trait TestValidations
{
    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    protected function assertInvalidationInStoreAction(
        array $data,
        string $rule,
        array $ruleParams = []
    ) {
        $response = $this->json('POST', $this->routeStore(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationInUpdateAction(
        array $data,
        string $rule,
        array $ruleParams = []
    ) {
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
    }

    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $ruleParams = []
    ) {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $fieldName = str_replace('_', ' ', $field);
            $response->assertJsonFragment([
                trans("validation.{$rule}", ['attribute' => $fieldName] + $ruleParams)
            ]);
        }
    }

    protected function assertInvalidationFile($field,$extension,$maxSize,$rule,$ruleParams=[]){
        $routes=[
            [
                "method"=>"POST",
                'route'=>$this->routeStore()
            ],
            [
                "method"=>"PUT",
                'route'=>$this->routeUpdate()
            ]
        ];

        foreach ($routes as $route) {
            $file=UploadedFile::fake()->create("$field.1$extension");
            $response=$this->json($route['method'],$route['route'],
            [$field=>$file]);
            
            $this->assertInvalidationFields($response,[$field],$rule,$ruleParams);

            $file=UploadedFile::fake()->create("$field.$extension")->size($maxSize+1);
            $response=$this->json($route['method'],$route['route'],
            [$field=>$file]);

            $this->assertInvalidationFields($response,[$field],"max.file",['max'=>$maxSize]);
            
        }
    }
}
