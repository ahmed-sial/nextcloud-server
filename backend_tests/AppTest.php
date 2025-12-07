<?php

declare(strict_types=1);

namespace OCA\Files;

use OC\NavigationManager;
use OCA\Files\Service\ChunkedUploadConfig;
use OCP\App\IAppManager;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\IGroupManager;
use OCP\INavigationManager;
use OCP\IURLGenerator;
use OCP\IUserSession;
use OCP\L10N\IFactory;
use OCP\Server;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;
use Psr\Log\LoggerInterface;

class AppTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();

        // Reset the singleton for tests
        $reflection = new \ReflectionClass(App::class);
        $property = $reflection->getProperty('navigationManager');
        $property->setAccessible(true);
        $property->setValue(null);
    }

    public function testGetNavigationManager(): void {
        $navManager = App::getNavigationManager();

        self::assertInstanceOf(INavigationManager::class, $navManager);

        // Calling again should return the same instance
        $navManager2 = App::getNavigationManager();
        self::assertSame($navManager, $navManager2);
    }

    public function testExtendJsConfig(): void {
        $maxChunkSize = 123456;
        
        // Mock the static method ChunkedUploadConfig::getMaxChunkSize()
        $mock = $this->getMockForAbstractClass(ChunkedUploadConfig::class);
        $mockClass = new class($maxChunkSize) extends ChunkedUploadConfig {
            private int $size;
            public function __construct(int $size) { $this->size = $size; }
            public static function getMaxChunkSize(): int {
                return 123456; // fixed value for testing
            }
        };

        $settings = [
            'array' => [
                'oc_appconfig' => json_encode([])
            ]
        ];

        App::extendJsConfig($settings);

        $decoded = json_decode($settings['array']['oc_appconfig'], true);
        self::assertArrayHasKey('files', $decoded);
        self::assertEquals($maxChunkSize, $decoded['files']['max_chunk_size']);
    }
}

