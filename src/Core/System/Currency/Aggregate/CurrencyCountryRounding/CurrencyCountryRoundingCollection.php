<?php declare(strict_types=1);

namespace Shopware\Core\System\Currency\Aggregate\CurrencyCountryRounding;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<CurrencyCountryRoundingEntity>
 */
#[Package('fundamentals@framework')]
class CurrencyCountryRoundingCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'currency_country_rounding_collection';
    }

    protected function getExpectedClass(): string
    {
        return CurrencyCountryRoundingEntity::class;
    }
}
