<?php
declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Files\Tests\Event;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCP\EventDispatcher\Event;
use Test\TestCase;

class LoadAdditionalScriptsEventTest extends TestCase {
	private LoadAdditionalScriptsEvent $event;

	protected function setUp(): void {
		parent::setUp();
		$this->event = new LoadAdditionalScriptsEvent();
	}

	public function testEventCanBeInstantiated(): void {
		$event = new LoadAdditionalScriptsEvent();
		
		$this->assertInstanceOf(LoadAdditionalScriptsEvent::class, $event);
	}

	public function testEventExtendsBaseEvent(): void {
		$this->assertInstanceOf(Event::class, $this->event);
	}

	public function testEventHasNoConstructorParameters(): void {
		// Verify that the event can be created without any parameters
		$event = new LoadAdditionalScriptsEvent();
		
		$this->assertInstanceOf(LoadAdditionalScriptsEvent::class, $event);
		$this->expectNotToPerformAssertions();
	}

	public function testEventIsNotPropagationStopped(): void {
		$this->assertFalse($this->event->isPropagationStopped());
	}

	public function testEventCanStopPropagation(): void {
		$this->event->stopPropagation();
		
		$this->assertTrue($this->event->isPropagationStopped());
	}

	public function testMultipleInstancesAreIndependent(): void {
		$event1 = new LoadAdditionalScriptsEvent();
		$event2 = new LoadAdditionalScriptsEvent();
		
		$event1->stopPropagation();
		
		$this->assertTrue($event1->isPropagationStopped());
		$this->assertFalse($event2->isPropagationStopped());
	}
}
