<?php
declare(strict_types=1);

namespace ModThree\Tests;

use InvalidArgumentException;
use ModThree\ModThree;
use PHPUnit\Framework\TestCase;

final class ModThreeTest extends TestCase
{
    public function testProvidedExamples(): void
    {
        $this->assertSame(1, ModThree::modThree('1101')); // 13 % 3 = 1
        $this->assertSame(2, ModThree::modThree('1110')); // 14 % 3 = 2
        $this->assertSame(0, ModThree::modThree('1111')); // 15 % 3 = 0
    }

    public function testEdgeCases(): void
    {
        $this->assertSame(0, ModThree::modThree(''));     // empty => 0
        $this->assertSame(0, ModThree::modThree('0'));
        $this->assertSame(1, ModThree::modThree('1'));
        $this->assertSame(2, ModThree::modThree('10'));   // 2
        $this->assertSame(0, ModThree::modThree('11'));   // 3
        $this->assertSame(1, ModThree::modThree('100'));  // 4
        $this->assertSame(0, ModThree::modThree(str_repeat('1', 10000))); // 10000 ones, even => S0 => 0
    }

    public function testInvalidInputThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ModThree::modThree('10102');
    }
}
