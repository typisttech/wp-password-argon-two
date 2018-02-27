<?php
/**
 * Plugin Name: WP Password Argon Two
 * Plugin URI:  https://typist.tech
 * Description: Securely store WordPress user passwords in database with Argon2i hashing and SHA-512 HMAC using PHP's native functions.
 * Author:      Typist Tech
 * Author URI:  https://typist.tech
 * Version:     0.1.0
 * Licence:     MIT
 */

declare(strict_types=1);

// Installing as a must-use plugin is the last resort.
// You should use composer autoload whenever possible.

// Order matters.
require_once __DIR__ . '/src/ValidatorInterface.php';
require_once __DIR__ . '/src/Validator.php';
require_once __DIR__ . '/src/PhpassValidator.php';
require_once __DIR__ . '/src/MDFiveValidator.php';
require_once __DIR__ . '/src/PasswordLock.php';
require_once __DIR__ . '/src/Manager.php';
require_once __DIR__ . '/src/ManagerFactory.php';
require_once __DIR__ . '/src/pluggable.php';
