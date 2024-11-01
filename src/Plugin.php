<?php
namespace Woozapp;

/**
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
 * Class Plugin
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Plugin
{
    /**
     * Plugin Version
     *
     * @since  1.0.0
     * @access static
     *
     * @var string The plugin current version
     */
    const PLUGIN_VERSION = '1.1.0';

    /**
     * Get Plugin Dir Path
     *
     * @since  1.0.0
     * @access static
     *
     * @return string The plugin dir path
     */
    public static function getPluginDirPath()
    {
        return untrailingslashit(plugin_dir_path(__DIR__));
    }

    /**
     * Get Plugin Dir Url
     *
     * @since  1.0.0
     * @access static
     *
     * @return string The plugin dir url
     */
    public static function getPluginDirUrl()
    {
        return untrailingslashit(plugin_dir_url(__DIR__));
    }
}
