<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Document;

use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Struct\Struct;

#[Package('after-sales')]
class DocumentIdStruct extends Struct
{
    public function __construct(
        protected string $id,
        protected string $deepLinkCode,
        protected ?string $mediaId = null,
        protected ?string $a11yMediaId = null,
    ) {
    }

    public function getDeepLinkCode(): string
    {
        return $this->deepLinkCode;
    }

    public function setDeepLinkCode(string $deepLinkCode): void
    {
        $this->deepLinkCode = $deepLinkCode;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function getA11yMediaId(): ?string
    {
        return $this->a11yMediaId;
    }

    public function getApiAlias(): string
    {
        return 'document_id';
    }
}
