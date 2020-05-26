<?php

declare(strict_types=1);

namespace Webloyer\Infra\Framework\Laravel\App\Http\Controllers\User;

use Illuminate\Support\Str;
use Webloyer\App\Service\User\UpdateUserRequest;
use Webloyer\Infra\Framework\Laravel\App\Http\Requests\User\UpdateRequest;

class RegenerateApiTokenController extends BaseController
{
    /**
     * Handle the incoming request.
     *
     * @param UpdateRequest $request
     * @param string        $id
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UpdateRequest $request, string $id)
    {
        $serviceRequest = (new UpdateUserRequest())
            ->setId($id)
            ->setApiToken(Str::random(60));
        $this->service->execute($serviceRequest);

        return redirect()->route('users.index');
    }
}
