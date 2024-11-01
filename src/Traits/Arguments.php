<?php
namespace Woozapp\Traits;

/**
 * Trait Arguments
 *
 * @package Woozapp\Traits
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
 * Trait Arguments
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
trait Arguments
{
    /**
     * Get Arguments
     *
     * @since  1.0.0
     * @access public
     *
     * @return array The arguments for this type
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Get Single Argument
     *
     * @since  1.0.0
     * @access public
     *
     * @throws \InvalidArgumentException If the argument trying to retrieve doesn't exists.
     *
     * @param string $name The name of the argument.
     *
     * @return mixed The value of the argument requested
     */
    public function getArg($name)
    {
        if (! array_key_exists($name, $this->args)) {
            throw new \InvalidArgumentException(
                sprintf(esc_html__('The argument %s doesn&#8217;t exists.', 'woozapp'), $name)
            );
        }

        return $this->args[$name];
    }

    /**
     * Set Arguments
     *
     * @since  1.0.0
     * @access public
     *
     * @param array $args The arguments to set.
     *
     * @return void
     */
    public function setArgs($args)
    {
        if (empty($args)) {
            return;
        }

        foreach ($args as $key => $val) {
            $this->setArg($key, $val);
        }
    }

    /**
     * Set Single Argument
     *
     * @todo   Could work with array? For example 'attrs' for input types.
     *
     * @since  1.0.0
     * @access public
     *
     * @throws \InvalidArgumentException If the argument trying to set doesn't exists.
     *
     * @param string $key    The key of the argument.
     * @param mixed  $val    The value to set for the argument.
     * @param bool   $append If the value should be appended to the current one or not.
     *
     * @return void
     */
    public function setArg($key, $val, $append = false)
    {
        // Don't set unknown arguments.
        if (! array_key_exists($key, $this->args)) {
            throw new \InvalidArgumentException(
                sprintf(esc_html__('The argument %s doesn&#8217;t exists.', 'woozapp'), $key)
            );
        }

        if ($append) {
            if (is_array($this->args[$key])) {
                array_push($this->args[$key], $val);
            } elseif (is_string($this->args[$key])) {
                $this->args[$key] .= " {$val}";
            }
        } else {
            $this->args[$key] = $val;
        }
    }
}
