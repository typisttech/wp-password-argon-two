<?php
/**
 * Plugin Name: WP Password Argon Two
 * Plugin URI:  https://typist.tech
 * Description: Replaces wp_hash_password and wp_check_password's phpass hasher with PHP 7.2's password_hash and password_verify using Argon2i.
 * Author:      Typist Tech
 * Author URI:  https://typist.tech
 * Version:     0.1.0
 * Licence:     MIT.
 */
declare(strict_types=1);

require_once WPMU_PLUGIN_DIR.'/src/FallbackPasswordLock.php';
require_once WPMU_PLUGIN_DIR.'/src/PasswordLock.php';
require_once WPMU_PLUGIN_DIR.'/src/pluggable.php';
