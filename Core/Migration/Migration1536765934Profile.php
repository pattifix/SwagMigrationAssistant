<?php declare(strict_types=1);

namespace SwagMigrationNext\Core\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1536765934Profile extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1536765934;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE `swag_migration_profile` (
    `id`                BINARY(16)  NOT NULL,
    `name`              VARCHAR(255),
    `gateway_name`      VARCHAR(255),
    `created_at`        DATETIME(3) NOT NULL,
    `updated_at`        DATETIME(3),
    PRIMARY KEY (`id`),
    CONSTRAINT `uniq.swag_migration_profile.name__gateway_name` UNIQUE (`name`, `gateway_name`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;
        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}