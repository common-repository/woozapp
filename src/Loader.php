<?php
namespace Woozapp;

/**
 * Loader File
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
 * Class Register
 *
 * @since  1.0.0
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
final class Loader
{
    /**
     * Actions
     *
     * The actions hooks
     *
     * @since  1.0.0
     * @access private
     *
     * @var array A list of actions
     */
    private $actions;

    /**
     * Filters
     *
     * The filters hooks
     *
     * @since  1.0.0
     * @access private
     *
     * @var array A list of filters
     */
    private $filters;

    /**
     * Construct
     *
     * @since  1.0.0
     * @access private
     */
    private function __construct()
    {
        $this->actions = array();
        $this->filters = array();
    }

    /**
     * Clean the Collection
     *
     * Avoid add same filters multiple times.
     *
     * @since  1.0.0
     * @access private
     *
     * @return void
     */
    private function clean()
    {
        $this->filters = array();
        $this->actions = array();
    }

    /**
     * Add Filters / Actions
     *
     * @param array  $filters The filters to add.
     * @param string $func    The function to call. 'add_action' or 'add_filter'
     *
     * @throws \ErrorException If the $func parameter is not callable
     *
     * @return void
     */
    private function add($filters, $func)
    {
        if (! is_callable($func)) {
            throw new \ErrorException($func . __(' must be callable in ', 'woozapp') . __CLASS__ . '::' . __METHOD__);
        }

        foreach ($filters as $args) {
            $priority     = isset($args['priority']) ? absint($args['priority']) : 20;
            $acceptedArgs = isset($args['accepted_args']) ? absint($args['accepted_args']) : 1;

            $func($args['filter'], $args['callback'], $priority, $acceptedArgs);
        }
    }

    /**
     * Prevent Cloning
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     */
    public function __clone()
    {
        trigger_error(esc_attr__('Cheatin&#8217; huh?', 'woozapp'), E_USER_ERROR);
    }

    /**
     * Get Instance
     *
     * @since  1.0.0
     * @access static
     *
     * @return Loader The instance of the class
     */
    public static function getInstance()
    {
        static $instance = null;

        if (null === $instance) {
            $instance = new self;
        }

        return $instance;
    }

    /**
     * Add Filters
     *
     * Ex.
     * 'context' => [
     *      'type' => [
     *          [
     *              'filter'             => 'filter_name',
     *              'callback'           => theCallback,
     *              'priority'           => N,
     *              'accepted_arguments' => N
     *          ],
     *      ],
     * ],
     *
     * @since  1.0.0
     * @access public
     *
     * @param array $haystack The list of filters and their arguments.
     *
     * @return Loader The instance of chaining
     */
    public function addFilters(array $haystack)
    {
        foreach ($haystack as $context => $filters) {
            foreach ($filters as $type => $args) {
                foreach ($args as $arg) {
                    $method = 'add' . ucfirst($type);
                    $this->{$method}($context, $arg);
                }
            }
        }

        return $this;
    }

    /**
     * Add Action
     *
     * @since  1.0.0
     * @access public
     *
     * @param string $context The context of the action. Allowed 'admin', 'front'.
     * @param string $args    Arguments.
     *
     * @return void
     */
    public function addAction($context, $args)
    {
        $this->actions[$context][] = $args;
    }

    /**
     * Add Filter
     *
     * @since  1.0.0
     * @access public
     *
     * @param string $context The context of the filter. Allowed 'admin', 'front'.
     * @param string $args    Arguments.
     *
     * @return void
     */
    public function addFilter($context, $args)
    {
        $this->filters[$context][] = $args;
    }

    /**
     * Add Filters based on context
     *
     * @since  1.0.0
     * @access public
     *
     * @throws \ErrorException If the $func parameter is not callable
     *
     * @return void
     */
    public function load()
    {
        // Add the shared filters.
        if (! empty($this->actions['inc'])) {
            $this->add($this->actions['inc'], 'add_action');
        }

        if (! empty($this->filters['inc'])) {
            $this->add($this->filters['inc'], 'add_action');
        }

        // Get the context of filters.
        $context = is_admin() ? 'admin' : 'front';

        // Add the filters.
        if (! empty($this->actions[$context])) {
            $this->add($this->actions[$context], 'add_action');
        }

        if (! empty($this->filters[$context])) {
            $this->add($this->filters[$context], 'add_filter');
        }

        // Be sure to clean the collection to prevent add filters multiple time.
        $this->clean();
    }
}
