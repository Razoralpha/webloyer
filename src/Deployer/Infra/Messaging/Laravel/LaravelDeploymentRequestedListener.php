<?php

declare(strict_types=1);

namespace Deployer\Infra\Messaging\Laravel;

use Common\Domain\Model\Event\DomainEventPublisher;
use DateTimeImmutable;
use Deployer\Domain\Model\{
    DeployerFinished,
    DeployerProgressed,
    DeployerStarted,
};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Webloyer\Domain\Model\Deployment\DeploymentRequested;
use Webloyer\Domain\Model\Recipe\Recipe;

class LaravelDeploymentRequestedListener implements ShouldQueue
{
    private $createdFiles = [];

    public function __destruct()
    {
        if (empty($this->createdFiles)) {
            return;
        }
        foreach ($this->createdFiles as $createdFile) {
            $this->deleteFile($createdFile);
        }
    }

    /**
     * Handle the event.
     *
     * @param DeploymentRequested $event
     * @return void
     */
    public function handle(DeploymentRequested $event): void
    {
        $this->createDeployerFile($event);
        $this->runDeployer($event);
    }

    /**
     * @param DeploymentRequested $event
     * @return void
     */
    public function createDeployerFile(DeploymentRequested $event): void
    {
        // Create recipe files
        $i = 1;
        foreach ($event->recipes()->toArray() as $recipe) {
            $this->createFile($this->getRecipeFileName($event, $i++), $recipe->body());
        }

        // Create the server file
        $this->createFile($this->getServerFileName($event), $event->server()->body());

        // Create the deployer file
        $contents[] = '<?php';
        $contents[] = 'namespace Deployer;';
        foreach ($this->getRecipeFileNames($event) as $recipeFileName) {
            $contents[] = "require '" . $recipeFileName . "';";
        }
        $contents[] = "set('repository', '" . $event->project()->repositoryUrl() . "');";
        $contents[] = "serverList('" . $this->getServerFileName($event) . "');";
        $this->createFile($this->getDeployerFileName($event), implode(PHP_EOL, $contents));
    }

    /**
     * @param DeploymentRequested $event
     * @return void
     */
    public function runDeployer(DeploymentRequested $event): void
    {
        $process = new Process([
            base_path('vendor/bin/dep'),
            '-f=' . $this->getDeployerFileName($event),
            '--ansi',
            '-n',
            '-vvv',
            $event->deployment()->task(),
            $event->project()->stageName(),
        ]);
        $process->setTimeout(600);
        DomainEventPublisher::getInstance()->publish(
            new DeployerStarted(
            )
        );

        $log = '';
        $process->run(function (string $type, string $buffer) use (&$log) {
            $log .= $buffer;
            DomainEventPublisher::getInstance()->publish(
                new DeployerProgressed(
                )
            );
        });

        $log = $process->isSuccessful() ? $process->getOutput() : $process->getErrorOutput();
        $status = $process->getExitCode();
        $finishDate = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        DomainEventPublisher::getInstance()->publish(
            new DeployerFinished(
            )
        );
    }

    public function getServerFileName(DeploymentRequested $event): string
    {
        return sprintf('server_%s_%s.yaml', $event->deployment()->projectId(), $event->deployment()->number());
    }

    public function getRecipeFileNames(DeploymentRequested $event): array
    {
        $i = 1;
        return array_map(function (Recipe $recipe) use ($event, $i): string {
            return $this->getRecipeFileName($event, $i++);
        }, $event->recipes()->toArray());
    }

    public function getRecipeFileName(DeploymentRequested $event, int $i): string
    {
        return sprintf('server_%s_%s_%s.php', $event->deployment()->projectId(), $event->deployment()->number(), $i);
    }

    public function getDeployerFileName(DeploymentRequested $event): string
    {
        return sprintf('deployer_%s_%s.php', $event->deployment()->projectId(), $event->deployment()->number());
    }

    public function createFile(string $fileName, string $contents): void
    {
        Storage::disk('local')->put($fileName, $contents);
        $this->createdFiles[] = $fileName;
    }

    public function deleteFile(string $fileName): void
    {
        Storage::disk('local')->delete($fileName);
    }
}
