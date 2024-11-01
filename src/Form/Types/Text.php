<?php
namespace Woozapp\Form\Types;

use Woozapp\Form\Abstracts\Type;

/**
 * Form Text Type
 *
 * @package Woozapp\Form\Types
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
 * Class Text
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
class Text extends Type
{
    /**
     * Constructor
     *
     * @todo   Add Sanitize / Escape Callback as arguments and fallback to methods.
     *
     * @since  1.0.0
     * @access public
     *
     * @param array $args The arguments for this type.
     */
    public function __construct($args)
    {
        $args = wp_parse_args($args, [
            'type'   => 'text',
            'filter' => FILTER_SANITIZE_STRING,
        ]);

        parent::__construct($args);
    }

    /**
     * Sanitize
     *
     * @todo   Add sanitize by pattern
     *
     * @since  1.0.0
     * @access public
     *
     * @param string $value The value to sanitize.
     *
     * @return string The sanitized value of this type. Empty string if the value is not correct.
     */
    public function sanitize($value)
    {
        $value = sanitize_text_field($value);

        return $this->applyPattern($value);
    }

    /**
     * Escape
     *
     * @since  1.0.0
     * @access public
     *
     * @return string The escaped value of this type
     */
    public function escape()
    {
        return esc_html($this->getValue());
    }

    /**
     * Get Html
     *
     * @since  1.0.0
     * @access public
     *
     * @return string The html version of this type
     */
    public function getHtml()
    {
        return sprintf(
            '<input type="%s" name="%s" id="%s"%s />',
            sanitize_key($this->getArg('type')),
            sanitize_key($this->getArg('name')),
            sanitize_key($this->getArg('id')),
            $this->getAttrs()
        );
    }
}
