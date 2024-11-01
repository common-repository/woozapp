<?php
namespace Woozapp;

/**
 * Shared Filters
 *
 * @package Woozapp\Admin
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
 * Class Filters
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Filters
{
    /**
     * Get Filters
     *
     * @since  1.0.0
     * @access static
     *
     * @return array A list of shared filters
     */
    public static function getFilters()
    {
        return [
            'inc' => [
                'action' => [
                    [
                        'filter'   => 'init',
                        'callback' => [__NAMESPACE__ . '\\EndPoints\\Dispatcher', 'register'],
                        'priority' => 20,
                    ],
                    [
                        'filter'   => 'template_redirect',
                        'callback' => [__NAMESPACE__ . '\\EndPoints\\Dispatcher', 'dispatch'],
                        'priority' => 20,
                    ],
                ],
            ],
        ];
    }
}
