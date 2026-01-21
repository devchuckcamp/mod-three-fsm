# FSM Generator Library - PHP Implementation

[![PHP CI](https://github.com/devchuckcamp/mod-three-fsm/actions/workflows/php-ci.yml/badge.svg)](https://github.com/devchuckcamp/mod-three-fsm/actions/workflows/php-ci.yml)
[![codecov](https://codecov.io/gh/devchuckcamp/mod-three-fsm/branch/main/graph/badge.svg)](https://codecov.io/gh/devchuckcamp/mod-three-fsm)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.3-blue.svg)](https://www.php.net)
[![License](https://img.shields.io/badge/license-Custom-orange.svg)](LICENSE)

A reusable Finite State Automaton (FSA) generator library for PHP 8.3, designed for developers to build custom automata. Includes a modulo-three implementation as a concrete example.

**Repository:** [github.com/devchuckcamp/mod-three-fsm](https://github.com/devchuckcamp/mod-three-fsm)

This library provides:
- **Generic FSA Framework** – Accept any 5-tuple FSA definition (Q, Σ, q₀, δ, F)
- **Type-Safe Implementation** – PHP 8.3 with comprehensive type declarations
- **Developer-Friendly API** – Clean, intuitive methods for creating and executing automata
- **Production-Ready** – Validation, error handling, and comprehensive test coverage
- **Extensible Design** – Modulo-three implementation plus reusable DFA framework

## Requirements

- **Docker:** Latest version
- **Docker Compose:** Latest version

**Alternative:** For local development without Docker, see [LOCAL_SETUP.md](LOCAL_SETUP.md).

## Setup (Docker)

```bash
git clone https://github.com/devchuckcamp/mod-three-fsm.git
cd mod-three-fsm/

# Build and start containers (custom PHP image includes Xdebug)
docker-compose up -d --build

# Install dependencies
docker-compose run composer install

# Verify PHP version inside container
docker-compose exec php php --version
```

## Dependency Management

### Composer via Docker

After modifying `composer.json` or adding new files, refresh the autoloader:

```bash
# Regenerate the autoloader
docker-compose run composer dump-autoload

# Or for a fresh install
docker-compose run composer install
```

This ensures that PSR-4 autoloading works correctly for any new classes added to the `src/` directory.

## Continuous Integration

This project uses **GitHub Actions** for automated testing and quality checks.

### Workflow Details

The CI pipeline runs automatically on:
- Every push to `main` branch
- Every pull request targeting `main`

**Test Job:**
- Validates `composer.json`
- Installs dependencies with caching
- Runs full test suite (14 tests, 31 assertions)
- Generates code coverage report (100% on core classes)
- Uploads coverage to Codecov
- Archives coverage artifacts

**Code Quality Job:**
- Checks for PHP syntax errors
- Verifies PSR-4 autoload compliance

### View Results

- **Build Status:** Check the badge at the top of this README
- **Coverage Reports:** View detailed coverage on [Codecov](https://codecov.io/gh/devchuckcamp/mod-three-fsm)
- **Workflow Runs:** See [Actions tab](https://github.com/devchuckcamp/mod-three-fsm/actions/workflows/php-ci.yml)

### Local Testing Before Push

Run the same checks locally to catch issues early:

```bash
# Validate composer.json
docker-compose run composer validate --strict

# Run syntax check on all PHP files
docker-compose exec php sh -c "find src tests -name '*.php' -exec php -l {} \;"

# Run full test suite with coverage
docker-compose exec -e XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-text --coverage-filter src
```

## Running Tests

### Using Docker

```bash
# Run all tests
docker-compose exec php vendor/bin/phpunit

# Run tests with detailed output
docker-compose exec php vendor/bin/phpunit --display-warnings --display-notices

# Run specific test file
docker-compose exec php vendor/bin/phpunit tests/ModThreeTest.php

# Run with code coverage (Xdebug preinstalled and enabled)
docker-compose exec -e XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-text --coverage-filter src

# Show all available options
docker-compose exec php vendor/bin/phpunit --help
```

### Using the Modulo-Three Function

```php
<?php
require_once 'vendor/autoload.php';

use ModThree\ModThree;

// Compute remainder for binary integers
echo ModThree::modThree("1101");  // Output: 1 (13 % 3 = 1)
echo ModThree::modThree("1110");  // Output: 2 (14 % 3 = 2)
echo ModThree::modThree("1111");  // Output: 0 (15 % 3 = 0)
``` 

### Using the DFA Framework

The `Dfa` class is a reusable FSA generator designed for other developers to build custom automata. Define any FSA as a 5-tuple: (Q, Σ, q₀, δ, F).

**Basic Example:**

```php
<?php
require_once 'vendor/autoload.php';

use ModThree\Fsm\Dfa;

// Create a DFA instance with your 5-tuple definition
$dfa = new Dfa(
    'S0',  // Initial state (q₀)
    [      // Transitions (δ): Q × Σ → Q
        'S0' => ['0' => 'S0', '1' => 'S1'],
        'S1' => ['0' => 'S2', '1' => 'S0'],
        'S2' => ['0' => 'S1', '1' => 'S2'],
    ],
    ['0', '1']  // Alphabet (Σ)
);

// Process input sequences
$finalState = $dfa->run("1101");      // S1
$nextState = $dfa->step('S0', '1');   // S1
```

## Architecture

### DFA Class (`src/Fsm/Dfa.php`)

The **generic FSA framework** accepts a 5-tuple definition:

- **Q** (States) – Implicitly defined through transition table keys
- **Σ** (Alphabet) – Explicitly provided as array of symbols
- **q₀** (Initial State) – Constructor parameter
- **δ** (Transitions) – Nested array mapping state × symbol → next state
- **F** (Final States) – All reachable states (implicit in this design)

**Key Methods:**
- `__construct(initialState, transitions, alphabet)` – Create FSA instance
- `initialState()` – Get the initial state
- `step(state, symbol)` – Transition one step
- `run(input)` – Process complete input sequence

**Validation:**
- Non-empty initial state and alphabet symbols
- Initial state defined in transitions
- Valid symbols for each input
- Complete transitions for all states

### ModThree Class (`src/ModThree.php`)

Concrete implementation solving the modulo-three problem:

- Maps final states to remainder values (S0→0, S1→1, S2→2)
- Lazily instantiates and caches the DFA
- Provides clean `modThree(string): int` API
- Error handling for invalid inputs

## Testing Strategy

### Framework Tests (`tests/DfaTest.php`)

Validates the reusable FSA generator:
- Correct state transitions for individual symbols
- Complete input sequence processing
- Invalid symbol detection
- Configuration validation

### Implementation Tests (`tests/ModThreeTest.php`)

Validates the modulo-three implementation:
- Provided examples (13, 14, 15)
- Edge cases (empty input, single bit, long sequences)
- Invalid input handling

## Example Walkthrough

**Processing "1101" (13 in binary):**

1. DFA initial state = S0
2. Read '1' → δ(S0, 1) = S1
3. Read '1' → δ(S1, 1) = S0
4. Read '0' → δ(S0, 0) = S0
5. Read '1' → δ(S0, 1) = S1
6. Final state = S1 → remainder = **1**
7. Verification: 13 % 3 = 1  // Correct!

## Clean Up

### Stop Docker Containers

```bash
# Stop all running containers
docker-compose down

# Stop and remove volumes
docker-compose down -v
```

## Additional Usage
