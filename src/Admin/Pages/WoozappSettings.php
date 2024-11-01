<?php
namespace Woozapp\Admin\Pages;

use \Woozapp\Functions;
use \Woozapp\Plugin;
use \Woozapp\Settings;
use \Woozapp\Form;
use \Woozapp\Admin;
use \Woozapp\AlertBox;
use \Woozapp\Admin\Forms\Settings as FormSettings;

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

defined('WPINC') || die;

/**
 * Class WoozappSettings
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class WoozappSettings
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
        add_submenu_page(
            'woozapp',
            esc_html__('Settings', 'woozapp'),
            esc_html__('Settings', 'woozapp'),
            'manage_options',
            'woozapp-settings',
            [__CLASS__, 'callback']
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
        // Validate Settings.
        $response = FormSettings::handle();
        // The alert Box.
        $alertBox = '';

        if (! empty($response->validated)) {
            $type     = $response->success ? 'success' : 'error';
            $alertBox = (new AlertBox($response->msg, $type))->alertBox();
        }

        // Set the data for the view.
        /** @noinspection PhpUnusedLocalVariableInspection */
        $data = (object)[
            'form'      => FormSettings::form(),
            'alert'     => $alertBox,
            'response'  => $response,
            'settings'  => Settings::getInstance()->get(),
        ];

        // Get the correct view.
        if (empty($response->validated['invalid']) && $response->success) {
            require_once Plugin::getPluginDirPath() . '/views/admin/settingsPageSuccess.php';
        } else {
            require_once Plugin::getPluginDirPath() . '/views/admin/settingsPage.php';
        }
    }
}
