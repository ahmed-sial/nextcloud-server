<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2016 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
namespace OCA\Files\Tests\Activity\Settings;

use OCA\Files\Activity\Settings\FileFavoriteChanged;
use OCP\IL10N;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;

class FileFavoriteChangedTest extends TestCase {

	protected IL10N&MockObject $l;
	protected FileFavoriteChanged $setting;

	protected function setUp(): void {
		parent::setUp();
		$this->l = $this->createMock(IL10N::class);
		$this->setting = new FileFavoriteChanged($this->l);
	}

	public function testGetIdentifier(): void {
		$this->assertSame('file_favorite_changed', $this->setting->getIdentifier());
	}

	public function testGetName(): void {
		$this->l->method('t')->willReturn('mocked name');
		$this->assertSame('mocked name', $this->setting->getName());
	}

	public function testGetPriority(): void {
		$this->assertSame(1, $this->setting->getPriority());
	}

	public function testCanChangeStream(): void {
		$this->assertTrue($this->setting->canChangeStream());
	}

	public function testIsDefaultEnabledStream(): void {
		$this->assertTrue($this->setting->isDefaultEnabledStream());
	}

	public function testCanChangeMail(): void {
		$this->assertFalse($this->setting->canChangeMail());
	}

	public function testIsDefaultEnabledMail(): void {
		$this->assertFalse($this->setting->isDefaultEnabledMail());
	}

	public function testCanChangeNotification(): void {
		$this->assertFalse($this->setting->canChangeNotification());
	}

	public function testIsDefaultEnabledNotification(): void {
		$this->assertFalse($this->setting->isDefaultEnabledNotification());
	}
}

