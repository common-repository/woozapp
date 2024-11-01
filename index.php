<?php
/**
 * Woozapp
 *
 * @package Woozapp
 * @version 1.1.0
 *
 * @wordpress-plugin
 * Plugin Name: Woozapp
 * Plugin URI: http://www.woozapp.com
 * Description: Woozapp is the best plugin to Build Real-time mobile Apps for any WordPress Website
 * Version: 1.1.0
 * Author: Woozapp
 * Author URI: http://www.woozapp.com
 * License: GPL2
 *
 * Copyright (C) 2016 Guido Scialfa <dev@guidoscialfa.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined('WPINC') || die;

/**
 * Register Autoloader
 *
 * After registering this autoload function with SPL, the following line
 * would cause the function to attempt to load the \Foo\Bar\Baz\Qux class
 * from /path/to/project/src/Baz/Qux.php:
 *
 * new \Foo\Bar\Baz\Qux;
 *
 * @link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
 *
 * @param string $class The fully-qualified class name.
 *
 * @return void
 */
spl_autoload_register(function ($class) {

    // Project-specific namespace prefix.
    $prefix = 'Woozapp\\';

    // Base directory for the namespace prefix.
    $base_dir = __DIR__ . '/src/';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered auto-loader.
        return;
    }

    // Get the relative class name.
    $relative_class = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace.
    // Separators with directory separators in the relative class name, append with .php.
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // If the file exists, require it.
    if (file_exists($file)) {
        require_once $file;
    }
});

// Register the activation hook.
register_activation_hook(__FILE__, ['\\Woozapp\\Activate', 'activate']);

if (version_compare($GLOBALS['wp_version'], '4.5', '<')) {
    add_action('admin_notices', function () {
        printf(
            '<div class="%1$s"><p>%2$s</p></div>',
            'notice notice-warning is-dismissible',
            esc_html__('Wops! Your current WordPress version may be not compatible with this plugin.', 'woozapp')
        );
    });
}

\Woozapp\Init::getInstance()->init();
