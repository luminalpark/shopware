<?php declare(strict_types=1);

namespace Shopware\Tests\Unit\Core\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\App\Payload\Source;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Service\AuthenticatedServiceClient;
use Shopware\Core\Service\ServiceException;
use Shopware\Core\Service\ServiceRegistryEntry;

/**
 * @internal
 */
#[CoversClass(AuthenticatedServiceClient::class)]
#[Package('framework')]
class AuthenticatedServiceClientTest extends TestCase
{
    private Client $client;

    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $this->client = new Client(['handler' => HandlerStack::create($this->mockHandler)]);
    }

    public function testSyncLicenseServicesSendsPostRequestWithCorrectPayload(): void
    {
        $this->mockHandler->append(new GuzzleResponse(200, []));

        $entry = new ServiceRegistryEntry('serviceA', 'description', 'https://example.com', 'appEndpoint', true, 'https://example.com/sync');
        $id = Uuid::randomHex();
        $source = new Source(
            'http:foo',
            $id,
            '1.0.1',
        );
        $serviceAuthedClient = new AuthenticatedServiceClient($this->client, $entry, $source);
        $serviceAuthedClient->syncLicense('license_key', 'license_host');

        $lastRequest = $this->mockHandler->getLastRequest();
        static::assertNotNull($lastRequest);
        static::assertSame('POST', $lastRequest->getMethod());
        static::assertSame('https://example.com/sync', (string) $lastRequest->getUri());
        static::assertSame('{"source":{"url":"http:foo","shopId":"' . $id . '","appVersion":"1.0.1","inAppPurchases":null},"licenseKey":"license_key","licenseHost":"license_host"}', (string) $lastRequest->getBody());
    }

    public function testSyncLicenseServicesSendsPostRequestWithLicenseHostNull(): void
    {
        $this->mockHandler->append(new GuzzleResponse(200, []));

        $entry = new ServiceRegistryEntry('serviceA', 'description', 'https://example.com', 'appEndpoint', true, 'https://example.com/sync');
        $id = Uuid::randomHex();
        $source = new Source(
            'http:foo',
            $id,
            '1.0.1',
        );
        $serviceAuthedClient = new AuthenticatedServiceClient($this->client, $entry, $source);
        $serviceAuthedClient->syncLicense('license_key');

        $lastRequest = $this->mockHandler->getLastRequest();
        static::assertNotNull($lastRequest);
        static::assertSame('POST', $lastRequest->getMethod());
        static::assertSame('https://example.com/sync', (string) $lastRequest->getUri());
        static::assertSame('{"source":{"url":"http:foo","shopId":"' . $id . '","appVersion":"1.0.1","inAppPurchases":null},"licenseKey":"license_key","licenseHost":""}', (string) $lastRequest->getBody());
    }

    public function testSyncLicenseServicesSendsPostRequestWithLicenseKeyEmpty(): void
    {
        $this->mockHandler->append(new GuzzleResponse(200, []));

        $entry = new ServiceRegistryEntry('serviceA', 'description', 'https://example.com', 'appEndpoint', true, 'https://example.com/sync');
        $id = Uuid::randomHex();
        $source = new Source(
            'http:foo',
            $id,
            '1.0.1',
        );
        $serviceAuthedClient = new AuthenticatedServiceClient($this->client, $entry, $source);
        $serviceAuthedClient->syncLicense('', 'license_host');

        $lastRequest = $this->mockHandler->getLastRequest();
        static::assertNotNull($lastRequest);
        static::assertSame('POST', $lastRequest->getMethod());
        static::assertSame('https://example.com/sync', (string) $lastRequest->getUri());
        static::assertSame('{"source":{"url":"http:foo","shopId":"' . $id . '","appVersion":"1.0.1","inAppPurchases":null},"licenseKey":"","licenseHost":"license_host"}', (string) $lastRequest->getBody());
    }

    public function testSyncLicenseServicesDoesNotSendRequestWhenKeyAndHostIsEmpty(): void
    {
        $this->mockHandler->append(new GuzzleResponse(200, []));

        $entry = new ServiceRegistryEntry('serviceA', 'description', 'https://example.com', 'appEndpoint', true, 'https://example.com/sync');
        $id = Uuid::randomHex();
        $source = new Source(
            'http:foo',
            $id,
            '1.0.1',
        );

        $serviceAuthedClient = new AuthenticatedServiceClient($this->client, $entry, $source);
        $serviceAuthedClient->syncLicense();

        $lastRequest = $this->mockHandler->getLastRequest();
        static::assertNotNull($lastRequest);
        static::assertSame('POST', $lastRequest->getMethod());
        static::assertSame('https://example.com/sync', (string) $lastRequest->getUri());
        static::assertSame('{"source":{"url":"http:foo","shopId":"' . $id . '","appVersion":"1.0.1","inAppPurchases":null},"licenseKey":"","licenseHost":""}', (string) $lastRequest->getBody());
    }

    public function testSyncLicenseServicesDoesNotSendRequestWhenLicenseSyncEndPointIsNull(): void
    {
        $this->mockHandler->append(new GuzzleResponse(200, []));

        $entry = new ServiceRegistryEntry('serviceA', 'description', 'https://example.com', 'appEndpoint', true, null);
        $source = new Source(
            'http:foo',
            Uuid::randomHex(),
            '1.0.1',
        );
        $serviceAuthedClient = new AuthenticatedServiceClient($this->client, $entry, $source);

        $serviceAuthedClient->syncLicense('license_key');

        static::assertNull($this->mockHandler->getLastRequest());
    }

    public function testSyncLicenseServicesThrowsServiceExceptionOnRequestError(): void
    {
        $this->mockHandler->append(new \Exception('Request error'));

        $entry = new ServiceRegistryEntry('serviceA', 'description', 'https://example.com', 'appEndpoint', true, 'https://example.com/sync');
        $source = new Source(
            'http:foo',
            Uuid::randomHex(),
            '1.0.1',
        );
        $serviceAuthedClient = new AuthenticatedServiceClient($this->client, $entry, $source);

        $this->expectException(ServiceException::class);
        $serviceAuthedClient->syncLicense('license_key');
    }
}
