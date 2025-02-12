<?php declare(strict_types=1);

namespace Shopware\Core\Migration\V6_5;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('framework')]
class Migration1678801126AddScheduledTaskDefaultRunIntervalColumn extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1678801126;
    }

    public function update(Connection $connection): void
    {
        if ($this->columnExists($connection, 'scheduled_task', 'default_run_interval')) {
            return;
        }

        $this->addColumn(
            connection: $connection,
            table: 'scheduled_task',
            column: 'default_run_interval',
            type: 'INT(11)'
        );

        $this->setMinRunInterval($connection);

        $connection->executeStatement(
            'ALTER TABLE `scheduled_task` MODIFY COLUMN `default_run_interval` INT(11) NOT NULL;'
        );
    }

    private function setMinRunInterval(Connection $connection): void
    {
        $tasks = $connection->fetchAllAssociative(
            'SELECT `id`, `run_interval`, `scheduled_task_class` FROM `scheduled_task`;'
        );

        foreach ($tasks as $task) {
            /** @var class-string<ScheduledTask> $taskClass */
            $taskClass = $task['scheduled_task_class'];

            try {
                $default = $taskClass::getDefaultInterval();
            } catch (\Throwable) {
                $default = $task['run_interval'];
            }
            $connection->executeStatement(
                'UPDATE `scheduled_task` SET `default_run_interval` = :default WHERE `id` = :id;',
                [
                    'default' => $default,
                    'id' => $task['id'],
                ]
            );
        }
    }
}
