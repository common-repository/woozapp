<?php

/**
 * Admin Settings Page Success View
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

<?php if (! isset($data->doingAjax)) : ?>
<div class="woozapp-wrapper">
    <h1>
        <?php esc_html_e('Woozapp', 'woozapp') ?>
        <span><?php esc_html_e(' | settings') ?></span>
    </h1>

    <div class="woozapp-content">
        <div class="row">
            <div class="main-content main-content--success">
                <?php endif; ?>

                <img class="woozapp-thumbnail"
                     src="<?php echo esc_url(\Woozapp\Plugin::getPluginDirUrl() . '/assets/imgs/thumbnail-thank-you.png') ?>"
                />

                <h2 class="woozapp-content__title"><?php esc_html_e('Thank you', 'woozapp') ?></h2>
                <p class="woozapp-content__content">
                    <?php esc_html_e(
                        'Your demo is on the way! Check the email with your Android device and open the attachment, then follow the installations steps.',
                        'woozapp'
                    ) ?>
                </p>
                <div class="woozapp-actions u-text--big">
                    <a href="<?php echo esc_url(admin_url('/admin.php?page=woozapp')) ?>">
                        <?php esc_html_e('Generate another demo', 'woozapp') ?>
                    </a>
                    <?php esc_html_e('or', 'woozapp') ?>
                    <a class="woozapp-btn woozapp-btn--normal" href="http://www.woozapp.com">
                        <?php esc_html_e('Buy it', 'woozapp') ?>
                    </a>
                </div>

                <?php if (! isset($data->doingAjax)) : ?>
            </div>

            <div class="site-frame">
                <iframe id="site" src="<?php echo esc_url($site_url) ?>"></iframe>
            </div>
        </div>
    </div>
    <?php Woozapp\Functions\getFooter(); ?>
</div>
<?php endif; ?>
