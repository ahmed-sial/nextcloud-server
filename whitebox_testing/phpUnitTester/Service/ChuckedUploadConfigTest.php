<?php

declare(strict_types=1);

namespace OCA\Files\Tests\Service;

use OCA\Files\Service\ChunkedUploadConfig;
use OCP\IConfig;
use OCP\Server;
use PHPUnit\Framework\TestCase;

class ChunkedUploadConfigTest extends TestCase {
    private $configMock;

    protected function setUp(): void {
        $this->configMock = $this->createMock(IConfig::class);

        // Replace the Server singleton to return our mock
        Server::registerService(IConfig::class, fn() => $this->configMock);
    }

    public function testGetMaxChunkSizeReturnsConfiguredValue(): void {
        $this->configMock->expects($this->once())
            ->method('getSystemValueInt')
            ->with('files.chunked_upload.max_size', 100 * 1024 * 1024)
            ->willReturn(50 * 1024 * 1024);

        $this->assertEquals(50 * 1024 * 1024, ChunkedUploadConfig::getMaxChunkSize());
    }

    public function testGetMaxChunkSizeReturnsDefaultIfNotSet(): void {
        $this->configMock->expects($this->once())
            ->method('getSystemValueInt')
            ->with('files.chunked_upload.max_size', 100 * 1024 * 1024)
            ->willReturn(100 * 1024 * 1024);

        $this->assertEquals(100 * 1024 * 1024, ChunkedUploadConfig::getMaxChunkSize());
    }

    public function testSetMaxChunkSizeCallsSetSystemValue(): void {
        $this->configMock->expects($this->once())
            ->method('setSystemValue')
            ->with('files.chunked_upload.max_size', 75 * 1024 * 1024);

        ChunkedUploadConfig::setMaxChunkSize(75 * 1024 * 1024);
    }

    public function testGetMaxParallelCountReturnsConfiguredValue(): void {
        $this->configMock->expects($this->once())
            ->method('getSystemValueInt')
            ->with('files.chunked_upload.max_parallel_count', 5)
            ->willReturn(10);

        $this->assertEquals(10, ChunkedUploadConfig::getMaxParallelCount());
    }

    public function testGetMaxParallelCountReturnsDefaultIfNotSet(): void {
        $this->configMock->expects($this->once())
            ->method('getSystemValueInt')
            ->with('files.chunked_upload.max_parallel_count', 5)
            ->willReturn(5);

        $this->assertEquals(5, ChunkedUploadConfig::getMaxParallelCount());
    }
}

