<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2023 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Files\Tests\Listener;

use OC\Files\Node\NonExistingFile;
use OC\Files\Node\NonExistingFolder;
use OC\Files\View;
use OC\FilesMetadata\Model\FilesMetadata;
use OCA\Files\Listener\SyncLivePhotosListener;
use OCA\Files\Service\LivePhotosService;
use OCP\EventDispatcher\Event;
use OCP\Exceptions\AbortedEventException;
use OCP\Files\Cache\CacheEntryRemovedEvent;
use OCP\Files\Events\Node\BeforeNodeCopiedEvent;
use OCP\Files\Events\Node\BeforeNodeDeletedEvent;
use OCP\Files\Events\Node\BeforeNodeRenamedEvent;
use OCP\Files\Events\Node\NodeCopiedEvent;
use OCP\Files\File;
use OCP\Files\Folder;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\Files\Storage\IStorage;
use OCP\FilesMetadata\IFilesMetadataManager;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;

class SyncLivePhotosListenerTest extends TestCase {
	/** @var Folder|MockObject */
	private $userFolder;
	
	/** @var IFilesMetadataManager|MockObject */
	private $filesMetadataManager;
	
	/** @var LivePhotosService|MockObject */
	private $livePhotosService;
	
	/** @var IRootFolder|MockObject */
	private $rootFolder;
	
	/** @var View|MockObject */
	private $view;
	
	private SyncLivePhotosListener $listener;

	protected function setUp(): void {
		parent::setUp();
		
		$this->userFolder = $this->createMock(Folder::class);
		$this->filesMetadataManager = $this->createMock(IFilesMetadataManager::class);
		$this->livePhotosService = $this->createMock(LivePhotosService::class);
		$this->rootFolder = $this->createMock(IRootFolder::class);
		$this->view = $this->createMock(View::class);
		
		$this->listener = new SyncLivePhotosListener(
			$this->userFolder,
			$this->filesMetadataManager,
			$this->livePhotosService,
			$this->rootFolder,
			$this->view
		);
	}

	public function testHandleWithNullUserFolder(): void {
		$listener = new SyncLivePhotosListener(
			null,
			$this->filesMetadataManager,
			$this->livePhotosService,
			$this->rootFolder,
			$this->view
		);
		
		$event = $this->createMock(BeforeNodeRenamedEvent::class);
		
		// Should return early
		$listener->handle($event);
		
		$this->expectNotToPerformAssertions();
	}

	public function testHandleWithNonLivePhotoEvent(): void {
		$event = $this->createMock(Event::class);
		
		$this->livePhotosService->expects($this->never())
			->method('getLivePhotoPeerId');
		
		$this->listener->handle($event);
	}

	public function testHandleBeforeNodeRenamedWithNoPeer(): void {
		$sourceNode = $this->createMock(File::class);
		$sourceNode->method('getId')->willReturn(123);
		
		$targetNode = $this->createMock(File::class);
		
		$event = $this->createMock(BeforeNodeRenamedEvent::class);
		$event->method('getSource')->willReturn($sourceNode);
		$event->method('getTarget')->willReturn($targetNode);
		
		$this->livePhotosService->expects($this->once())
			->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(null);
		
		$this->userFolder->expects($this->never())
			->method('getFirstNodeById');
		
		$this->listener->handle($event);
	}

	public function testHandleBeforeNodeRenamedWithPeerNotFound(): void {
		$sourceNode = $this->createMock(File::class);
		$sourceNode->method('getId')->willReturn(123);
		
		$targetNode = $this->createMock(File::class);
		
		$event = $this->createMock(BeforeNodeRenamedEvent::class);
		$event->method('getSource')->willReturn($sourceNode);
		$event->method('getTarget')->willReturn($targetNode);
		
		$this->livePhotosService->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(456);
		
		$this->userFolder->expects($this->once())
			->method('getFirstNodeById')
			->with(456)
			->willReturn(null);
		
		$this->listener->handle($event);
	}

