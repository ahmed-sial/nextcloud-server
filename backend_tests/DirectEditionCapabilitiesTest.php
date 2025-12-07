<?php

declare(strict_types=1);

namespace OCA\Files;

use OCA\Files\Service\DirectEditingService;
use OCP\Capabilities\ICapability;
use OCP\Capabilities\IInitialStateExcludedCapability;
use OCP\IURLGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;

class DirectEditingCapabilitiesTest extends TestCase {

    protected DirectEditingService&MockObject $directEditingService;
    protected IURLGenerator&MockObject $urlGenerator;
    protected DirectEditingCapabilities $capabilities;

    protected function setUp(): void {
        parent::setUp();

        $this->directEditingService = $this->createMock(DirectEditingService::class);
        $this->urlGenerator = $this->createMock(IURLGenerator::class);

        $this->capabilities = new DirectEditingCapabilities(
            $this->directEditingService,
            $this->urlGenerator
        );
    }

    public function testGetCapabilities(): void {
        $etag = '12345etag';
        $url = 'https://nextcloud.example.com/ocs/v2.php/apps/files.DirectEditing/info';

        // Mock the dependencies
        $this->directEditingService
            ->expects(self::once())
            ->method('getDirectEditingETag')
            ->willReturn($etag);

        $this->urlGenerator
            ->expects(self::once())
            ->method('linkToOCSRouteAbsolute')
            ->with('files.DirectEditing.info')
            ->willReturn($url);

        $expected = [
            'files' => [
                'directEditing' => [
                    'url' => $url,
                    'etag' => $etag,
                    'supportsFileId' => true,
                ]
            ],
        ];

        self::assertEqualsCanonicalizing($expected, $this->capabilities->getCapabilities());
    }
}

