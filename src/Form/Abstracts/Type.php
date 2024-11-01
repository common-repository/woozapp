<?php
namespace Woozapp\Form\Abstracts;

use Woozapp\Traits\Arguments;
use Woozapp\Form\Interfaces\Types;

/**
 * Abstract Type
 *
 * @package Woozapp\Form\Abstracts
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
 * Class Abstract Type
 *
 * @todo    Need list of allowed attributes.
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
abstract class Type implements Types
{
    use Arguments;

    /**
     * Arguments
     *
     * @since  1.0.0
     * @access protected
     *
     * @var array A list of arguments for the input type
     */
    private $args;

    /**
     * Constructor
     *
     * @since  1.0.0
     * @access public
     *
     * @param array $args The arguments for this type.
     */
    public function __construct($args)
    {
        $args = wp_parse_args($args, [
            'name'        => '',
            'id'          => (isset($args['id']) ? $args['id'] : $args['name']),
            'attrs'       => array(),
            'sanitize_cb' => [$this, 'sanitize'],
            'escape_cb'   => [$this, 'escape'],
            'filter'      => '', // Needed, some input may not need a filter value.
            'is_invalid'  => false,
        ]);

        $this->args = $args;
    }

    /**
     * To String
     *
     * Return the html version of the current type
     *
     * @since  1.0.0
     * @access public
     *
     * @return string The current type in html format
     */
    public function __toString()
    {
        return $this->getHtml();
    }

    /**
     * Get Value
     *
     * @since  1.0.0
     * @access public
     *
     * @return mixed The value of the input type, empty string if there is no value to return.
     */
    public function getValue()
    {
        return $this->args['value'];
    }

    /**
     * Get Extra Attributes
     *
     * @since  1.0.0
     * @access protected
     *
     * @return string The string key="value" pair extracted from the attributes array
     */
    protected function getAttrs()
    {
        $attrs  = '';
        $_attrs = $this->getArg('attrs');

        if (! empty($_attrs) && is_array($_attrs)) {
            foreach ($_attrs as $key => $val) {
                $attrs .= ' ' . sanitize_key($key) . '="' . esc_attr($val) . '"';
            }
        }

        return $attrs;
    }

    /**
     * Apply Pattern
     *
     * Apply the pattern defined by the type if exists.
     *
     * @since  1.0.0
     * @access protected
     *
     * @param string $value The value to pass to the pattern.
     *
     * @return string The result of the applied pattern. Empty string if the pattern doesn't matched anything.
     */
    protected function applyPattern($value)
    {
        $pattern = isset($this->getArg('attrs')['pattern']) ? $this->getArg('attrs')['pattern'] : '';

        if ($pattern) {
            if (! preg_match("/{$pattern}/", $value)) {
                return '';
            }
        }

        return $value;
    }
}
