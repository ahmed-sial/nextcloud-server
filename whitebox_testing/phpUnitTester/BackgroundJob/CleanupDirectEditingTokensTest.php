<?php

declare(strict_types=1);

namespace OCA\Files\Tests\BackgroundJob;

use OCA\Files\BackgroundJob\CleanupDirectEditingTokens;
use OCP\BackgroundJob\TimedJob;
use OCP\DirectEditing\IManager;
use OCP\AppFramework\Utility\ITimeFactory;
use PHPUnit\Framework\TestCase;

class CleanupDirectEditingTokensTest extends TestCase {
    public function testRunCallsCleanup(): void {
        // Mock the ITimeFactory (not used in run)
        $timeFactory = $this->createMock(ITimeFactory::class);

        // Mock the IManager
        $manager = $this->createMock(IManager::class);

        // Expect cleanup() to be called exactly once
        $manager->expects($this->once())
                ->method('cleanup');

        // Instantiate the background job
        $job = new CleanupDirectEditingTokens($timeFactory, $manager);

        // Run the job
        $job->run([]);
    }

    public function testJobIsTimedJob(): void {
        $timeFactory = $this->createMock(ITimeFactory::class);
        $manager = $this->createMock(IManager::class);

        $job = new CleanupDirectEditingTokens($timeFactory, $manager);

        $this->assertInstanceOf(TimedJob::class, $job);
    }
}

