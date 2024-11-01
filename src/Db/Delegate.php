<?php
namespace Woozapp\Db;

/**
 * Database Delegate
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
 * Class Delegate
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Delegate
{
    /**
     * Action
     *
     * @since  1.0.0
     * @access private
     *
     * @var string The action to do
     */
    private $action;

    /**
     * Data
     *
     * @since  1.0.0
     * @access private
     *
     * @var object The data to store into the database as object.
     */
    private $data;

    /**
     * Construct
     *
     * @since  1.0.0
     * @access public
     *
     * @throws  \Exception when the data is empty.
     *
     * @param \stdClass $data   The object containing the data to store into the db.
     * @param string    $action The action to perform.
     */
    public function __construct(\stdClass $data, $action = 'insert')
    {
        if (empty((array)$data)) {
            throw new \Exception('Delegate: Data is empty');
        }

        $this->action = sanitize_key(str_replace('-', '_', $action));
        $this->data   = $data;
    }

    /**
     * Delegate
     *
     * @since  1.0.0
     * @access public
     *
     * @return mixed
     */
    public function delegate()
    {
        global $wpdb;

        $response = 0; // @todo Need to be a valid http code?

        switch ($this->action) {
            case 'insert':
                // Check if data exists.
                // @todo Move into a method.
                $query   = 'SELECT device_id FROM woozapp WHERE device_id = %s';
                $results = $wpdb->get_results($wpdb->prepare($query, [$this->data->device_id], ['%s', '%s']));

                if (empty($results)) {
                    $response = Insert::insert($this->data);
                } else {
                    $response = Update::update($this->data);
                }
                break;

            default:
                break;
        }

        return $response;
    }
}
