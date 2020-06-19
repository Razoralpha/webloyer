<?php

declare(strict_types=1);

namespace Webloyer\Domain\Model\User;

abstract class UserRole extends User
{
    /** @var UserCore */
    private $core;

    /**
     * @param UserRoleSpecification $roleSpec
     * @param UserCore              $core
     * @return UserRole
     */
    public static function createFor(UserRoleSpecification $roleSpec, UserCore $core): UserRole
    {
        $role = $roleSpec->create();
        $role->core = $core;
        return $role;
    }

    /**
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function id(): string
    {
        return $this->core->id();
    }

    /**
     * {@inheritdoc}
     */
    public function email(): string
    {
        return $this->core->email();
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->core->name();
    }

    /**
     * {@inheritdoc}
     */
    public function password(): string
    {
        return $this->core->password();
    }

    /**
     * {@inheritdoc}
     */
    public function apiToken(): ?string
    {
        return $this->core->apiToken();
    }

    /**
     * {@inheritdoc}
     */
    public function roles(): array
    {
        return $this->roles->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function changeEmail(string $email): UserCore
    {
        return $this->core->changeEmail($email);
    }

    /**
     * {@inheritdoc}
     */
    public function changeName(string $name): UserCore
    {
        return $this->core->changeName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function changePassword(string $password): UserCore
    {
        return $this->core->changePassword($password);
    }

    /**
     * {@inheritdoc}
     */
    public function changeApiToken(string $apiToken): UserCore
    {
        return $this->core->changeApiToken($apiToken);
    }

    /**
     * {@inheritdoc}
     */
    public function provide(UserInterest $interest): void
    {
        $this->core->provide($interest);
    }

    /**
     * {@inheritdoc}
     */
    public function equals($object): bool
    {
        return $this->core->equals($object);
    }

    /**
     * {@inheritdoc}
     */
    public function addRole(UserRoleSpecification $roleSpec): void
    {
        $this->core->addRole($roleSpec);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole(UserRoleSpecification $roleSpec): bool
    {
        return $this->core->hasRole($roleSpec);
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole(UserRoleSpecification $roleSpec): void
    {
        $this->core->removeRole($roleSpec);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllRoles(): void
    {
        $this->core->removeAllRoles();
    }

    /**
     * {@inheritdoc}
     */
    public function getRole(UserRoleSpecification $roleSpec): ?UserRole
    {
        return $this->core->getRole($roleSpec);
    }
}
