<?php
namespace Woozapp;

/**
 * Alert Boxes
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
 * Class AlertBox
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class AlertBox
{
    /**
     * List
     *
     * @since  1.0.0
     * @access private
     *
     * @var array A list of alerts
     */
    private $list;

    /**
     * Get Message Markup
     *
     * @since  1.0.0
     * @access private
     *
     * @param array $list An array containing the messages list
     *
     * @return string The messages markup list
     */
    private function getMessagesMarkup($list)
    {
        if (empty($list)) {
            return '';
        }

        $output = '';

        // Add the message.
        $output .= '<p class="woozapp-alert-box__content">' . wp_kses_post(reset($list)) . '</p>';

        // All other messages are items of a list.
        if (1 < count($list)) {
            $output .= '<ul class="woozapp-alert-box__list">';
            foreach ($list as $msg) {
                $output .= '<li class="woozapp-alert-box__list__item">' . wp_kses_post(reset($msg)) . '</li>';
            }
            $output .= '</ul>';
        }

        return $output;
    }

    /**
     * Construct
     *
     * @since      1.0.0
     * @access     public
     *
     * @param array|string $messages The message of the alert box.
     * @param string       $type     The type of the alert box. Optional. Allowed: error, notice, warning, success.
     */
    public function __construct($messages, $type = 'notice')
    {
        $this->list[] = [
            'type'     => $type,
            'messages' => (array)$messages,
        ];
    }

    /**
     * Get Message
     *
     * @since  1.0.0
     * @access public
     *
     * @return string The message of the alert box
     */
    public function getMessages()
    {
        return $this->list;
    }

    /**
     * Has Messages
     *
     * @since  1.0.0
     * @access public
     *
     * @return bool True if the is list is not empty, false otherwise.
     */
    public function hasMessages()
    {
        return ! empty($this->list);
    }

    /**
     * Clean List
     *
     * @since  1.0.0
     * @access public
     *
     * @return array The list of the message before clean it.
     */
    public function clean()
    {
        // Get the list.
        $_list = $this->list;
        // Clean the list.
        $this->list = array();

        return $_list;
    }

    /**
     * Show Alert Box
     *
     * @since  1.0.0
     * @access public
     *
     * @return string The html markup if $echo is set to false.
     */
    public function alertBox()
    {

        $output = '';

        foreach ($this->list as $msgs) {
            $modifier = 'woozapp-alert-box--' . $msgs['type'];
            $output .= sprintf(
                '<div class="woozapp-alert-box %s">%s</div>',
                $modifier,
                $this->getMessagesMarkup($msgs['messages'])
            );
        }

        // Clean the list.
        $this->clean();

        return $output;
    }
}
