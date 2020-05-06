<?php

declare(strict_types=1);

namespace Webloyer;

use Deployer\Domain\Model\{
    DeployerFinished,
    DeployerProgressed,
    DeployerStarted,
};
use Event;
use Illuminate\Support\ServiceProvider;
use Webloyer\Domain\Model\Deployment\{
    DeploymentCompleted,
    DeploymentRepository,
    DeploymentRequested,
};
use Webloyer\Domain\Model\Project\ProjectRepository;
use Webloyer\Domain\Model\Recipe\RecipeRepository;
use Webloyer\Domain\Model\Server\ServerRepository;
use Webloyer\Domain\Model\Setting\Mail\MailSettingRepository;
use Webloyer\Domain\Model\User\UserRepository;
use Webloyer\Infra\Messaging\LaravelQueue\{
    LaravelQueueDeployerFinishedListener,
    LaravelQueueDeployerProgressedListener,
    LaravelQueueDeployerStartedListener,
    SendNotificationWhenDeploymentCompletedListener,
};
use Webloyer\Infra\Db\Repositories\Deployment\DbDeploymentRepository;
use Webloyer\Infra\Db\Repositories\Project\DbProjectRepository;
use Webloyer\Infra\Db\Repositories\Recipe\DbRecipeRepository;
use Webloyer\Infra\Db\Repositories\Server\DbServerRepository;
use Webloyer\Infra\Db\Repositories\Setting\Mail\DbMailSettingRepository;
use Webloyer\Infra\Db\Repositories\User\DbUserRepository;

class WebloyerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(DeploymentRepository::class, DbDeploymentRepository::class);
        $this->app->bind(MailSettingRepository::class, DbMailSettingRepository::class);
        $this->app->bind(ProjectRepository::class, DbProjectRepository::class);
        $this->app->bind(RecipeRepository::class, DbRecipeRepository::class);
        $this->app->bind(ServerRepository::class, DbServerRepository::class);
        $this->app->bind(UserRepository::class, DbUserRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        Event::listen(DeploymentCompleted::class, SendNotificationWhenDeploymentCompletedListener::class);
        Event::listen(DeployerFinished::class, LaravelQueueDeployerFinishedListener::class);
        Event::listen(DeployerProgessed::class, LaravelQueueDeployerProgressedListener::class);
        Event::listen(DeployerStarted::class, LaravelQueueDeployerStartedListener::class);
    }
}
