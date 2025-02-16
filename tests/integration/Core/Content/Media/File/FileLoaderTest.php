<?php declare(strict_types=1);

namespace Shopware\Tests\Integration\Core\Content\Media\File;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\File\FileFetcher;
use Shopware\Core\Content\Media\File\FileLoader;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Test\Media\MediaFixtures;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
final class FileLoaderTest extends TestCase
{
    use IntegrationTestBehaviour;
    use MediaFixtures;

    public const TEST_IMAGE = __DIR__ . '/../fixtures/shopware-logo.png';

    private FileLoader $fileLoader;

    private FileFetcher $fileFetcher;

    private FileSaver $fileSaver;

    private EntityRepository $mediaRepository;

    protected function setUp(): void
    {
        $this->fileLoader = static::getContainer()->get(FileLoader::class);
        $this->fileFetcher = static::getContainer()->get(FileFetcher::class);
        $this->fileSaver = static::getContainer()->get(FileSaver::class);
        $this->mediaRepository = static::getContainer()->get('media.repository');
    }

    public function testLoadMediaFile(): void
    {
        $context = Context::createDefaultContext();
        $blob = \file_get_contents(self::TEST_IMAGE);
        static::assertIsString($blob);
        $mediaFile = $this->fileFetcher->fetchBlob($blob, 'png', 'image/png');
        $mediaId = Uuid::randomHex();
        $this->mediaRepository->create([['id' => $mediaId]], $context);
        $this->fileSaver->persistFileToMedia($mediaFile, $mediaId . '.png', $mediaId, $context);
        $this->fileFetcher->cleanUpTempFile($mediaFile);

        static::assertSame($blob, $this->fileLoader->loadMediaFile($mediaId, $context));
        static::assertFileDoesNotExist($mediaFile->getFileName());

        $this->mediaRepository->delete([['id' => $mediaId]], $context);
    }

    public function testLoadMediaFileStream(): void
    {
        $context = Context::createDefaultContext();
        $blob = \file_get_contents(self::TEST_IMAGE);
        static::assertIsString($blob);
        $mediaFile = $this->fileFetcher->fetchBlob($blob, 'png', 'image/png');
        $mediaId = Uuid::randomHex();
        $this->mediaRepository->create([['id' => $mediaId]], $context);
        $this->fileSaver->persistFileToMedia($mediaFile, $mediaId . '.png', $mediaId, $context);
        $this->fileFetcher->cleanUpTempFile($mediaFile);

        static::assertSame($blob, (string) $this->fileLoader->loadMediaFileStream($mediaId, $context));
        static::assertFileDoesNotExist($mediaFile->getFileName());

        $this->mediaRepository->delete([['id' => $mediaId]], $context);
    }
}
