<?php
namespace Woozapp\EndPoints;

use \Woozapp\Logger;
use \Woozapp\Db\Delegate as Db;

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
 * Class Dispatcher
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Dispatcher
{
    /**
     * Register EndPoint
     *
     * @since  1.0.0
     * @access static
     *
     * @return void
     */
    public static function register()
    {
        add_rewrite_tag('%woozapp%', '([^&]+)');
        add_rewrite_tag('%subpoint%', '([^&]+)');

        add_rewrite_rule('^woozapp/([a-z0-9\-]+)?/?$', 'index.php?woozapp=1&subpoint=$matches[1]', 'top');
        flush_rewrite_rules();
    }

    /**
     * Dispatcher
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     */
    public static function dispatch()
    {
        global $wp;

        if (! isset($wp->query_vars['woozapp'])) {
            return;
        }

        // Get Logger.
        $logger = new Logger();
        // Get the subpoint.
        $subPoint = isset($wp->query_vars['subpoint']) ? $wp->query_vars['subpoint'] : '';

        if ($subPoint) {
            try {
                // Call the correct resource for the request.
                switch ($subPoint) {
                    case 'settings':
                        Settings::run();
                        break;

                    case 'adddevice':
                        $device = new DataDevice();
                        $device->sanitize();

                        // Store the data into the db.
                        $db       = new Db($device->getData());
                        $response = $db->delegate();

                        // No rows has been updated or created.
                        if (! $response) {
                            $logger->log(sprintf(
                                esc_html__('Error during insert/update a device: %s', 'woozapp'),
                                serialize($device->getData())
                            ));
                        }
                        // @todo Set the header for the response.
                        break;
                    default:
                        break;
                }
            } catch (\Exception $e) {
                $logger->log($e->getMessage());
            }
        }

        die;
    }
}
