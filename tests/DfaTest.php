<?php
declare(strict_types=1);

namespace ModThree\Tests;

use InvalidArgumentException;
use ModThree\Fsm\Dfa;
use PHPUnit\Framework\TestCase;

final class DfaTest extends TestCase
{
    public function testRunTransitionsCorrectly(): void
    {
        $dfa = new Dfa('S0', [
            'S0' => ['0' => 'S0', '1' => 'S1'],
            'S1' => ['0' => 'S2', '1' => 'S0'],
            'S2' => ['0' => 'S1', '1' => 'S2'],
        ], ['0','1']);

        $this->assertSame('S0', $dfa->run(''));       // no input
        $this->assertSame('S1', $dfa->run('1'));
        $this->assertSame('S0', $dfa->run('11'));
        $this->assertSame('S2', $dfa->run('10'));
    }

    public function testInvalidSymbolThrows(): void
    {
        $dfa = new Dfa('S0', [
            'S0' => ['0' => 'S0', '1' => 'S1'],
            'S1' => ['0' => 'S2', '1' => 'S0'],
            'S2' => ['0' => 'S1', '1' => 'S2'],
        ], ['0','1']);

        $this->expectException(InvalidArgumentException::class);
        $dfa->run('10x01');
    }

    public function testInitialStateMethod(): void
    {
        $dfa = new Dfa('S0', [
            'S0' => ['0' => 'S0', '1' => 'S1'],
            'S1' => ['0' => 'S2', '1' => 'S0'],
            'S2' => ['0' => 'S1', '1' => 'S2'],
        ], ['0','1']);

        $this->assertSame('S0', $dfa->initialState());
    }

    public function testStepMethodTransitions(): void
    {
        $dfa = new Dfa('S0', [
            'S0' => ['0' => 'S0', '1' => 'S1'],
            'S1' => ['0' => 'S2', '1' => 'S0'],
            'S2' => ['0' => 'S1', '1' => 'S2'],
        ], ['0','1']);

        $this->assertSame('S1', $dfa->step('S0', '1'));
        $this->assertSame('S0', $dfa->step('S1', '1'));
        $this->assertSame('S2', $dfa->step('S1', '0'));
    }

    public function testUndefinedStateThrows(): void
    {
        $dfa = new Dfa('S0', [
            'S0' => ['0' => 'S0', '1' => 'S1'],
            'S1' => ['0' => 'S2', '1' => 'S0'],
            'S2' => ['0' => 'S1', '1' => 'S2'],
        ], ['0','1']);

        $this->expectException(InvalidArgumentException::class);
        $dfa->step('UNDEFINED', '0');
    }

    public function testUndefinedTransitionThrows(): void
    {
        $dfa = new Dfa('S0', [
            'S0' => ['0' => 'S0', '1' => 'S1'],
            'S1' => ['0' => 'S2', '1' => 'S0'],
            'S2' => ['0' => 'S1', '1' => 'S2'],
        ], ['0','1']);

        $this->expectException(InvalidArgumentException::class);
        $dfa->step('S0', '2');
    }

    public function testEmptyInitialStateThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Initial state cannot be empty');
        new Dfa('', [
            'S0' => ['0' => 'S0', '1' => 'S1'],
        ], ['0','1']);
    }

    public function testEmptyTransitionsThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Transitions cannot be empty');
        new Dfa('S0', [], ['0','1']);
    }

    public function testEmptyAlphabetSymbolThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Alphabet symbols cannot be empty');
        new Dfa('S0', [
            'S0' => ['0' => 'S0', '1' => 'S1'],
        ], ['0', '', '1']);
    }

    public function testInitialStateNotInTransitionsThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Initial state 'S0' is not defined in transitions");
        new Dfa('S0', [
            'S1' => ['0' => 'S1', '1' => 'S2'],
            'S2' => ['0' => 'S2', '1' => 'S1'],
        ], ['0','1']);
    }

    public function testMissingTransitionThrows(): void
    {
        // Create DFA with incomplete transitions (S2 missing transitions for some states)
        $dfa = new Dfa('S0', [
            'S0' => ['0' => 'S0', '1' => 'S1'],
            'S1' => ['0' => 'S2', '1' => 'S0'],
            'S2' => ['0' => 'S1'],  // Missing '1' transition from S2
        ], ['0','1']);

        $this->expectException(InvalidArgumentException::class);
        $dfa->step('S2', '1');
    }
}
