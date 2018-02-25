<?php
/**
 * Plugin Name: WP Password Argon Two
 * Plugin URI:  https://typist.tech
 * Description: Replaces wp_hash_password and wp_check_password's phpass hasher with PHP 7.2's password_hash and password_verify using Argon2i.
 * Author:      Typist Tech
 * Author URI:  https://typist.tech
 * Version:     0.1.0
 * Licence:     MIT
 */

declare(strict_types=1);

// Order matters.
require_once __DIR__ . '/src/ValidatorInterface.php';
require_once __DIR__ . '/src/Validator.php';
require_once __DIR__ . '/src/WordPressValidator.php';
require_once __DIR__ . '/src/PasswordLock.php';
require_once __DIR__ . '/src/Manager.php';
require_once __DIR__ . '/src/ManagerFactory.php';
require_once __DIR__ . '/src/pluggable.php';
