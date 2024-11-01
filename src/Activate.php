<?php
namespace Woozapp;

/**
 * Activate
 *
 * @package Woozapp
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
 * Class Activate
 *
 * @wtodo   Test the creation when there is the option.
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Activate
{
    /**
     * Db Version
     *
     * @since  1.0.0
     * @access private
     *
     * @var string The version of the current plugin db
     */
    private static $dbVersion = '1.0.0';

    /**
     * Create Plugin Tables
     *
     * @since  1.0.0
     * @access private
     *
     * @return void
     */
    private static function createTables()
    {
        global $wpdb;

        // Check if the db version is changed.
        if (self::$dbVersion === get_option('woozapp_db_version')) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate(); // Database collate.

        $sql = "CREATE TABLE woozapp (
          id int(11) NOT NULL AUTO_INCREMENT,
          device_id varchar(128) NOT NULL,
          device_type varchar(255) NOT NULL,
          token longtext NOT NULL,
          os_version varchar(255) NOT NULL,
          app_version varchar(255) NOT NULL,
          language varchar(255) DEFAULT '' NOT NULL,
          created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          enabled tinyint(1) DEFAULT 1 NOT NULL,
          attempts tinyint(1) DEFAULT 0 NOT NULL,
          note longtext DEFAULT '' NOT NULL,
          debug tinyint(1) DEFAULT 0 NOT NULL,
          PRIMARY KEY  (id),
          UNIQUE KEY device_id (device_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($sql);
    }

    /**
     * Check Php Version
     *
     * @since  1.0.0
     * @access static
     *
     * @return void
     */
    public static function checkPhpVersion()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $msg = sprintf(
                esc_html__(
                    'Woozapp require at least php 5.4. We cannot activate the plugin since your php version is %s',
                    'woozapp'
                ),
                PHP_VERSION
            );

            wp_die(esc_html($msg));
        }
    }

    /**
     * Plugin Activate
     *
     * @since  1.0.0
     * @access static
     *
     * @return void
     */
    public static function activate()
    {
        // Check PhpVersion.
        self::checkPhpVersion();
        // Create the tables needed by the plugin.
        self::createTables();
        // Update the db version option.
        update_site_option('woozapp_db_version', self::$dbVersion);
    }
}
