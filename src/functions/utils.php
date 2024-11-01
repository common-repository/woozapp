<?php
namespace Woozapp\Functions;

/**
 * Kses Functions
 *
 * @package Woozapp\Functions
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
 * Switch Upload Dir & Url
 *
 * @since 1.0.0
 *
 * @param string $file   The file path.
 * @param string $switch The reference to the string to replace. Allowed 'dir>url', 'url>dir'.
 *
 * @return string The file url
 */
function switchUploadDirPathUrl($file, $switch = 'dir>url')
{
    // Get upload dir data.
    $uploadDir = wp_upload_dir();

    if ('dir>url' === $switch) {
        $_file = str_replace($uploadDir['basedir'], $uploadDir['baseurl'], $file);
    } elseif ('url>dir' === $switch) {
        $_file = str_replace($uploadDir['baseurl'], $uploadDir['basedir'], $file);
    } else {
        $_file = $file;
    }

    return $_file;
}

/**
 * Get the TimeZone
 *
 * Retrieve the timezone based on the WordPress option
 *
 * @since 1.0.0
 *
 * @return string The timezone option value
 */
function getTimeZone()
{
    // Timezone_string is empty when the option is set to Manual Offset. So we use gmt_offset.
    $option = get_option('timezone_string') ? get_option('timezone_string') : get_option('gmt_offset');
    // Set to UTC in order to prevent issue if used with DateTimeZone constructor.
    $option = (in_array($option, array('', '0'), true) ? 'UTC' : $option);

    return $option;
}

/**
 * Is Json
 *
 * @since 1.1.0
 *
 * @return bool True if the data is a json string, false otherwise.
 */
function isJSON($data)
{
    if (! is_string($data) || '' === $data) {
        return false;
    }

    return (json_decode($data) && JSON_ERROR_NONE === json_last_error());
}
