<?php

namespace Tests\Unit;

use App\Libraries\SlugGenerator;
use PHPUnit\Framework\TestCase;

final class SlugGeneratorTest extends TestCase
{
    public function testGeneratesReadableSlug(): void
    {
        $this->assertSame('uma-noticia-de-teste', SlugGenerator::generate('Uma notícia de teste'));
    }

    public function testRemovesUnsupportedCharacters(): void
    {
        $this->assertSame('pet-mais-fofo', SlugGenerator::generate('Pet @# mais fofo!!!'));
    }
}
