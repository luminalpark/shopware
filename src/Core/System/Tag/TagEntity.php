<?php declare(strict_types=1);

namespace Shopware\Core\System\Tag;

use Shopware\Core\Checkout\Customer\CustomerCollection;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\LandingPage\LandingPageCollection;
use Shopware\Core\Content\Media\MediaCollection;
use Shopware\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientCollection;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Rule\RuleCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Framework\Log\Package;

#[Package('fundamentals@framework')]
class TagEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $name;

    /**
     * @var ProductCollection|null
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $products;

    /**
     * @var MediaCollection|null
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $media;

    /**
     * @var CategoryCollection|null
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $categories;

    /**
     * @var CustomerCollection|null
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $customers;

    /**
     * @var OrderCollection|null
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $orders;

    /**
     * @var ShippingMethodCollection|null
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $shippingMethods;

    /**
     * @var NewsletterRecipientCollection|null
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $newsletterRecipients;

    /**
     * @var LandingPageCollection|null
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $landingPages;

    /**
     * @var RuleCollection|null
     *
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    protected $rules;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getProducts(): ?ProductCollection
    {
        return $this->products;
    }

    public function setProducts(ProductCollection $products): void
    {
        $this->products = $products;
    }

    public function getMedia(): ?MediaCollection
    {
        return $this->media;
    }

    public function setMedia(MediaCollection $media): void
    {
        $this->media = $media;
    }

    public function getCategories(): ?CategoryCollection
    {
        return $this->categories;
    }

    public function setCategories(CategoryCollection $categories): void
    {
        $this->categories = $categories;
    }

    public function getCustomers(): ?CustomerCollection
    {
        return $this->customers;
    }

    public function setCustomers(CustomerCollection $customers): void
    {
        $this->customers = $customers;
    }

    public function getOrders(): ?OrderCollection
    {
        return $this->orders;
    }

    public function setOrders(OrderCollection $orders): void
    {
        $this->orders = $orders;
    }

    public function getShippingMethods(): ?ShippingMethodCollection
    {
        return $this->shippingMethods;
    }

    public function setShippingMethods(ShippingMethodCollection $shippingMethods): void
    {
        $this->shippingMethods = $shippingMethods;
    }

    public function getNewsletterRecipients(): ?NewsletterRecipientCollection
    {
        return $this->newsletterRecipients;
    }

    public function setNewsletterRecipients(NewsletterRecipientCollection $newsletterRecipients): void
    {
        $this->newsletterRecipients = $newsletterRecipients;
    }

    public function getLandingPages(): ?LandingPageCollection
    {
        return $this->landingPages;
    }

    public function setLandingPages(LandingPageCollection $landingPages): void
    {
        $this->landingPages = $landingPages;
    }

    public function getRules(): ?RuleCollection
    {
        return $this->rules;
    }

    public function setRules(RuleCollection $rules): void
    {
        $this->rules = $rules;
    }
}
