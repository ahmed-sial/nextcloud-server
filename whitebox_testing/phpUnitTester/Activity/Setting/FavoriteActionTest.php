<?php

declare(strict_types=1);

namespace OCA\Files\Activity\Settings;

use PHPUnit\Framework\MockObject\MockObject;
use OCP\IL10N;
use Test\TestCase;

class FavoriteActionTest extends TestCase {

    protected FavoriteAction $action;
    protected IL10N&MockObject $l10n;

    protected function setUp(): void {
        parent::setUp();
        $this->l10n = $this->createMock(IL10N::class);
        $this->l10n->method('t')->willReturnArgument(0); // Return the translation key itself
        $this->action = new FavoriteAction($this->l10n);
    }

    public function testGetIdentifier(): void {
        $this->assertSame('favorite', $this->action->getIdentifier());
    }

    public function testGetName(): void {
        $name = 'A file has been added to or removed from your <strong>favorites</strong>';
        $this->assertSame($name, $this->action->getName());
    }

    public function testGetPriority(): void {
        $priority = $this->action->getPriority();
        $this->assertIsInt($priority);
        $this->assertGreaterThanOrEqual(0, $priority);
        $this->assertLessThanOrEqual(100, $priority);
        $this->assertSame(5, $priority);
    }

    public function testCanChangeStream(): void {
        $this->assertFalse($this->action->canChangeStream());
    }

    public function testIsDefaultEnabledStream(): void {
        $this->assertTrue($this->action->isDefaultEnabledStream());
    }

    public function testCanChangeMail(): void {
        $this->assertFalse($this->action->canChangeMail());
    }

    public function testIsDefaultEnabledMail(): void {
        $this->assertFalse($this->action->isDefaultEnabledMail());
    }

    public function testCanChangeNotification(): void {
        $this->assertFalse($this->action->canChangeNotification());
    }
}

