<?php
namespace Woozapp\Db;

use Woozapp\Functions;

/**
 * Database Insert
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

/**
 * Class Insert
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Insert
{
    /**
     * Insert
     *
     * @since  1.0.0
     * @access static
     *
     * @throws \Exception in case the data is emptu.
     *
     * @param \stdClass $data The data as object to store into the database.
     *
     * @return int|false The number of rows inserted, or false on error.
     */
    public static function insert(\stdClass $data)
    {
        global $wpdb;

        if (empty((array)$data)) {
            throw new \Exception('Delegate Insert: Data is empty');
        }

        // Set the current timezone based on option.
        $timeZone = new \DateTimeZone(Functions\getTimeZone());

        return $wpdb->insert(
            'woozapp',
            [
                'device_id'   => $data->device_id,
                'device_type' => $data->device_type,
                'token'       => $data->token,
                'os_version'  => $data->os_version,
                'app_version' => $data->app_version,
                'language'    => $data->language,
                'created_at'  => (new \DateTime('now', $timeZone))->format('Y-m-d H:i:s'),
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ]
        );
    }
}
