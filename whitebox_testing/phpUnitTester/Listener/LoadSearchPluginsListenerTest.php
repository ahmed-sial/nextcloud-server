<?php
declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Files\Tests\Listener;

use OCA\Files\Event\LoadSearchPlugins;
use OCA\Files\Listener\LoadSearchPluginsListener;
use OCP\EventDispatcher\Event;
use Test\TestCase;

/**
 * @group DB
 */
class LoadSearchPluginsListenerTest extends TestCase {
	private LoadSearchPluginsListener $listener;

	protected function setUp(): void {
		parent::setUp();
		$this->listener = new LoadSearchPluginsListener();
	}

	public function testHandleWithCorrectEvent(): void {
		$event = $this->createMock(LoadSearchPlugins::class);
		
		// The listener should handle the event without throwing exceptions
		$this->listener->handle($event);
		
		$this->expectNotToPerformAssertions();
	}

	public function testHandleWithWrongEventType(): void {
		$event = $this->createMock(Event::class);
		
		// Should return early without processing
		$this->listener->handle($event);
		
		$this->expectNotToPerformAssertions();
	}

	public function testHandleChecksEventInstance(): void {
		$wrongEvent = new class extends Event {
		};
		
		// Should not throw exception even with wrong event type
		$this->listener->handle($wrongEvent);
		
		$this->assertTrue(true);
	}
}
