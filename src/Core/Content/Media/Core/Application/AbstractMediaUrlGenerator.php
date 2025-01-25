<?php declare(strict_types=1);

namespace Shopware\Core\Content\Media\Core\Application;

use Shopware\Core\Content\Media\Core\Params\UrlParams;
use Shopware\Core\Framework\Log\Package;

/**
 * Url generator for media and thumbnails
 *
 * The url generator is called at runtime to generate the absolute urls for the media and thumbnails.
 * Generated urls are based on the stored paths in the database.
 */
#[Package('discovery')]
abstract class AbstractMediaUrlGenerator
{
    /**
     * Builds absolute urls for the given paths
     *
     * The provided paths must be relative paths (e.g. media/0a/test.jpg). The absolute url is generated by the configured filesystem.
     * The relative paths are stored in the database and the absolute urls are generated on the fly.
     *
     * @param array<string|int, UrlParams> $paths indexed by id, value contains the path
     *
     * @return array<string|int, string> indexed by id, value contains the absolute url (e.g. https://my.shop.de/media/0a/test.jpg)
     */
    abstract public function generate(array $paths): array;
}
