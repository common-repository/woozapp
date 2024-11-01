<?php
namespace Woozapp\EndPoints;

/**
 * EndPoint Settings
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
 * Class Settings
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Settings
{
    /**
     * Run the request
     *
     * @since  1.0.0
     * @access static
     *
     * @throws \Exception in case the data settings are empty or cannot decode as json.
     *
     * @return void
     */
    public static function run()
    {
        // Get the plugin settings.
        $data = \Woozapp\Settings::getInstance()->get();

        if (! $data) {
            throw new \Exception(esc_html__('Retrieve Settings: Data is empty.', 'woozapp'));
        }

        // Encode Json.
        $json          = wp_json_encode($data);
        $jsonLastError = json_last_error();

        if (JSON_ERROR_NONE !== $jsonLastError) {
            throw new \Exception(sprintf(
                esc_html__('Retrieve Settings: Cannot decode json data %s', 'woozapp'),
                $jsonLastError
            ));
        }

        // Output the json.
        echo $json;
    }
}
