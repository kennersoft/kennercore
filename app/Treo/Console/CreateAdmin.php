<?php

declare(strict_types=1);

namespace Treo\Console;

use Treo\Core\ORM\EntityManager;
use Espo\Core\Utils\PasswordHash;
use Treo\Repositories\User as UserRepository;
use Throwable;

/**
 * Class Daemon
 *
 * @author r.ratsun <r.ratsun@treolabs.com>
 */
class CreateAdmin extends AbstractConsole
{
    private const SYSTEM_NAME = 'system';
    private const SYSTEM_ID = 'system';
    private const ADMIN_ID = '1';
    private const ADMIN_NAME = 'admin';
    private const ADMIN_PASS = 'admin';
    private const SQL = <<<SQL
INSERT INTO user (id, user_name, password, is_active, is_admin)
VALUES (:id, :userName, :password, :isActive, :isAdmin);
SQL;

    /**
     * @var bool
     */
    public static $isHidden = false;

    private EntityManager $entityManager;
    private UserRepository $userRepository;

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Creates default admin:admin user';
    }

    /**
     * @inheritDoc
     */
    public function run(array $data): void
    {
        $this->entityManager = $this->getContainer()->get('EntityManager');
        $this->userRepository = $this->entityManager->getRepository('User');

        try {
            $this->createSystemUser();
            $this->createAdminUser();
        } catch (Throwable $exception) {
            var_dump($exception->getMessage());
        }

        echo 'Admin and system user created successfully' . PHP_EOL;
    }

    private function createSystemUser(): void
    {
        if ($this->isUserExists(self::SYSTEM_NAME)) {
            echo 'System already exists.' . PHP_EOL;
            return;
        }

        $this->createUser(self::SYSTEM_ID, self::SYSTEM_NAME, null, false);
    }

    private function createAdminUser(): void
    {
        if ($this->isUserExists(self::ADMIN_NAME)) {
            echo 'Admin already exists.' . PHP_EOL;
            return;
        }

        $this->createUser(self::ADMIN_ID, self::ADMIN_NAME, $this->hashPassword(self::ADMIN_PASS), true);
    }

    private function createUser(string $id, string $name, ?string $password, bool $isAdmin): void
    {
        $this->entityManager->getPDO()->prepare(self::SQL)->execute([
            'id' => $id,
            'userName' => $name,
            'password' => $password,
            'isActive' => true,
            'isAdmin' => $isAdmin
        ]);
    }

    private function hashPassword(string $password): string
    {
        return (new PasswordHash($this->getConfig()))->hash($password);
    }

    private function isUserExists(string $name): bool
    {
        return $this->userRepository->where(['userName' => $name])->findOne() !== null;
    }
}