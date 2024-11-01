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

defined('WP_UNINSTALL_PLUGIN') || exit;

/**
 * Class Uninstall
 *
 * @wtodo   Test the creation when there is the option.
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Uninstall
{
    /**
     * Drop Tables
     *
     * @since  1.0.0
     * @access private
     *
     * @return void
     */
    private static function dropTables()
    {
        global $wpdb;

        $wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS woozapp'));
    }

    /**
     * Remove all options
     *
     * @since  1.0.0
     * @access private
     *
     * @return void
     */
    private static function removeAllOptions()
    {
        // Delete the DB option.
        delete_site_option('woozapp_db_version');
        // Delete Transients.
        delete_site_transient('woozapp_app_settings');
    }

    /**
     * Remove Uploads
     *
     * @since  1.0.0
     * @access private
     *
     * @return void
     */
    private static function removeUploads()
    {
        if (! class_exists('WP_Filesystem_Direct')) {
            require_once untrailingslashit(ABSPATH) . '/wp-admin/includes/class-wp-filesystem-base.php';
            require_once untrailingslashit(ABSPATH) . '/wp-admin/includes/class-wp-filesystem-direct.php';
        }

        $filesystem = new \WP_Filesystem_Direct(array());
        $uploadDir  = wp_upload_dir();

        // Remove the log files.
        $logFile = untrailingslashit($uploadDir['basedir']) . '/woozapp-log.log';
        if ($filesystem->exists($logFile)) {
            $filesystem->delete($logFile);
        }

        // Remove the plugin upload dir.
        $pluginUploadDir = untrailingslashit($uploadDir['basedir']) . '/woozapp';
        if ($filesystem->exists($pluginUploadDir)) {
            $filesystem->delete($pluginUploadDir);
        }
    }

    /**
     * Uninstall plugin
     *
     * @since  1.0.0
     * @access static
     *
     * @return void
     */
    public static function uninstall()
    {
        // Drop Database Table
        self::dropTables();
        // Remove all options.
        self::removeAllOptions();
        // Delete the Uploads files and directories.
        self::removeUploads();
    }
}

Uninstall::uninstall();
