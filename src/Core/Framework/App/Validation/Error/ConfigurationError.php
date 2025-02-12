<?php declare(strict_types=1);

namespace Shopware\Core\Framework\App\Validation\Error;

use Shopware\Core\Framework\Log\Package;

/**
 * @internal only for use by the app-system
 */
#[Package('framework')]
class ConfigurationError extends Error
{
    private const KEY = 'manifest-invalid-config';

    /**
     * @param list<string> $violations
     */
    public function __construct(array $violations)
    {
        $this->message = \sprintf(
            "The following custom components are not allowed to be used in app configuration:\n- %s",
            implode("\n- ", $violations)
        );

        parent::__construct($this->message);
    }

    public function getMessageKey(): string
    {
        return self::KEY;
    }
}
