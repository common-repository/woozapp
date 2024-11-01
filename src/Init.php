<?php
namespace Woozapp;

/**
 * Init File
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
 * Class Init
 *
 * @since  1.0.0
 * @author Guido Scialfa <dev@guidoscialfa.com>
 */
final class Init
{
    /**
     * Loader
     *
     * @since  1.0.0
     * @access private
     *
     * @var Loader The register instance
     */
    private $loader;

    /**
     * Filters
     *
     * @since  1.0.0
     * @access private
     *
     * @var array A list of filters and their callbacks
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
        // Get the Register.
        $this->loader = Loader::getInstance();

        // Get the filters based on context.
        if (is_admin()) {
            $filters = Admin\Filters::getFilters();
        } else {
            $filters = Front\Filters::getFilters();
        }

        $this->filters = array_merge(Filters::getFilters(), $filters);
    }

    /**
     * Load Dependencies
     *
     * @since  1.0.0
     * @access private
     *
     * @return void
     */
    private function loadDependencies()
    {
        // Utils Functions.
        require_once Plugin::getPluginDirPath() . '/src/functions/utils.php';

        // Kses Custom Functions.
        require_once Plugin::getPluginDirPath() . '/src/functions/kses.php';

        // Template Functions.
        require_once Plugin::getPluginDirPath() . '/src/functions/templates.php';
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
        trigger_error(esc_html__('Cheatin&#8217; huh?', 'woozapp'), E_USER_ERROR);
    }

    /**
     * Get Instance
     *
     * @since  1.0.0
     * @access static
     *
     * @return Init The instance of the class
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
     * Init
     *
     * @since  1.0.0
     * @access public
     *
     * @return void
     */
    public function init()
    {
        // Require base libraries.
        $this->loadDependencies();

        // Add Filters.
        $this->loader
            ->addFilters($this->filters)
            ->load();
    }
}
