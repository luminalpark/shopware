<?php declare(strict_types=1);

namespace Shopware\Core\Framework\App\Aggregate\ActionButton;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system
 *
 * @extends EntityCollection<ActionButtonEntity>
 */
#[Package('framework')]
class ActionButtonCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ActionButtonEntity::class;
    }
}
