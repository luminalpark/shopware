<?php declare(strict_types=1);

namespace Shopware\Tests\Unit\Core\Content\Flow\Dispatching\Action;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupEntity;
use Shopware\Core\Content\Flow\Dispatching\Action\SetCustomerGroupCustomFieldAction;
use Shopware\Core\Content\Flow\Dispatching\StorableFlow;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Event\CustomerGroupAware;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @package business-ops
 *
 * @internal
 *
 * @covers \Shopware\Core\Content\Flow\Dispatching\Action\SetCustomerGroupCustomFieldAction
 */
class SetCustomerGroupCustomFieldActionTest extends TestCase
{
    private Connection&MockObject $connection;

    private MockObject&EntityRepository $repository;

    private MockObject&EntitySearchResult $entitySearchResult;

    private SetCustomerGroupCustomFieldAction $action;

    private MockObject&StorableFlow $flow;

    public function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->entitySearchResult = $this->createMock(EntitySearchResult::class);

        $this->action = new SetCustomerGroupCustomFieldAction($this->connection, $this->repository);

        $this->flow = $this->createMock(StorableFlow::class);
    }

    public function testRequirements(): void
    {
        static::assertSame(
            [CustomerGroupAware::class],
            $this->action->requirements()
        );
    }

    public function testName(): void
    {
        static::assertSame('action.set.customer.group.custom.field', SetCustomerGroupCustomFieldAction::getName());
    }

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $existsData
     * @param array<string, mixed> $expected
     *
     * @dataProvider actionExecutedProvider
     */
    public function testExecutedAction(array $config, array $existsData, array $expected): void
    {
        $customerGroup = new CustomerGroupEntity();
        $customerGroup->setCustomFields($existsData);

        $this->flow->expects(static::exactly(2))->method('getData')->willReturn(Uuid::randomHex());
        $this->flow->expects(static::once())->method('hasData')->willReturn(true);
        $this->flow->expects(static::once())->method('getConfig')->willReturn($config);

        $customerGroupId = $this->flow->getData('customerGroupId');

        $this->entitySearchResult->expects(static::once())
            ->method('first')
            ->willReturn($customerGroup);

        $this->repository->expects(static::once())
            ->method('search')
            ->willReturn($this->entitySearchResult);

        $this->connection->expects(static::once())
            ->method('fetchOne')
            ->willReturn('custom_field_test');

        $this->repository->expects(static::once())
            ->method('update')
            ->with([['id' => $customerGroupId, 'customFields' => $expected['custom_field_test'] ? $expected : null]]);

        $this->action->handleFlow($this->flow);
    }

    public function testActionWithNotAware(): void
    {
        $this->flow->expects(static::once())->method('hasData')->willReturn(false);
        $this->flow->expects(static::never())->method('getData');
        $this->repository->expects(static::never())->method('update');

        $this->action->handleFlow($this->flow);
    }

    public static function actionExecutedProvider(): \Generator
    {
        yield 'Test aware with upsert config' => [
            [
                'entity' => 'customer_group',
                'customFieldId' => Uuid::randomHex(),
                'customFieldText' => 'custom_field_test',
                'customFieldValue' => ['blue', 'gray'],
                'customFieldSetId' => null,
                'customFieldSetText' => null,
                'option' => 'upsert',
            ],
            [
                'custom_field_test' => ['red', 'green'],
            ],
            [
                'custom_field_test' => ['blue', 'gray'],
            ],
        ];

        yield 'Test aware with create config' => [
            [
                'entity' => 'customer_group',
                'customFieldId' => Uuid::randomHex(),
                'customFieldText' => null,
                'customFieldValue' => ['blue', 'gray'],
                'customFieldSetId' => null,
                'customFieldSetText' => null,
                'option' => 'create',
            ],
            [
                'test' => ['red', 'green'],
            ],
            [
                'test' => ['red', 'green'],
                'custom_field_test' => ['blue', 'gray'],
            ],
        ];

        yield 'Test aware with clear config' => [
            [
                'entity' => 'customer_group',
                'customFieldId' => Uuid::randomHex(),
                'customFieldText' => 'custom_field_test',
                'customFieldValue' => null,
                'customFieldSetId' => null,
                'customFieldSetText' => null,
                'option' => 'clear',
            ],
            [
                'custom_field_test' => ['red', 'green', 'blue'],
            ],
            [
                'custom_field_test' => null,
            ],
        ];

        yield 'Test aware with add config' => [
            [
                'entity' => 'customer_group',
                'customFieldId' => Uuid::randomHex(),
                'customFieldText' => 'custom_field_test',
                'customFieldValue' => ['blue', 'gray'],
                'customFieldSetId' => null,
                'customFieldSetText' => null,
                'option' => 'add',
            ],
            [
                'custom_field_test' => ['red', 'green'],
            ],
            [
                'custom_field_test' => ['red', 'green', 'blue', 'gray'],
            ],
        ];

        yield 'Test aware with remove config' => [
            [
                'entity' => 'customer_group',
                'customFieldId' => Uuid::randomHex(),
                'customFieldText' => 'custom_field_test',
                'customFieldValue' => ['green', 'blue'],
                'customFieldSetId' => null,
                'customFieldSetText' => null,
                'option' => 'remove',
            ],
            [
                'custom_field_test' => ['red', 'green', 'blue'],
            ],
            [
                'custom_field_test' => ['red'],
            ],
        ];
    }
}
