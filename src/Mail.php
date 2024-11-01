<?php
namespace Woozapp;

use \Woozapp\Settings;

/**
 * Mail Handler
 *
 * @todo Make it abstract and extend by context.
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
 * Class Mail
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
class Mail
{
    /**
     * To
     *
     * @since  1.0.0
     * @access private
     *
     * @var string The email address
     */
    private $to;

    /**
     * Subject
     *
     * @since  1.0.0
     * @access private
     *
     * @var string The subject of the email
     */
    private $subject;

    /**
     * Template Path
     *
     * @since  1.0.0
     * @access private
     *
     * @var string The template to use as body for the email
     */
    private $templatePath;

    /**
     * Context Of the email
     *
     * @since  1.0.0
     * @access private
     *
     * @var string The context key name of the email
     */
    private $context;

    /**
     * Construct
     *
     * @since  1.0.0
     * @access public
     *
     * @param string $subject      The email subject.
     * @param string $templatePath The template path for this email.
     * @param string $context      The context of the email to generate correctly the template.
     */
    public function __construct($subject, $templatePath, $context)
    {
        // @todo make the property settable.
        $this->to           = 'info@appandmap.com';
        $this->subject      = $subject;
        $this->templatePath = $templatePath;
        $this->context      = $context;
    }

    /**
     * Send
     *
     * @since  1.0.0
     * @access public
     *
     * @return bool Whatever the wp_mail function return
     */
    public function send()
    {
        $content = $this->getContent();

        // Do not send email if there is no content to send.
        if ('' === $content) {
            return false;
        }

        return wp_mail($this->to, $this->subject, $content);
    }

    /**
     * Get Content Template Data
     *
     * @since  1.0.0
     * @access private
     *
     * @return string The content expanded
     */
    private function getContent()
    {
        $content = $this->getTemplate();

        if ('' === $content) {
            return '';
        }

        switch ($this->context) {
            case 'settings':
                // Get the settings.
                $settings = (array)Settings::getInstance()->get();

                // May be something went work, so return an empty string.
                if (empty($settings)) {
                    return '';
                }

                $s = '';
                foreach ($settings as $key => $value) {
                    $key = ucwords(str_replace('_', ' ', $key));
                    $s .= "{$key} : {$value}\n";
                }
                $s = rtrim($s, "\n");

                // Expand variables.
                $content = str_replace('{{adddeviceEndpoint}}', home_url('/?woozapp=1&subpoint=adddevice'), $content) .
                           "\n";
                $content = str_replace('{{settingsEndpoint}}', home_url('/?woozapp=1&subpoint=settings'), $content) .
                           "\n";
                $content = str_replace('{{settingsList}}', $s, $content) . "\n";
                break;
            default:
                break;
        }

        return $content;
    }

    /**
     * Get Template
     *
     * @since  1.0.0
     * @access private
     *
     * @return string The content of the template
     */
    private function getTemplate()
    {
        if (! class_exists('WP_Filesystem_Direct')) {
            require_once untrailingslashit(ABSPATH) . '/wp-admin/includes/class-wp-filesystem-base.php';
            require_once untrailingslashit(ABSPATH) . '/wp-admin/includes/class-wp-filesystem-direct.php';
        }

        $filesystem = new \WP_Filesystem_Direct(array());

        if (! $filesystem->exists($this->templatePath)) {
            return '';
        }

        return $filesystem->get_contents($this->templatePath);
    }
}
