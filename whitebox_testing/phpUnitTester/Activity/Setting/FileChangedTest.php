<?php

declare(strict_types=1);

namespace OCA\Files\Activity\Settings;

use PHPUnit\Framework\MockObject\MockObject;
use OCP\IL10N;
use Test\TestCase;

class FileChangedTest extends TestCase {

    protected IL10N&MockObject $l10n;
    protected FileChanged $fileChanged;

    protected function setUp(): void {
        parent::setUp();
        $this->l10n = $this->createMock(IL10N::class);
        $this->l10n->method('t')->willReturnArgument(0);

        $this->fileChanged = new FileChanged($this->l10n);
    }

    public function testGetIdentifier(): void {
        $this->assertSame('file_changed', $this->fileChanged->getIdentifier());
    }

    public function testGetName(): void {
        $this->assertSame('A file or folder has been <strong>changed</strong>', $this->fileChanged->getName());
    }

    public function testGetPriority(): void {
        $this->assertSame(2, $this->fileChanged->getPriority());
    }

    public function testCanChangeMail(): void {
        $this->assertTrue($this->fileChanged->canChangeMail());
    }

    public function testIsDefaultEnabledMail(): void {
        $this->assertFalse($this->fileChanged->isDefaultEnabledMail());
    }

    public function testCanChangeNotification(): void {
        $this->assertTrue($this->fileChanged->canChangeNotification());
    }

    public function testIsDefaultEnabledNotification(): void {
        $this->assertFalse($this->fileChanged->isDefaultEnabledNotification());
    }

    public function testGroupMethods(): void {
        $this->assertSame('files', $this->fileChanged->getGroupIdentifier());
        $this->assertSame('Files', $this->fileChanged->getGroupName());
    }
}

