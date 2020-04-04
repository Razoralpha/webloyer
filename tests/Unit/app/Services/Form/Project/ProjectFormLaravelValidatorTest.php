<?php

namespace Tests\Unit\app\Services\Form\Project;

use App\Services\Form\Project\ProjectFormLaravelValidator;

use Tests\Helpers\Factory;
use Tests\TestCase;

class ProjectFormLaravelValidatorTest extends TestCase
{
    protected $useDatabase = true;

    public function test_Should_FailToValidate_When_RecipeIdFieldIsMissing()
    {
        Factory::create('App\Models\Server', [
            'name'        => 'Server 1',
            'description' => '',
            'body'        => '',
        ]);

        $input = [
            'name'       => 'Project 1',
            'server_id'  => 1,
            'repository' => 'http://example.com',
            'stage'      => 'staging',
        ];

        $form = new ProjectFormLaravelValidator($this->app['validator']);
        $result = $form->with($input)->passes();
        $errors = $form->errors();

        $this->assertFalse($result, 'Expected validation to fail.');
        $this->assertInstanceOf('Illuminate\Support\MessageBag', $errors);
    }

    public function test_Should_FailToValidate_When_NameFieldIsMissing()
    {
        Factory::create('App\Models\Recipe', [
            'name'        => 'Recipe 1',
            'description' => '',
            'body'        => '',
        ]);

        Factory::create('App\Models\Server', [
            'name'        => 'Server 1',
            'description' => '',
            'body'        => '',
        ]);

        $input = [
            'recipe_id'  => [1],
            'server_id'  => 1,
            'repository' => 'http://example.com',
            'stage'      => 'staging',
        ];

        $form = new ProjectFormLaravelValidator($this->app['validator']);

        $result = $form->with($input)->passes();
        $errors = $form->errors();

        $this->assertFalse($result, 'Expected validation to fail.');
        $this->assertInstanceOf('Illuminate\Support\MessageBag', $errors);
    }

    public function test_Should_FailToValidate_When_ServerIdFieldIsMissing()
    {
        Factory::create('App\Models\Recipe', [
            'name'        => 'Recipe 1',
            'description' => '',
            'body'        => '',
        ]);

        $input = [
            'name'       => 'Project 1',
            'recipe_id'  => [1],
            'repository' => 'http://example.com',
            'stage'      => 'staging',
        ];

        $form = new ProjectFormLaravelValidator($this->app['validator']);

        $result = $form->with($input)->passes();
        $errors = $form->errors();

        $this->assertFalse($result, 'Expected validation to fail.');
        $this->assertInstanceOf('Illuminate\Support\MessageBag', $errors);
    }

    public function test_Should_FailToValidate_When_RepositoryFieldIsMissing()
    {
        Factory::create('App\Models\Recipe', [
            'name'        => 'Recipe 1',
            'description' => '',
            'body'        => '',
        ]);

        Factory::create('App\Models\Server', [
            'name'        => 'Server 1',
            'description' => '',
            'body'        => '',
        ]);

        $input = [
            'name'      => 'Project 1',
            'recipe_id' => [1],
            'server_id' => 1,
            'stage'     => 'staging',
        ];

        $form = new ProjectFormLaravelValidator($this->app['validator']);

        $result = $form->with($input)->passes();
        $errors = $form->errors();

        $this->assertFalse($result, 'Expected validation to fail.');
        $this->assertInstanceOf('Illuminate\Support\MessageBag', $errors);
    }

    public function test_Should_FailToValidate_When_RepositoryFieldIsInvalidUrl()
    {
        // HACK Laravel 5.2 URL validation doesn't work with PHP 7.3 due to preg_match() error.
        if (version_compare(phpversion(), '7.3.0', '>=')) {
            $this->markTestIncomplete("Laravel 5.2 URL validation doesn't work with PHP 7.3 due to preg_match() error.");
        }

        Factory::create('App\Models\Recipe', [
            'name'        => 'Recipe 1',
            'description' => '',
            'body'        => '',
        ]);

        Factory::create('App\Models\Server', [
            'name'        => 'Server 1',
            'description' => '',
            'body'        => '',
        ]);

        $input = [
            'name'       => 'Project 1',
            'recipe_id'  => [1],
            'server_id'  => 1,
            'repository' => 'invalid_url',
            'stage'      => 'staging',
        ];

        $form = new ProjectFormLaravelValidator($this->app['validator']);

        $result = $form->with($input)->passes();
        $errors = $form->errors();

        $this->assertFalse($result, 'Expected validation to fail.');
        $this->assertInstanceOf('Illuminate\Support\MessageBag', $errors);
    }

    public function test_Should_FailToValidate_When_StageFieldIsMissing()
    {
        Factory::create('App\Models\Recipe', [
            'name'        => 'Recipe 1',
            'description' => '',
            'body'        => '',
        ]);

        Factory::create('App\Models\Server', [
            'name'        => 'Server 1',
            'description' => '',
            'body'        => '',
        ]);

        $input = [
            'name'       => 'Project 1',
            'recipe_id'  => [1],
            'server_id'  => 1,
            'repository' => 'http://example.com',
        ];

        $form = new ProjectFormLaravelValidator($this->app['validator']);

        $result = $form->with($input)->passes();
        $errors = $form->errors();

        $this->assertFalse($result, 'Expected validation to fail.');
        $this->assertInstanceOf('Illuminate\Support\MessageBag', $errors);
    }

    public function test_Should_PassToValidate_When_NameFieldAndRecipeIdFieldAndServerIdFieldAndRepositoryFieldAndStageFieldAreValid()
    {
        Factory::create('App\Models\Recipe', [
            'name'        => 'Recipe 1',
            'description' => '',
            'body'        => '',
        ]);

        Factory::create('App\Models\Server', [
            'name'        => 'Server 1',
            'description' => '',
            'body'        => '',
        ]);

        $input = [
            'name'       => 'Project 1',
            'recipe_id'  => [1],
            'server_id'  => 1,
            'repository' => 'http://example.com',
            'stage'      => 'staging',
        ];

        $form = new ProjectFormLaravelValidator($this->app['validator']);

        $result = $form->with($input)->passes();
        $errors = $form->errors();

        $this->assertTrue($result, 'Expected validation to succeed.');
        $this->assertEmpty($errors);
    }
}
