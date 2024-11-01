<?php
namespace Woozapp;

/**
 * Settings
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
     * Settings
     *
     * @since  1.0.0
     * @access private
     *
     * @var string The settings transient name
     */
    private $settings;

    /**
     * Expire
     *
     * @since  1.0.0
     * @access private
     *
     * @var int The second when the transient timeout will expire
     */
    private $expire;

    /**
     * Construct
     *
     * @since  1.0.0
     * @access private
     */
    private function __construct()
    {
        $this->settings = 'woozapp_app_settings';
        $this->expire   = 0; //(60 * 60 * 12);
    }

    /**
     * Prevent Cloning
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error(esc_attr__('Cheatin&#8217; huh?', 'woozapp'), E_USER_ERROR);
    }

    /**
     * Get Instance
     *
     * @since  1.0.0
     * @access static
     */
    public static function getInstance()
    {
        static $instance = null;

        if (null === $instance) {
            $instance = new self;
        }

        return $instance;
    }

    /**
     * Store Settings
     *
     * @since  1.0.0
     * @access public
     *
     * @throws \Exception In case the json encode is not possible.
     *
     * @param array $data A list of key value pairs to store into transient.
     *
     * @return bool True on success false on failure
     */
    public function store(array $data)
    {
        // Check if the timeout transient is expired.
        // @todo Re enable it.
        /*if (get_transient($this->settings)) {
            throw new \Exception(esc_html__('You cannot save the app settings know.', 'woozapp'));
        }*/

        // Store the transient and set the timeout.
        return set_site_transient($this->settings, $data, $this->expire);
    }

    /**
     * Get Settings
     *
     * @since  1.0.0
     * @access public
     *
     * @return mixed The transient data
     */
    public function get()
    {
        return get_site_transient($this->settings);
    }
}
