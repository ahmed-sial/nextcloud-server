<?php

declare(strict_types=1);

namespace OCA\Files\Activity\Filter;

use OCP\IL10N;
use OCP\IURLGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;

class FileChangesTest extends TestCase {

    protected FileChanges $filter;
    protected IL10N&MockObject $l;
    protected IURLGenerator&MockObject $url;

    protected function setUp(): void {
        parent::setUp();
        $this->l = $this->createMock(IL10N::class);
        $this->url = $this->createMock(IURLGenerator::class);

        $this->filter = new FileChanges($this->l, $this->url);
    }

    public function testGetIdentifier(): void {
        $this->assertSame('files', $this->filter->getIdentifier());
    }

    public function testGetName(): void {
        $this->l->method('t')->with('File changes')->willReturn('File changes');
        $this->assertSame('File changes', $this->filter->getName());
    }

    public function testGetPriority(): void {
        $this->assertSame(30, $this->filter->getPriority());
    }

    public function testGetIcon(): void {
        $this->url->method('imagePath')->with('core', 'places/files.svg')->willReturn('core/places/files.svg');
        $this->url->method('getAbsoluteURL')->with('core/places/files.svg')->willReturn('absolute_url');
        $this->assertSame('absolute_url', $this->filter->getIcon());
    }

    public function testFilterTypes(): void {
        $input = ['file_created', 'file_changed', 'other'];
        $expected = ['file_created', 'file_changed'];
        $this->assertSame($expected, $this->filter->filterTypes($input));
    }

    public function testAllowedApps(): void {
        $this->assertSame(['files'], $this->filter->allowedApps());
    }
}

