# Local Development Setup (Without Docker)

This guide provides instructions for setting up and running the FSM Generator Library directly on your local machine without Docker.

## Requirements

- **PHP:** 8.3 or higher
- **Composer:** Latest version
- **Xdebug or PCOV:** (Optional) For code coverage reporting

## Installation

### 1. Install PHP 8.3+

#### macOS (using Homebrew)
```bash
brew install php@8.3
brew link php@8.3
php --version
```

#### Ubuntu/Debian
```bash
sudo apt-get update
sudo apt-get install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl
php --version
```

#### Windows
1. Download PHP 8.3 from [php.net/downloads](https://www.php.net/downloads.php)
2. Extract to `C:\php83`
3. Add `C:\php83` to your PATH environment variable
4. Verify installation:
   ```powershell
   php --version
   ```

### 2. Install Composer

#### macOS/Linux
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

#### Windows
1. Download installer from [getcomposer.org](https://getcomposer.org/download/)
2. Run `Composer-Setup.exe`
3. Verify installation:
   ```powershell
   composer --version
   ```

### 3. Clone Repository and Install Dependencies

```bash
git clone https://github.com/devchuckcamp/mod-three-fsm.git
cd mod-three-fsm/php

# Install project dependencies
composer install
```

## Optional: Install Code Coverage Driver

### Option 1: Xdebug (Recommended for Development)

#### macOS/Linux
```bash
pecl install xdebug
```

Add to `php.ini`:
```ini
zend_extension=xdebug.so
xdebug.mode=coverage
```

#### Windows
1. Download appropriate Xdebug DLL from [xdebug.org/download](https://xdebug.org/download)
2. Place in PHP extensions directory
3. Add to `php.ini`:
   ```ini
   zend_extension=php_xdebug.dll
   xdebug.mode=coverage
   ```

### Option 2: PCOV (Lighter Alternative)

```bash
pecl install pcov
```

Add to `php.ini`:
```ini
extension=pcov.so
pcov.enabled=1
```

Verify installation:
```bash
php -m | grep -i xdebug  # or pcov
```

## Running Tests

### Run All Tests
```bash
vendor/bin/phpunit
```

### Run Tests with Details
```bash
vendor/bin/phpunit --display-warnings --display-notices
```

### Run Specific Test File
```bash
vendor/bin/phpunit tests/ModThreeTest.php
```

### Run with Code Coverage (requires Xdebug or PCOV)
```bash
# With Xdebug
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text --coverage-filter src

# With PCOV
php -d pcov.enabled=1 vendor/bin/phpunit --coverage-text --coverage-filter src
```

### Run Code Coverage Report (HTML)
```bash
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html coverage
# Open coverage/index.html in browser
```

## Running

Run any file directly:

```bash
php src/ModThree.php
```

## Updating Dependencies

### Refresh Autoloader
```bash
composer dump-autoload
```

### Update All Packages
```bash
composer update
```

### Install New Package
```bash
composer require vendor/package
```

## Troubleshooting

### Issue: "composer: command not found"
**Solution:** Ensure Composer is installed and added to PATH. Reinstall following steps above.

### Issue: "PHP version requirement not satisfied"
**Solution:** Verify PHP version with `php --version`. Must be 8.3+. Update PHP if needed.

### Issue: "Class not found" errors
**Solution:** Run `composer dump-autoload` to regenerate autoloader.

### Issue: Code coverage not working
**Solution:** 
1. Verify Xdebug/PCOV is installed: `php -m | grep -i xdebug`
2. Check `php.ini` configuration
3. Set `XDEBUG_MODE=coverage` environment variable

### Issue: Tests fail with permission errors
**Solution:** Ensure proper file permissions:
```bash
chmod -R 755 vendor/
chmod -R 755 src/
chmod -R 755 tests/
```

## Next Steps

- Read [README.md](README.md) for library usage and architecture
- 
## Support

For Docker-based setup (recommended), see [README.md](README.md#setup-docker).
