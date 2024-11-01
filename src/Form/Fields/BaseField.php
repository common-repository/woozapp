<?php
namespace Woozapp\Form\Fields;

use Woozapp\Form\Abstracts\Field;

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
class BaseField extends Field
{
    /**
     * Get Field Html
     *
     * @since  1.0.0
     * @access public
     *
     * @return string The html version of the field
     */
    public function getHtml()
    {
        // Set the container class
        $containerClass = array_map('sanitize_html_class', $this->getArg('container_class'));

        // Open container.
        $output = sprintf('<%1$s class="%2$s">', tag_escape($this->getArg('container')), implode(' ', $containerClass));

        // Get the field label.
        $output .= $this->argCb('before_label') . $this->getLabel();
        // Get the input type.
        $output .= $this->argCb('before_input') . $this->getType() . $this->argCb('after_input');
        // Get the field description.
        $output .= $this->getDescription();

        // Close the container.
        $output .= '</' . tag_escape($this->getArg('container')) . '>';

        return $output;
    }
}
