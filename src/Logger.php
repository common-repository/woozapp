<?php
namespace Woozapp;

/**
 * Logger
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
 * Class Logger
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Logger
{
    /**
     * File Path
     *
     * @since  1.0.0
     * @access private
     *
     * @var string The file where the log will be stored
     */
    private $filePath;

    /**
     * Upload Dir
     *
     * @since  1.0.0
     * @access private
     *
     * @var array Data about the upload dir
     */
    private $uploadDir;

    /**
     * WP FileSystem
     *
     * @since  1.0.0
     * @access private
     *
     * @var \WP_Filesystem_Direct Instance
     */
    private $filesystem;

    /**
     * Time Zone
     *
     * @since  1.0.0
     * @access private
     *
     * @var \DateTimeZone The date Time zone based on WordPress options.
     */
    private $dateTimeZone;

    /**
     * Construct
     *
     * @since  1.0.0
     * @access public
     */
    public function __construct()
    {
        // Set the permission constants if not already set.
        if (! defined('FS_CHMOD_DIR')) {
            define('FS_CHMOD_DIR', (fileperms(ABSPATH) & 0777 | 0755));
        }
        if (! defined('FS_CHMOD_FILE')) {
            define('FS_CHMOD_FILE', (fileperms(ABSPATH . 'index.php') & 0777 | 0644));
        }

        if (! class_exists('WP_Filesystem_Direct')) {
            require_once untrailingslashit(ABSPATH) . '/wp-admin/includes/class-wp-filesystem-base.php';
            require_once untrailingslashit(ABSPATH) . '/wp-admin/includes/class-wp-filesystem-direct.php';
        }

        // TimeZone WordPress option.
        $timeZone = Functions\getTimeZone();

        $this->uploadDir    = wp_upload_dir();
        $this->filePath     = untrailingslashit($this->uploadDir['basedir']) . '/woozapp-log.log';
        $this->filesystem   = new \WP_Filesystem_Direct(array());
        $this->dateTimeZone = new \DateTimeZone($timeZone);
    }

    /**
     * Log Message
     *
     * @since  1.0.0
     * @access static
     *
     * @param string $message The message to store into file.
     *
     * @return void
     */
    public function log($message)
    {
        if ('' === $message) {
            return;
        }

        // Retrieve the current date.
        $time = new \DateTime('now', $this->dateTimeZone);
        // Add time at the begin of the message.
        $message = '[' . $time->format('Y-m-d H:i:s') . ' ' . $this->dateTimeZone->getName() . '] ' . $message;
        // Get the file content and append the new message.
        $message = $this->filesystem->get_contents($this->filePath) . "\r" . $message;

        // Try to put the content into the file.
        $this->filesystem->put_contents($this->filePath, $message, FS_CHMOD_FILE);
    }
}
