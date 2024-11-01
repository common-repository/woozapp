<?php
namespace Woozapp\Form\Abstracts;

use Woozapp\Traits\Arguments;
use Woozapp\Form\Interfaces\Fields;

/**
 * Abstract Field
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
 * Class Abstract Field
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
abstract class Field implements Fields
{
    use Arguments;

    /**
     * Input Type
     *
     * @since  1.0.0
     * @access protected
     *
     * @var Type The object type
     */
    private $type;

    /**
     * Arguments
     *
     * @since  1.0.0
     * @access protected
     *
     * @var array The field arguments
     */
    private $args;

    /**
     * Construct
     *
     * @since  1.0.0
     * @access public
     *
     * @param Type  $type The input type related to this field.
     * @param array $args The arguments to build the field.
     */
    public function __construct(Type $type, $args)
    {
        // Set the input type.
        $this->type = $type;

        // Set the arguments for the current field.
        $this->args = wp_parse_args($args, [
            'container'           => 'div',
            'container_class'     => [
                'woozapp-field',
                'woozapp-field--' . sanitize_key($this->type->getArg('type')),
            ],
            'label'               => '',
            'before_label'        => '',
            'before_input'        => '',
            'after_input'         => '',
            'desc_container'      => 'p',
            'description'         => '',
            'invalid_description' => '',
        ]);
    }

    /**
     * To String
     *
     * Return the html version of the current field
     *
     * @since  1.0.0
     * @access public
     *
     * @return string The current field in html format
     */
    public function __toString()
    {
        return $this->getHtml();
    }

    /**
     * Get Type
     *
     * @since  1.0.0
     * @access public
     *
     * @return Type The type associated to the current field
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Execute the Before or After callback
     *
     * @since  1.0.0
     * @access protected
     *
     * @param callable $func A callback function to call.
     *
     * @return mixed The returned value of the callback or an empty string if the callback is not callable.
     */
    protected function argCb($func)
    {
        if (! is_callable($this->getArg($func))) {
            return '';
        }

        return call_user_func($this->getArg($func), $this);
    }

    /**
     * Get Label
     *
     * @since  1.0.0
     * @access protected
     *
     * @return string The label for the current field type
     */
    protected function getLabel()
    {
        if (! $this->getArg('label')) {
            return '';
        }

        // The label.
        return sprintf(
            '<label for="%s">%s</label>',
            sanitize_key($this->getType()->getArg('id')),
            esc_html($this->getArg('label'))
        );
    }

    /**
     * Get Description
     *
     * @since  1.0.0
     * @access protected
     *
     * @return string The field description markup
     */
    protected function getDescription()
    {
        if (! $this->getArg('description')) {
            return '';
        }

        // Scope Class.
        $scope = ! empty($this->getArg('container_class')) ? $this->getArg('container_class')[0] : '';

        // The field description.
        return sprintf(
            '<%1$s class="%2$s">%3$s</%1$s>',
            tag_escape($this->getArg('desc_container')),
            sanitize_html_class($scope) . '__description',
            sanitize_text_field($this->getArg('description'))
        );
    }
}
