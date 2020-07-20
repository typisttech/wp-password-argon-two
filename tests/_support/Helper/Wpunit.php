<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo\Helper;

use Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I
class Wpunit extends Module
{
    public function _beforeSuite($settings = [])
    {
        parent::_beforeSuite($settings);

        if (! defined('WP_PASSWORD_ARGON_TWO_PEPPER')) {
            define('WP_PASSWORD_ARGON_TWO_PEPPER', 'my-pepper');
        }

        if (! defined('WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS')) {
            define('WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS', [
                'my-second-pepper',
                'my-third-pepper',
            ]);
        }

        if (! defined('WP_PASSWORD_ARGON_TWO_OPTIONS')) {
            define('WP_PASSWORD_ARGON_TWO_OPTIONS', [
                'memory_cost' => 1024,
                'time_cost'   => 2,
                'threads'     => 1,
            ]);
        }
    }
}
