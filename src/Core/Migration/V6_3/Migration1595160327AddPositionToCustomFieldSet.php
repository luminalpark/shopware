<?php declare(strict_types=1);

namespace Shopware\Core\Migration\V6_3;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
#[Package('framework')]
class Migration1595160327AddPositionToCustomFieldSet extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1595160327;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `custom_field_set`
            ADD COLUMN `position` INT(11) NOT NULL DEFAULT 1 AFTER `active`;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
