<?php

declare(strict_types=1);

namespace OCA\Files\Tests\Service;

use OCA\Files\Service\DirectEditingService;
use OCP\DirectEditing\ACreateEmpty;
use OCP\DirectEditing\ACreateFromTemplate;
use OCP\DirectEditing\IEditor;
use OCP\DirectEditing\IManager;
use OCP\DirectEditing\RegisterDirectEditorEvent;
use OCP\EventDispatcher\IEventDispatcher;
use PHPUnit\Framework\TestCase;

class DirectEditingServiceTest extends TestCase {
    private $eventDispatcher;
    private $directEditingManager;
    private DirectEditingService $service;

    protected function setUp(): void {
        $this->eventDispatcher = $this->createMock(IEventDispatcher::class);
        $this->directEditingManager = $this->createMock(IManager::class);

        $this->service = new DirectEditingService(
            $this->eventDispatcher,
            $this->directEditingManager
        );
    }

    public function testGetDirectEditingETagCallsCapabilities(): void {
        $this->eventDispatcher->expects($this->once())
            ->method('dispatchTyped')
            ->with($this->isInstanceOf(RegisterDirectEditorEvent::class));

        $this->directEditingManager->method('isEnabled')->willReturn(false);

        $etag = $this->service->getDirectEditingETag();
        $this->assertIsString($etag);
        $this->assertEquals(\md5(\json_encode(['editors' => [], 'creators' => []])), $etag);
    }

    public function testGetDirectEditingCapabilitiesWithDisabledManager(): void {
        $this->directEditingManager->method('isEnabled')->willReturn(false);

        $capabilities = $this->service->getDirectEditingCapabilitites();
        $this->assertEquals(['editors' => [], 'creators' => []], $capabilities);
    }

    public function testGetDirectEditingCapabilitiesWithEditorsAndCreators(): void {
        $editor = $this->createMock(IEditor::class);
        $editor->method('getId')->willReturn('editor1');
        $editor->method('getName')->willReturn('Editor 1');
        $editor->method('getMimetypes')->willReturn(['text/plain']);
        $editor->method('getMimetypesOptional')->willReturn(['text/markdown']);
        $editor->method('isSecure')->willReturn(true);

        $creator1 = $this->createMock(ACreateEmpty::class);
        $creator1->method('getId')->willReturn('creator1');
        $creator1->method('getName')->willReturn('Creator 1');
        $creator1->method('getExtension')->willReturn('txt');
        $creator1->method('getMimetype')->willReturn('text/plain');

        $creator2 = $this->createMock(ACreateFromTemplate::class);
        $creator2->method('getId')->willReturn('creator2');
        $creator2->method('getName')->willReturn('Creator 2');
        $creator2->method('getExtension')->willReturn('md');
        $creator2->method('getMimetype')->willReturn('text/markdown');

        $editor->method('getCreators')->willReturn([$creator1, $creator2]);

        $this->directEditingManager->method('isEnabled')->willReturn(true);
        $this->directEditingManager->method('getEditors')->willReturn(['editor1' => $editor]);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatchTyped')
            ->with($this->isInstanceOf(RegisterDirectEditorEvent::class));

        $capabilities = $this->service->getDirectEditingCapabilitites();

        $this->assertArrayHasKey('editors', $capabilities);
        $this->assertArrayHasKey('creators', $capabilities);

        $this->assertArrayHasKey('editor1', $capabilities['editors']);
        $this->assertEquals('Editor 1', $capabilities['editors']['editor1']['name']);

        $this->assertArrayHasKey('creator1', $capabilities['creators']);
        $this->assertArrayHasKey('creator2', $capabilities['creators']);
        $this->assertFalse($capabilities['creators']['creator1']['templates']);
        $this->assertTrue($capabilities['creators']['creator2']['templates']);
    }
}

