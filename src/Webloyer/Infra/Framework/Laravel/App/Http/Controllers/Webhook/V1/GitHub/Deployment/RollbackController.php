<?php

declare(strict_types=1);

namespace Webloyer\Infra\Framework\Laravel\App\Http\Controllers\Webhook\V1\GitHub\Deployment;

use Illuminate\Http\Request;
use Webloyer\App\Service\Deployment\RollbackDeploymentRequest;

class RollbackController extends BaseController
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param string  $projectId
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, string $projectId)
    {
        $serviceRequest = (new RollbackDeploymentRequest())
            ->setProjectId($projectId)
            ->setExecutor($request->user()->toEntity()->id());
        $deployment = $this->service->execute($serviceRequest);

        $link = link_to_route('projects.deployments.show', '#' . $deployment->number, [$projectId, $deployment->number]);
        $request->session()->flash('status', "The deployment $link was successfully started.");

        return redirect()->route('projects.deployments.index', [$projectId]);
    }
}