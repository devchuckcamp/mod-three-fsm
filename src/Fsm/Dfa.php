<?php
declare(strict_types=1);

namespace ModThree\Fsm;

use InvalidArgumentException;

/**
 * Deterministic Finite Automaton (DFA)
 *
 * Transitions are provided as:
 *  [
 *    'S0' => ['0' => 'S0', '1' => 'S1'],
 *    'S1' => ['0' => 'S2', '1' => 'S0'],
 *    ...
 *  ]
 */
final class Dfa
{
    /** @var array<string, array<string, string>> */
    private array $transitions;

    /** @var array<string, bool> */
    private array $alphabet;

    private string $initialState;

    /**
     * @param array<string, array<string, string>> $transitions
     * @param list<string> $alphabet
     */
    public function __construct(string $initialState, array $transitions, array $alphabet)
    {
        if ($initialState === '') {
            throw new InvalidArgumentException('Initial state cannot be empty.');
        }
        if (empty($transitions)) {
            throw new InvalidArgumentException('Transitions cannot be empty.');
        }

        $this->initialState = $initialState;
        $this->transitions = $transitions;

        $this->alphabet = [];
        foreach ($alphabet as $symbol) {
            if ($symbol === '') {
                throw new InvalidArgumentException('Alphabet symbols cannot be empty.');
            }
            $this->alphabet[$symbol] = true;
        }

        // Minimal validation: ensure initial state exists in transitions.
        if (!array_key_exists($initialState, $transitions)) {
            throw new InvalidArgumentException("Initial state '{$initialState}' is not defined in transitions.");
        }
    }

    public function initialState(): string
    {
        return $this->initialState;
    }

    public function step(string $state, string $symbol): string
    {
        if (!isset($this->alphabet[$symbol])) {
            throw new InvalidArgumentException("Invalid symbol '{$symbol}'.");
        }
        if (!isset($this->transitions[$state])) {
            throw new InvalidArgumentException("Unknown state '{$state}'.");
        }
        if (!isset($this->transitions[$state][$symbol])) {
            throw new InvalidArgumentException("No transition defined for state '{$state}' on symbol '{$symbol}'.");
        }

        return $this->transitions[$state][$symbol];
    }

    public function run(string $input): string
    {
        $state = $this->initialState;

        $len = strlen($input);
        for ($i = 0; $i < $len; $i++) {
            $symbol = $input[$i]; // safe for '0'/'1' ASCII
            $state = $this->step($state, $symbol);
        }

        return $state;
    }
}
