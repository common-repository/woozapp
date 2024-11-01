<?php
namespace Woozapp\Functions;

/**
 * Kses Functions
 *
 * @package Woozapp\Functions
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
 * Sanitize content for allowed HTML tags for post content.
 *
 * Post content refers to the page contents of the 'post' type and not $_POST
 * data from forms.
 *
 * @todo  Remove if the issue will be fixed. See below.
 *
 * @see   https://core.trac.wordpress.org/ticket/37085
 *
 * @since 1.0.0
 *
 * @param string $data Post content to filter
 *
 * @return string Filtered post content with allowed HTML tags and attributes intact.
 */
function kses_post($data)
{
    global $allowedposttags;

    $_allowedposttags = array_merge($allowedposttags, [
        'input' => [
            'data-invalid'         => true,
            'data-max-file-size'   => true,
            'data-wrong-file-size' => true,
            'data-wrong-size'      => true,
            'data-size'            => true,
            'accept'               => true,
            'autocomplete'         => true, // @todo add the allowed values list.
            'autofocus'            => true,
            'checked'              => true,
            'class'                => true,
            'disabled'             => true,
            'id'                   => true,
            'height'               => true,
            'min'                  => true,
            'max'                  => true,
            'minlenght'            => true,
            'maxlength'            => true,
            'name'                 => true,
            'pattern'              => true,
            'placeholder'          => true,
            'readony'              => true,
            'required'             => true,
            'size'                 => true,
            'src'                  => true,
            'step'                 => true,
            'type'                 => true,
            'value'                => true,
            'width'                => true,
        ],
    ]);

    // Form attributes.
    $_allowedposttags['form'] = array_merge($_allowedposttags['form'], ['novalidate' => true]);

    return wp_kses($data, $_allowedposttags);
}
