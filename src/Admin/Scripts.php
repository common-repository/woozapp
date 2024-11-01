<?php
namespace Woozapp\Admin;

use Woozapp\Plugin;

/**
 * Admin Scripts
 *
 * @package Woozapp\Admin
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
 * Class Scripts Filters
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Scripts
{
    /**
     * Get Scripts List
     *
     * @since  1.0.0
     * @access private
     *
     * @return array A list of scripts for this plugin
     */
    private static function getScriptsList()
    {
        // Don't use a watcher, minify only via grunt task instead.
        $prefix = defined('UNPREFIX_DEBUG') && UNPREFIX_DEBUG ? '' : '.min';

        return [
            ['woozapp', Plugin::getPluginDirUrl() . '/assets/js/admin' . $prefix . '.js', ['jquery'], '', true],
        ];
    }

    /**
     * Get Styles List
     *
     * @since  1.0.0
     * @access private
     *
     * @return array A list of styles for this plugin
     */
    private static function getStylesList()
    {
        return [
            ['woozapp', Plugin::getPluginDirUrl() . '/assets/css/admin.css', array(), '', 'screen'],
        ];
    }

    /**
     * Register/Enqueue Scripts and Styles
     *
     * @since  1.0.0
     * @access static
     *
     * @return void
     */
    public static function enqueueScripts()
    {
        // Register and enqueue styles.
        foreach (self::getStylesList() as $styles) {
            // Retrieve the params.
            list($handle, $src, $deps, $ver, $media) = $styles;

            // Register and enqueue.
            wp_register_style($handle, $src, $deps, $ver, $media);
            wp_enqueue_style($handle);
        }

        // Register and enqueue scripts.
        foreach (self::getScriptsList() as $scripts) {
            // Retrieve the params.
            list($handle, $src, $deps, $ver, $inFooter) = $scripts;

            // Register and enqueue.
            wp_register_script($handle, $src, $deps, $ver, $inFooter);
            wp_enqueue_script($handle);
        }
    }
}
