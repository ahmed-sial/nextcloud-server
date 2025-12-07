<?php

declare(strict_types=1);

namespace OCA\Files\Activity;

use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\Node;
use OCP\ITagManager;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;

class HelperTest extends TestCase {

    protected Helper $helper;
    protected ITagManager&MockObject $tagManager;
    protected IRootFolder&MockObject $rootFolder;

    protected function setUp(): void {
        parent::setUp();
        $this->tagManager = $this->createMock(ITagManager::class);
        $this->rootFolder = $this->createMock(IRootFolder::class);
        $this->helper = new Helper($this->tagManager, $this->rootFolder);
    }

    public function testGetFavoriteNodesNoFavoritesThrows(): void {
        $tags = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getFavorites'])
            ->getMock();
        $tags->method('getFavorites')->willReturn([]);

        $this->tagManager->method('load')->willReturn($tags);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No favorites');

        $this->helper->getFavoriteNodes('user1');
    }

    public function testGetFavoriteNodesTooManyFavoritesThrows(): void {
        $tags = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getFavorites'])
            ->getMock();
        $favorites = array_fill(0, Helper::FAVORITE_LIMIT + 1, 'id');
        $tags->method('getFavorites')->willReturn($favorites);

        $this->tagManager->method('load')->willReturn($tags);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Too many favorites');

        $this->helper->getFavoriteNodes('user1');
    }

    public function testGetFavoriteNodesReturnsNodes(): void {
        $tags = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getFavorites'])
            ->getMock();
        $favorites = ['id1', 'id2'];
        $tags->method('getFavorites')->willReturn($favorites);

        $this->tagManager->method('load')->willReturn($tags);

        $userFolder = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getFirstNodeById'])
            ->getMock();

        $node1 = $this->createMock(Node::class);
        $node2 = $this->createMock(Folder::class);

        $userFolder->method('getFirstNodeById')
            ->willReturnMap([
                ['id1', $node1],
                ['id2', $node2],
            ]);

        $this->rootFolder->method('getUserFolder')->willReturn($userFolder);

        $result = $this->helper->getFavoriteNodes('user1');
        $this->assertCount(2, $result);
        $this->assertSame($node1, $result[0]);
        $this->assertSame($node2, $result[1]);
    }

    public function testGetFavoriteNodesFoldersOnly(): void {
        $tags = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getFavorites'])
            ->getMock();
        $tags->method('getFavorites')->willReturn(['id1', 'id2']);

        $this->tagManager->method('load')->willReturn($tags);

        $userFolder = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getFirstNodeById'])
            ->getMock();

        $node1 = $this->createMock(Node::class);
        $node2 = $this->createMock(Folder::class);

        $userFolder->method('getFirstNodeById')
            ->willReturnMap([
                ['id1', $node1],
                ['id2', $node2],
            ]);

        $this->rootFolder->method('getUserFolder')->willReturn($userFolder);

        $result = $this->helper->getFavoriteNodes('user1', true);
        $this->assertCount(1, $result);
        $this->assertSame($node2, $result[0]);
    }

    public function testGetFavoriteFilePaths(): void {
        $tags = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getFavorites'])
            ->getMock();
        $tags->method('getFavorites')->willReturn(['id1']);

        $this->tagManager->method('load')->willReturn($tags);

        $node = $this->createMock(Folder::class);
        $node->method('getPath')->willReturn('/home/file');

        $userFolder = $this->getMockBuilder(\stdClass::class)
            ->addMethods(['getFirstNodeById', 'getRelativePath'])
            ->getMock();
        $userFolder->method('getFirstNodeById')->willReturn($node);
        $userFolder->method('getRelativePath')->willReturnCallback(fn($path) => ltrim($path, '/'));

        $this->rootFolder->method('getUserFolder')->willReturn($userFolder);

        $result = $this->helper->getFavoriteFilePaths('user1');
        $this->assertEquals(['items' => ['home/file'], 'folders' => ['home/file']], $result);
    }
}

