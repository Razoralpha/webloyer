<?php

declare(strict_types=1);

namespace Webloyer\App\Service\User;

use Webloyer\Domain\Model\User\UserApiToken;

class GetUserByApiTokenService extends UserService
{
    /**
     * @param GetUserRequest $request
     * @return mixed
     */
    public function execute($request = null)
    {
        $apiToken = new UserApiToken($request->getApiToken());
        $user = $this->userRepository->findByApiToken($apiToken);
        return $this->userDataTransformer->write($user)->read();
    }
}
