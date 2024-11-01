<?php
namespace Woozapp\EndPoints;

/**
 * EndPoint Data Devices
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
 * Class DataDevice
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class DataDevice
{
    /**
     * Sanitize Callbacks
     *
     * @since  1.0.0
     * @access private
     *
     * @var array A list of key values pair of properties names and callback functions
     */
    private $sanitizeCbs;

    /**
     * Data
     *
     * @since  1.0.0
     * @access private
     *
     * @var object The data object
     */
    private $data;

    /**
     * Construct
     *
     * @since  1.0.0
     * @access public
     *
     * @throws \Exception If the json decode fail or data is empty.
     */
    public function __construct()
    {
        $this->data        = (object)json_decode(file_get_contents('php://input'));
        $this->sanitizeCbs = array();

        $lastJsonError = json_last_error();

        if (empty((array)$this->data)) {
            throw new \Exception('Data Device is empty');
        }

        if (JSON_ERROR_NONE !== $lastJsonError) {
            throw new \Exception(sprintf('Errors during decode the data. Last Json Error: %s', $lastJsonError));
        }
    }

    /**
     * Get Data
     *
     * @since  1.0.0
     * @access public
     *
     * @return \stdClass The data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sanitize Data
     *
     * @since  1.0.0
     * @access public
     *
     * @throws \Exception In case the sanitize fail.
     */
    public function sanitize()
    {
        $log = '';

        // Sanitize the values before send them to the db.
        foreach (get_object_vars($this->data) as $name => $prop) {
            // Don't write directly the property, so we can store the log with the incorrect value for debug.
            $prop = isset($this->sanitizeCbs[$name]) ? $this->sanitizeCbs[$name]($prop) : $prop;

            if (! $prop) {
                $log .= 'SAN: ' . $name . ' - Value: ' . $prop . ' - RetValue: ' . $this->data->$name . '|| ';
                continue;
            }

            // Restore the sanitize property value.
            $this->data->$name = $prop;
        }

        if (! empty($log)) {
            throw new \Exception($log);
        }
    }
}