	public function testHandleBeforeNodeRenamedSuccess(): void {
		$sourceNode = $this->createMock(File::class);
		$sourceNode->method('getId')->willReturn(123);
		$sourceNode->method('getExtension')->willReturn('jpg');
		
		$targetParent = $this->createMock(Folder::class);
		$targetParent->method('getPath')->willReturn('/user/files/target');
		$targetParent->method('get')->willThrowException(new NotFoundException());
		
		$targetNode = $this->createMock(File::class);
		$targetNode->method('getName')->willReturn('photo.jpg');
		$targetNode->method('getParent')->willReturn($targetParent);
		
		$peerFile = $this->createMock(File::class);
		$peerFile->method('getId')->willReturn(456);
		$peerFile->method('getExtension')->willReturn('mov');
		$peerFile->expects($this->once())
			->method('move')
			->with('/user/files/target/photo.mov');
		
		$event = $this->createMock(BeforeNodeRenamedEvent::class);
		$event->method('getSource')->willReturn($sourceNode);
		$event->method('getTarget')->willReturn($targetNode);
		
		$this->livePhotosService->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(456);
		
		$this->userFolder->method('getFirstNodeById')
			->with(456)
			->willReturn($peerFile);
		
		$this->listener->handle($event);
	}

	public function testHandleBeforeNodeRenamedWithExtensionChange(): void {
		$this->expectException(AbortedEventException::class);
		$this->expectExceptionMessage('Cannot change the extension of a Live Photo');
		
		$sourceNode = $this->createMock(File::class);
		$sourceNode->method('getId')->willReturn(123);
		$sourceNode->method('getExtension')->willReturn('jpg');
		
		$targetParent = $this->createMock(Folder::class);
		
		$targetNode = $this->createMock(File::class);
		$targetNode->method('getName')->willReturn('photo.png');
		$targetNode->method('getParent')->willReturn($targetParent);
		
		$peerFile = $this->createMock(File::class);
		$peerFile->method('getId')->willReturn(456);
		$peerFile->method('getExtension')->willReturn('mov');
		
		$event = $this->createMock(BeforeNodeRenamedEvent::class);
		$event->method('getSource')->willReturn($sourceNode);
		$event->method('getTarget')->willReturn($targetNode);
		
		$this->livePhotosService->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(456);
		
		$this->userFolder->method('getFirstNodeById')
			->with(456)
			->willReturn($peerFile);
		
		$this->listener->handle($event);
	}

	public function testHandleBeforeNodeRenamedWithExistingTarget(): void {
		$this->expectException(AbortedEventException::class);
		$this->expectExceptionMessage('A file already exist at destination path of the Live Photo');
		
		$sourceNode = $this->createMock(File::class);
		$sourceNode->method('getId')->willReturn(123);
		$sourceNode->method('getExtension')->willReturn('jpg');
		
		$targetParent = $this->createMock(Folder::class);
		$targetParent->method('get')
			->with('photo.jpg')
			->willReturn($this->createMock(File::class));
		
		$targetNode = $this->createMock(File::class);
		$targetNode->method('getName')->willReturn('photo.jpg');
		$targetNode->method('getParent')->willReturn($targetParent);
		
		$peerFile = $this->createMock(File::class);
		$peerFile->method('getId')->willReturn(456);
		$peerFile->method('getExtension')->willReturn('mov');
		
		$event = $this->createMock(BeforeNodeRenamedEvent::class);
		$event->method('getSource')->willReturn($sourceNode);
		$event->method('getTarget')->willReturn($targetNode);
		
		$this->livePhotosService->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(456);
		
		$this->userFolder->method('getFirstNodeById')
			->with(456)
			->willReturn($peerFile);
		
		$this->listener->handle($event);
	}

	public function testHandleBeforeNodeDeletedWithVideoPart(): void {
		$this->expectException(AbortedEventException::class);
		$this->expectExceptionMessage('Cannot delete the video part of a live photo');
		
		$deletedNode = $this->createMock(File::class);
		$deletedNode->method('getId')->willReturn(123);
		$deletedNode->method('getMimetype')->willReturn('video/quicktime');
		
		$peerFile = $this->createMock(File::class);
		$peerFile->method('getId')->willReturn(456);
		
		$event = $this->createMock(BeforeNodeDeletedEvent::class);
		$event->method('getNode')->willReturn($deletedNode);
		
		$this->livePhotosService->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(456);
		
		$this->userFolder->method('getFirstNodeById')
			->with(456)
			->willReturn($peerFile);
		
		$this->listener->handle($event);
	}

	public function testHandleBeforeNodeDeletedWithImagePart(): void {
		$deletedNode = $this->createMock(File::class);
		$deletedNode->method('getId')->willReturn(123);
		$deletedNode->method('getMimetype')->willReturn('image/jpeg');
		
		$peerFile = $this->createMock(File::class);
		$peerFile->method('getId')->willReturn(456);
		$peerFile->expects($this->once())
			->method('delete');
		
		$event = $this->createMock(BeforeNodeDeletedEvent::class);
		$event->method('getNode')->willReturn($deletedNode);
		
		$this->livePhotosService->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(456);
		
		$this->userFolder->method('getFirstNodeById')
			->with(456)
			->willReturn($peerFile);
		
		$this->listener->handle($event);
	}

