<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ApplySettings;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Ngmy\Webloyer\IdentityAccess\Domain\Model\User\User;
use Ngmy\Webloyer\Webloyer\Application\Recipe\RecipeService;
use Ngmy\Webloyer\Webloyer\Application\Project\ProjectService;
use Ngmy\Webloyer\Webloyer\Domain\Model\Project\Project;
use Ngmy\Webloyer\Webloyer\Domain\Model\Project\ProjectId;
use Ngmy\Webloyer\Webloyer\Domain\Model\Recipe\Recipe;
use Ngmy\Webloyer\Webloyer\Domain\Model\Recipe\RecipeId;
use Ngmy\Webloyer\Webloyer\Port\Adapter\Form\RecipeForm\RecipeForm;
use Session;
use Tests\Helpers\ControllerTestHelper;
use Tests\Helpers\DummyMiddleware;
use Tests\Helpers\MockeryHelper;
use TestCase;

class RecipesControllerTest extends TestCase
{
    use ControllerTestHelper;

    use MockeryHelper;

    private $recipeForm;

    private $recipeService;

    private $projectService;

    public function setUp()
    {
        parent::setUp();

        $this->app->instance(ApplySettings::class, new DummyMiddleware());

        Session::start();

        $user = $this->mock(User::class);
        $user->shouldReceive('can')->andReturn(true);
        $user->shouldReceive('name');
        $this->auth($user);

        $this->recipeForm = $this->mock(RecipeForm::class);
        $this->recipeService = $this->mock(RecipeService::class);
        $this->projectService = $this->mock(ProjectService::class);

        $this->app->instance(RecipeForm::class, $this->recipeForm);
        $this->app->instance(RecipeService::class, $this->recipeService);
        $this->app->instance(ProjectService::class, $this->projectService);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->closeMock();
    }

    public function test_Should_DisplayIndexPage_When_IndexPageIsRequested()
    {
        $recipe = $this->createRecipe();
        $recipes = new Collection([
            $recipe,
        ]);
        $page = 1;
        $perPage = 10;

        $this->recipeService
            ->shouldReceive('getRecipesByPage')
            ->with($page, $perPage)
            ->andReturn(
                new LengthAwarePaginator(
                    $recipes,
                    $recipes->count(),
                    $perPage,
                    $page,
                    [
                        'path' => Paginator::resolveCurrentPath(),
                    ]
                )
            )
            ->once();

        $response = $this->get('recipes');

        $response->assertStatus(200);
        $response->assertViewHas('recipes');
    }

    public function test_Should_DisplayCreatePage_When_CreatePageIsRequested()
    {
        $response = $this->get('recipes/create');

        $response->assertStatus(200);
    }

    public function test_Should_RedirectToIndexPage_When_StoreProcessSucceeds()
    {
        $this->recipeForm
            ->shouldReceive('save')
            ->andReturn(true)
            ->once();

        $response = $this->post('recipes');

        $response->assertRedirect('recipes');
    }

    public function test_Should_RedirectToCreatePage_When_StoreProcessFails()
    {
        $this->recipeForm
            ->shouldReceive('save')
            ->andReturn(false)
            ->once();

        $this->recipeForm
            ->shouldReceive('errors')
            ->withNoArgs()
            ->andReturn(new MessageBag())
            ->once();

        $response = $this->post('recipes');

        $response->assertRedirect('recipes/create');
        $response->assertSessionHasErrors();
    }

    public function test_Should_DisplayShowPage_When_ShowPageIsRequestedAndResourceIsFound()
    {
        $recipe = $this->createRecipe([
            'afferentProjectIds' => [1, 2],
        ]);

        foreach ($recipe->afferentProjectIds() as $afferentProjectId) {
            $project = $this->createProject([
                'projectId' => $afferentProjectId->id(),
            ]);
            $this->projectService
                ->shouldReceive('getProjectById')
                ->with($project->projectId()->id())
                ->andReturn($project)
                ->once();
        }

        $this->recipeService
            ->shouldReceive('getRecipeById')
            ->with($recipe->recipeId()->id())
            ->andReturn($recipe)
            ->once();

        $response = $this->get("recipes/{$recipe->recipeId()->id()}");

        $response->assertStatus(200);
        $response->assertViewHas('recipe');
        $response->assertViewHas('afferentProjects');
    }

    public function test_Should_DisplayNotFoundPage_When_ShowPageIsRequestedAndResourceIsNotFound()
    {
        $recipeId = 1;

        $this->recipeService
            ->shouldReceive('getRecipeById')
            ->with($recipeId)
            ->andReturn(null)
            ->once();

        $response = $this->get("recipes/$recipeId");

        $response->assertStatus(404);
    }

