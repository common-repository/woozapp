<?php
namespace Woozapp\Form\Abstracts;

use Woozapp\Traits\Arguments;
use Woozapp\Form\Interfaces\Forms;

/**
 * Form
 *
 * @package Woozapp\Form
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
 * Class Abstract Form
 *
 * @todo    Need list for allowed attributes.
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
abstract class Form implements Forms
{
    use Arguments;

    /**
     * Fields
     *
     * @since  1.0.0
     * @access private
     *
     * @var array A list of object fields for this form
     */
    private $fields;

    /**
     * Hidden Types
     *
     * @since  1.0.0
     * @access private
     *
     * @var array A list of hidden types for this form
     */
    private $hiddenTypes;

    /**
     * Arguments
     *
     * @since  1.0.0
     * @access private
     *
     * @var array A list of arguments to build this form
     */
    private $args;

    /**
     * Get Extra Attributes
     *
     * @todo   Put in a Trait
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
     * Constructor
     *
     * @since  1.0.0
     * @access public
     *
     * @param array $args The arguments for the current form.
     */
    public function __construct($args)
    {
        $this->fields      = array();
        $this->hiddenTypes = array();
        $this->args        = wp_parse_args($args, [
            'action' => '#',
            'method' => 'get',
            'name'   => 'woozapp_form',
            'attrs'  => array(),
        ]);
    }

    /**
     * To String
     *
     * Return the form in html format
     *
     * @since  1.0.0
     * @access public
     *
     * @return string The current form in html format
     */
    public function __toString()
    {
        return $this->getHtml();
    }

    /**
     * Add Field
     *
     * @since  1.0.0
     * @access public
     *
     * @param Field $field The field to add to this form
     *
     * @return void
     */
    public function addField(Field $field)
    {
        // Get the attribute id value of the input type associated to the field.
        // Since it is unique, we can use it to reference the field within the form.
        $key = $field->getType()->getArg('id');

        // Set the field.
        $this->fields[sanitize_key($key)] = $field;
    }

    /**
     * Add Hidden Type
     *
     * @since  1.0.0
     * @access public
     *
     * @param Type $type The input type to add to this form
     */
    public function addHidden(Type $type)
    {
        if ('hidden' !== $type->getArg('type')) {
            return;
        }

        // Get the attribute id of the input type to use as key.
        $key = $type->getArg('id');

        $this->hiddenTypes[sanitize_key($key)] = $type;
    }

    /**
     * Get Field
     *
     * @since  1.0.0
     * @access public
     *
     * @param string $name The name of the field to retrieve.
     *
     * @return Field|\stdClass The field object requested an stdClass object if the field doesn't exists
     */
    public function getField($name)
    {
        if (! isset($this->fields[$name])) {
            return new \stdClass();
        }

        return $this->fields[$name];
    }

    /**
     * Get Hidden Type
     *
     * @since  1.0.0
     * @access public
     *
     * @param string $name The index key of the hidden type to retrieve.
     *
     * @return Type|\stdClass The hidden type object requested or a \stdClass if the type doesn't exists.
     */
    public function getHiddenType($name)
    {
        if (! isset($this->hiddenTypes[$name])) {
            return new \stdClass();
        }

        return $this->hiddenTypes[$name];
    }

    /**
     * Get Fields
     *
     * @since  1.0.0
     * @access public
     *
     * @return array The fields associated to the current form
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get Hiddens Type
     *
     * @since  1.0.0
     * @access public
     *
     * @return array The hidden type objects associated to the current form
     */
    public function getHiddenTypes()
    {
        return $this->hiddenTypes;
    }

    /**
     * Get Form Nonce
     *
     * @since  1.0.0
     * @access protected
     *
     * @return string The input type hidden for nonce
     */
    public function getNonce()
    {
        return wp_nonce_field(
            $this->getArg('name'),
            $this->getArg('name') . '_nonce',
            true,
            false
        );
    }
}
