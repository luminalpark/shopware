<?php declare(strict_types=1);

namespace Shopware\Core\Framework;

use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Context\ContextSource;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\DataAbstractionLayer\Pricing\CashRoundingConfig;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Struct\StateAwareTrait;
use Shopware\Core\Framework\Struct\Struct;
use Symfony\Component\Serializer\Attribute\Ignore;

#[Package('core')]
class Context extends Struct
{
    use StateAwareTrait;

    final public const SYSTEM_SCOPE = 'system';
    final public const USER_SCOPE = 'user';
    final public const CRUD_API_SCOPE = 'crud';

    final public const SKIP_TRIGGER_FLOW = 'skipTriggerFlow';

    /**
     * @var non-empty-list<string>
     */
    protected array $languageIdChain;

    protected string $scope = self::USER_SCOPE;

    protected bool $rulesLocked = false;

    /**
     * @deprecated tag:v6.7.0 - Will be natively typed
     */
    #[Ignore]
    protected $extensions = [];

    /**
     * @param array<string> $languageIdChain
     * @param array<string> $ruleIds
     */
    public function __construct(
        protected ContextSource $source,
        protected array $ruleIds = [],
        protected string $currencyId = Defaults::CURRENCY,
        array $languageIdChain = [Defaults::LANGUAGE_SYSTEM],
        protected string $versionId = Defaults::LIVE_VERSION,
        protected float $currencyFactor = 1.0,
        protected bool $considerInheritance = false,
        /**
         * @see CartPrice::TAX_STATE_GROSS, CartPrice::TAX_STATE_NET, CartPrice::TAX_STATE_FREE
         */
        protected string $taxState = CartPrice::TAX_STATE_GROSS,
        protected CashRoundingConfig $rounding = new CashRoundingConfig(2, 0.01, true)
    ) {
        if ($source instanceof SystemSource) {
            $this->scope = self::SYSTEM_SCOPE;
        }

        $languageIdChain = array_values(array_filter($languageIdChain));
        if (empty($languageIdChain)) {
            throw FrameworkException::invalidArgumentException('Argument "languageIdChain" must not be empty');
        }

        $this->languageIdChain = $languageIdChain;
    }

    /**
     * @internal
     */
    public static function createDefaultContext(?ContextSource $source = null): self
    {
        $source ??= new SystemSource();

        return new self($source);
    }

    public static function createCLIContext(?ContextSource $source = null): self
    {
        return self::createDefaultContext($source);
    }

    public function getSource(): ContextSource
    {
        return $this->source;
    }

    public function getVersionId(): string
    {
        return $this->versionId;
    }

    public function getLanguageId(): string
    {
        return $this->languageIdChain[0];
    }

    public function getCurrencyId(): string
    {
        return $this->currencyId;
    }

    public function getCurrencyFactor(): float
    {
        return $this->currencyFactor;
    }

    /**
     * @return array<string>
     */
    public function getRuleIds(): array
    {
        return $this->ruleIds;
    }

    /**
     * @return non-empty-list<string>
     */
    public function getLanguageIdChain(): array
    {
        return $this->languageIdChain;
    }

    public function createWithVersionId(string $versionId): self
    {
        $context = new self(
            $this->source,
            $this->ruleIds,
            $this->currencyId,
            $this->languageIdChain,
            $versionId,
            $this->currencyFactor,
            $this->considerInheritance,
            $this->taxState,
            $this->rounding
        );
        $context->scope = $this->scope;

        foreach ($this->getExtensions() as $key => $extension) {
            $context->addExtension($key, $extension);
        }

        return $context;
    }

    /**
     * @deprecated tag:v6.7.0 - reason:return-type-change - Return type will be native
     *
     * @template TReturn of mixed
     *
     * @param \Closure(Context): TReturn $callback
     *
     * @return TReturn the return value of the provided callback function
     */
    public function scope(string $scope, \Closure $callback)
    {
        $currentScope = $this->getScope();
        $this->scope = $scope;

        try {
            $result = $callback($this);
        } finally {
            $this->scope = $currentScope;
        }

        return $result;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function considerInheritance(): bool
    {
        return $this->considerInheritance;
    }

    public function setConsiderInheritance(bool $considerInheritance): void
    {
        $this->considerInheritance = $considerInheritance;
    }

    public function getTaxState(): string
    {
        return $this->taxState;
    }

    public function setTaxState(string $taxState): void
    {
        $this->taxState = $taxState;
    }

    public function isAllowed(string $privilege): bool
    {
        if ($this->source instanceof AdminApiSource) {
            return $this->source->isAllowed($privilege);
        }

        return true;
    }

    /**
     * @param array<string> $ruleIds
     */
    public function setRuleIds(array $ruleIds): void
    {
        if ($this->rulesLocked) {
            throw FrameworkException::contextRulesLocked();
        }

        $this->ruleIds = array_filter(array_values($ruleIds));
    }

    /**
     * @deprecated tag:v6.7.0 - reason:return-type-change - Return type will be native
     *
     * @template TReturn of mixed
     *
     * @param \Closure(Context): TReturn $function
     *
     * @return TReturn
     */
    public function enableInheritance(\Closure $function)
    {
        $previous = $this->considerInheritance;
        $this->considerInheritance = true;
        $result = $function($this);
        $this->considerInheritance = $previous;

        return $result;
    }

    /**
     * @deprecated tag:v6.7.0 - reason:return-type-change - Return type will be native
     *
     * @template TReturn of mixed
     *
     * @param \Closure(Context): TReturn $function
     *
     * @return TReturn
     */
    public function disableInheritance(\Closure $function)
    {
        $previous = $this->considerInheritance;
        $this->considerInheritance = false;
        $result = $function($this);
        $this->considerInheritance = $previous;

        return $result;
    }

    public function getApiAlias(): string
    {
        return 'context';
    }

    public function getRounding(): CashRoundingConfig
    {
        return $this->rounding;
    }

    public function setRounding(CashRoundingConfig $rounding): void
    {
        $this->rounding = $rounding;
    }

    public function lockRules(): void
    {
        $this->rulesLocked = true;
    }
}
