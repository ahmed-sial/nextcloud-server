<?php

declare(strict_types=1);

namespace OCA\Files;

use OCP\Config\Lexicon\Strictness;
use OCP\Config\ValueType;
use PHPUnit\Framework\TestCase;

class ConfigLexiconTest extends TestCase {

    protected ConfigLexicon $lexicon;

    protected function setUp(): void {
        parent::setUp();
        $this->lexicon = new ConfigLexicon();
    }

    public function testGetStrictness(): void {
        $strictness = $this->lexicon->getStrictness();
        $this->assertSame(Strictness::NOTICE, $strictness);
    }

    public function testGetAppConfigs(): void {
        $configs = $this->lexicon->getAppConfigs();

        $this->assertIsArray($configs);
        $this->assertCount(1, $configs);

        $entry = $configs[0];
        $this->assertInstanceOf(\OCP\Config\Lexicon\Entry::class, $entry);
        $this->assertSame(ConfigLexicon::OVERWRITES_HOME_FOLDERS, $entry->getKey());
        $this->assertSame(ValueType::ARRAY, $entry->getType());
        $this->assertSame([], $entry->getDefaultRaw());
        $this->assertStringContainsString('home folders', $entry->getDefinition());
    }

    public function testGetUserConfigs(): void {
        $userConfigs = $this->lexicon->getUserConfigs();
        $this->assertIsArray($userConfigs);
        $this->assertEmpty($userConfigs);
    }
}