    public function test_Should_DisplayEditPage_When_EditPageIsRequestedAndResourceIsFound()
    {
        $recipe = $this->createRecipe();

        $this->recipeService
            ->shouldReceive('getRecipeById')
            ->with($recipe->recipeId()->id())
            ->andReturn($recipe)
            ->once();

        $response = $this->get("recipes/{$recipe->recipeId()->id()}/edit");

        $response->assertStatus(200);
        $response->assertViewHas('recipe');
    }

    public function test_Should_DisplayNotFoundPage_When_EditPageIsRequestedAndResourceIsNotFound()
    {
        $recipeId = 1;

        $this->recipeService
            ->shouldReceive('getRecipeById')
            ->with($recipeId)
            ->andReturn(null)
            ->once();

        $response = $this->get("recipes/$recipeId/edit");

        $response->assertStatus(404);
    }

    public function test_Should_RedirectToIndexPage_When_UpdateProcessSucceeds()
    {
        $recipe = $this->createRecipe();

        $this->recipeService
            ->shouldReceive('getRecipeById')
            ->with($recipe->recipeId()->id())
            ->andReturn($recipe)
            ->once();

        $this->recipeForm
            ->shouldReceive('update')
            ->andReturn(true)
            ->once();

        $response = $this->put("recipes/{$recipe->recipeId()->id()}");

        $response->assertRedirect('recipes');
    }

    public function test_Should_RedirectToEditPage_When_UpdateProcessFails()
    {
        $recipe = $this->createRecipe();

        $this->recipeService
            ->shouldReceive('getRecipeById')
            ->with($recipe->recipeId()->id())
            ->andReturn($recipe)
            ->once();

        $this->recipeForm
            ->shouldReceive('update')
            ->andReturn(false)
            ->once();

        $this->recipeForm
            ->shouldReceive('errors')
            ->withNoArgs()
            ->andReturn(new MessageBag())
            ->once();

        $response = $this->put("recipes/{$recipe->recipeId()->id()}");

        $response->assertRedirect("recipes/{$recipe->recipeId()->id()}/edit");
        $response->assertSessionHasErrors();
    }

    public function test_Should_DisplayNotFoundPage_When_UpdateProcessIsRequestedAndResourceIsNotFound()
    {
        $recipeId = 1;

        $this->recipeService
            ->shouldReceive('getRecipeById')
            ->with($recipeId)
            ->andReturn(null)
            ->once();

        $response = $this->put("recipes/$recipeId");

        $response->assertStatus(404);
    }

    public function test_Should_RedirectToIndexPage_When_DestroyProcessIsRequestedAndDestroyProcessSucceeds()
    {
        $recipe = $this->createRecipe();

        $this->recipeService
            ->shouldReceive('getRecipeById')
            ->with($recipe->recipeId()->id())
            ->andReturn($recipe)
            ->once();

        $this->recipeService
            ->shouldReceive('removeRecipe')
            ->once();

        $response = $this->delete("recipes/{$recipe->recipeId()->id()}");

        $response->assertRedirect('recipes');
    }

    public function test_Should_DisplayNotFoundPage_When_DestroyProcessIsRequestedAndResourceIsNotFound()
    {
        $recipeId = 1;

        $this->recipeService
            ->shouldReceive('getRecipeById')
            ->with($recipeId)
            ->andReturn(null)
            ->once();

        $response = $this->delete("recipes/$recipeId");

        $response->assertStatus(404);
    }

    private function createRecipe(array $params = [])
    {
        $recipeId = 1;
        $name = '';
        $description = '';
        $body = '';
        $afferentProjectIds = [1];
        $createdAt = null;
        $updatedAt = null;
        $concurrencyVersion = '';

        extract($params);

        $recipe = $this->mock(Recipe::class);

        $recipe->shouldReceive('recipeId')->andReturn(new RecipeId($recipeId));
        $recipe->shouldReceive('name')->andReturn($name);
        $recipe->shouldReceive('description')->andReturn($description);
        $recipe->shouldReceive('body')->andReturn($body);
        $recipe->shouldReceive('afferentProjectIds')->andReturn(array_map(function ($afferentProjectId) {
            return new ProjectId($afferentProjectId);
        }, $afferentProjectIds));
        $recipe->shouldReceive('afferentProjectsCount')->andReturn(count($afferentProjectIds));
        $recipe->shouldReceive('createdAt')->andReturn(new Carbon($createdAt));
        $recipe->shouldReceive('updatedAt')->andReturn(new Carbon($updatedAt));
        $recipe->shouldReceive('concurrencyVersion')->andReturn($concurrencyVersion);

        return $recipe;
    }

    private function createProject(array $params = [])
    {
        $projectId = 1;
        $name = '';

        extract($params);

        $project = $this->mock(Project::class);

        $project->shouldReceive('projectId')->andReturn(new ProjectId($projectId));
        $project->shouldReceive('name')->andReturn($name);

        return $project;
    }
}
