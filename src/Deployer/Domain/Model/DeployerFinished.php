<?php

declare(strict_types=1);

namespace Deployer\Domain\Model;

use Common\Domain\Model\Event\{
    DomainEvent,
    PublishableDomainEvent,
};

class DeployerFinished implements DomainEvent, PublishableDomainEvent
{
}
