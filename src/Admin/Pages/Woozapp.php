<?php
namespace Woozapp\Admin\Pages;

/**
 * Custom Menu Page
 *
 * @package Woozapp\Admin\Pages
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
 * Class Woozapp
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Woozapp
{
    /**
     * Add Menu Page
     *
     * @since  1.0.0
     * @access static
     *
     * @return void
     */
    public static function addMenuPage()
    {
        // Add the object page.
        add_menu_page(
            esc_html__('WooZap', 'woozapp'),
            esc_html__('WooZap', 'woozapp'),
            'manage_options',
            'woozapp',
            [__CLASS__, 'callback'],
            false
        );
    }

    /**
     * CallBack
     *
     * @since  1.0.0
     * @access static
     *
     * @return void
     */
    public static function callback()
    {
        WoozappSettings::callback();
    }
}