	public function testHandleCacheEntryRemovedEvent(): void {
		$peerFile = $this->createMock(File::class);
		$peerFile->expects($this->once())
			->method('delete');
		
		$event = $this->createMock(CacheEntryRemovedEvent::class);
		$event->method('getFileId')->willReturn(123);
		
		$this->livePhotosService->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(456);
		
		$this->userFolder->method('getFirstNodeById')
			->with(456)
			->willReturn($peerFile);
		
		$this->listener->handle($event);
	}

	public function testHandleNodeCopiedEventWithFiles(): void {
		$storage = $this->createMock(IStorage::class);
		$cache = $this->createMock(\OCP\Files\Cache\ICache::class);
		$cache->method('getNumericStorageId')->willReturn(1);
		$storage->method('getCache')->willReturn($cache);
		
		$sourceNode = $this->createMock(File::class);
		$sourceNode->method('getId')->willReturn(123);
		$sourceNode->method('getExtension')->willReturn('jpg');
		
		$targetParent = $this->createMock(Folder::class);
		$targetParent->method('getPath')->willReturn('/user/files/target');
		$targetParent->method('nodeExists')->with('photo.mov')->willReturn(false);
		
		$targetNode = $this->createMock(File::class);
		$targetNode->method('getId')->willReturn(789);
		$targetNode->method('getName')->willReturn('photo.jpg');
		$targetNode->method('getParent')->willReturn($targetParent);
		$targetNode->method('getStorage')->willReturn($storage);
		
		$peerFile = $this->createMock(File::class);
		$peerFile->method('getId')->willReturn(456);
		$peerFile->method('getExtension')->willReturn('mov');
		
		$targetPeerFile = $this->createMock(File::class);
		$targetPeerFile->method('getId')->willReturn(999);
		$targetPeerFile->method('getStorage')->willReturn($storage);
		
		$peerFile->method('copy')
			->with('/user/files/target/photo.mov')
			->willReturn($targetPeerFile);
		
		$targetMetadata = $this->createMock(FilesMetadata::class);
		$targetMetadata->expects($this->once())
			->method('setStorageId')
			->with(1);
		$targetMetadata->expects($this->once())
			->method('setString')
			->with('files-live-photo', '999');
		
		$peerMetadata = $this->createMock(FilesMetadata::class);
		$peerMetadata->expects($this->once())
			->method('setStorageId')
			->with(1);
		$peerMetadata->expects($this->once())
			->method('setString')
			->with('files-live-photo', '789');
		
		$this->filesMetadataManager->expects($this->exactly(2))
			->method('getMetadata')
			->willReturnOnConsecutiveCalls($targetMetadata, $peerMetadata);
		
		$this->filesMetadataManager->expects($this->exactly(2))
			->method('saveMetadata');
		
		$event = $this->createMock(NodeCopiedEvent::class);
		$event->method('getSource')->willReturn($sourceNode);
		$event->method('getTarget')->willReturn($targetNode);
		
		$this->livePhotosService->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(456);
		
		$this->userFolder->method('getFirstNodeById')
			->with(456)
			->willReturn($peerFile);
		
		$this->listener->handle($event);
	}

	public function testHandleBeforeNodeCopiedEventWithFolder(): void {
		$sourceChild = $this->createMock(File::class);
		$sourceChild->method('getId')->willReturn(123);
		$sourceChild->method('getExtension')->willReturn('jpg');
		
		$sourceFolder = $this->createMock(Folder::class);
		$sourceFolder->method('getDirectoryListing')->willReturn([$sourceChild]);
		
		$targetFolder = $this->createMock(Folder::class);
		$targetFolder->method('getPath')->willReturn('/user/files/target');
		
		$event = $this->createMock(BeforeNodeCopiedEvent::class);
		$event->method('getSource')->willReturn($sourceFolder);
		$event->method('getTarget')->willReturn($targetFolder);
		
		$this->livePhotosService->method('getLivePhotoPeerId')
			->with(123)
			->willReturn(null);
		
		$this->listener->handle($event);
	}

	public function testImplementsIEventListenerInterface(): void {
		$this->assertInstanceOf(
			\OCP\EventDispatcher\IEventListener::class,
			$this->listener,
			'SyncLivePhotosListener should implement IEventListener'
		);
	}
}
