<?php
declare(strict_types=1);

namespace ModThree;

use InvalidArgumentException;
use ModThree\Fsm\Dfa;

final class ModThree
{
    private const S0 = 'S0';
    private const S1 = 'S1';
    private const S2 = 'S2';

    /** @var array<string,int> */
    private const STATE_TO_REMAINDER = [
        self::S0 => 0,
        self::S1 => 1,
        self::S2 => 2,
    ];

    public static function modThree(string $input): int
    {
        // Empty input => initial state S0 => remainder 0
        if ($input === '') {
            return 0;
        }

        $dfa = self::dfa();
        $finalState = $dfa->run($input);

        return self::STATE_TO_REMAINDER[$finalState]
            ?? throw new InvalidArgumentException("Unexpected final state '{$finalState}'.");
    }

    private static function dfa(): Dfa
    {
        $transitions = [
            self::S0 => ['0' => self::S0, '1' => self::S1],
            self::S1 => ['0' => self::S2, '1' => self::S0],
            self::S2 => ['0' => self::S1, '1' => self::S2],
        ];

        return new Dfa(self::S0, $transitions, ['0', '1']);
    }
}
