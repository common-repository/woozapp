<?php
/**
 * Admin Settings Page View
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 * @package Woozapp\Admin\Views
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

$site_url = isset($data->settings['site_url']) ? $data->settings['site_url'] : home_url('/');
?>

<div class="woozapp-wrapper">

    <h1>
        <?php esc_html_e('Woozapp', 'woozapp') ?>
        <span><?php esc_html_e(' | settings') ?></span>
    </h1>

    <div class="woozapp-content">
        <div class="row">
            <div class="main-content">
                <?php echo \Woozapp\Functions\kses_post($data->form->getHtml()); ?>
                <div class="woozapp-loader" style="display:none;">
                    <img src="<?php echo esc_url(\Woozapp\Plugin::getPluginDirUrl() . '/assets/svg/loader.svg'); ?>"/>
                </div>
            </div>
            <div class="site-frame">
                <iframe id="site" src="<?php echo esc_url($site_url) ?>"></iframe>
            </div>
        </div>
    </div>

    <?php Woozapp\Functions\getFooter() ?>
</div>
