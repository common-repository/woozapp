<?php
namespace Woozapp\Admin\Ajax;

use \Woozapp\Admin\Forms;

/**
 * Dispatcher Admin Ajax
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
final class Delegate
{
    /**
     * Dispatcher
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     */
    public static function delegate()
    {
        // No Ajax?
        (defined('DOING_AJAX') && DOING_AJAX) || wp_die('Cheatin&#8217; Uh?');

        // The response data.
        $response = array();
        // Get the Action to dispatch.
        $action = filter_input(INPUT_POST, 'delegate', FILTER_SANITIZE_STRING);

        if (! $action) {
            wp_die();
        }

        switch ($action) {
            case 'woozapp_form_settings':
                // Validate Settings.
                $response = Forms\Settings::handle();
                break;
            case 'frame':
                $src = filter_input(INPUT_POST, 'src', FILTER_SANITIZE_URL);
                $src = '<iframe id="site" src="' . esc_url($src) . '"></iframe>';
                wp_send_json($src);
                break;
            default:
                wp_die('Cheatin&#8217 Uh?');
                break;
        }

        wp_send_json($response);
    }
}
