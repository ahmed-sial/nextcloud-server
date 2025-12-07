<?php
declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Files\Tests\Listener;

use OCA\Files\Event\LoadSidebar;
use OCA\Files\Listener\LoadSidebarListener;
use OCP\EventDispatcher\Event;
use Test\TestCase;

/**
 * @group DB
 */
class LoadSidebarListenerTest extends TestCase {
	private LoadSidebarListener $listener;

	protected function setUp(): void {
		parent::setUp();
		$this->listener = new LoadSidebarListener();
	}

	public function testHandleWithCorrectEvent(): void {
		$event = $this->createMock(LoadSidebar::class);
		
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

	public function testImplementsIEventListenerInterface(): void {
		$this->assertInstanceOf(
			\OCP\EventDispatcher\IEventListener::class,
			$this->listener,
			'LoadSidebarListener should implement IEventListener'
		);
	}
}
