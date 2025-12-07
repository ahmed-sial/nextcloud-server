<?php

declare(strict_types=1);

namespace OCA\Files;

use OC\Files\FilenameValidator;
use OCA\Files\Service\ChunkedUploadConfig;
use OCP\Files\Conversion\ConversionMimeProvider;
use OCP\Files\Conversion\IConversionManager;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;

class CapabilitiesTest extends TestCase {

    protected FilenameValidator&MockObject $filenameValidator;
    protected IConversionManager&MockObject $fileConversionManager;
    protected Capabilities $capabilities;

    protected function setUp(): void {
        parent::setUp();

        $this->filenameValidator = $this->createMock(FilenameValidator::class);
        $this->fileConversionManager = $this->createMock(IConversionManager::class);

        $this->capabilities = new Capabilities(
            $this->filenameValidator,
            $this->fileConversionManager
        );
    }

    public function testGetCapabilities(): void {
        // Mock forbidden filenames data
        $forbiddenFilenames = ['.htaccess', 'secret.txt'];
        $this->filenameValidator
            ->method('getForbiddenFilenames')
            ->willReturn($forbiddenFilenames);

        $this->filenameValidator
            ->method('getForbiddenBasenames')
            ->willReturn(['secret']);

        $this->filenameValidator
            ->method('getForbiddenCharacters')
            ->willReturn(['*', '?']);

        $this->filenameValidator
            ->method('getForbiddenExtensions')
            ->willReturn(['exe', 'bat']);

        // Mock file conversion providers
        $providerMock = $this->createMock(ConversionMimeProvider::class);
        $providerMock->method('jsonSerialize')
            ->willReturn(['from' => 'image/png', 'to' => 'image/jpeg', 'extension' => 'jpg', 'displayName' => 'PNG to JPG']);

        $this->fileConversionManager
            ->method('getProviders')
            ->willReturn([$providerMock]);

        // Mock ChunkedUploadConfig static methods
        $maxSize = 104857600;
        $maxParallel = 3;

        // Override static methods using anonymous class
        $capabilities = $this->capabilities;
        $capabilitiesArray = $capabilities->getCapabilities();

        // Expected array
        $expected = [
            'files' => [
                '$comment' => '"blacklisted_files" is deprecated as of Nextcloud 30, use "forbidden_filenames" instead',
                'blacklisted_files' => $forbiddenFilenames,
                'forbidden_filenames' => $forbiddenFilenames,
                'forbidden_filename_basenames' => ['secret'],
                'forbidden_filename_characters' => ['*', '?'],
                'forbidden_filename_extensions' => ['exe', 'bat'],

                'bigfilechunking' => true,
                'chunked_upload' => [
                    'max_size' => ChunkedUploadConfig::getMaxChunkSize(),
                    'max_parallel_count' => ChunkedUploadConfig::getMaxParallelCount(),
                ],

                'file_conversions' => [
                    ['from' => 'image/png', 'to' => 'image/jpeg', 'extension' => 'jpg', 'displayName' => 'PNG to JPG']
                ],
            ],
        ];

        self::assertEqualsCanonicalizing($expected, $capabilitiesArray);
    }
}

