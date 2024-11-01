<?php
namespace Woozapp;

/**
 * Remote Handler
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
 * Class Remote
 *
 * @since   1.1.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Remote
{
    /**
     * URI
     *
     * @since  1.1.0
     * @access private
     *
     * @var string The uri where store the data
     */
    private $uri;

    /**
     * cURL Opts
     *
     * @since  1.1.0
     * @access private
     *
     * @var array A list of cURL options
     */
    private $cURLOpts;

    /**
     * Construct
     *
     * @since   1.1.0
     * @access  public
     *
     * @throws  |Exception if the data is not a valid json string.
     *
     * @param string $uri  The uri where to send the data.
     * @param array  $opts A list of cURL options.
     */
    public function __construct($uri, $opts)
    {
        $this->uri      = $uri;
        $this->cURLOpts = $opts;
    }

    /**
     * Call Uri
     *
     * @since  1.1.0
     * @access public
     *
     * @throws \Exception If the curl exec fail.
     * @throws \InvalidArgumentException If the passed data is not a json or a string convertible to json.
     *
     * @param string $data The json data to send.
     *
     * @return void|mixed The response of the curl exec if CURLOPT_RETURNTRANSFER or null otherwise.
     */
    public function call($data)
    {
        // Be sure the data is a json.
        if (false === Functions\isJSON($data)) {
            // Try to convert into a json.
            $data = wp_json_encode($data);
            // At least throw an error.
            if (! $data) {
                throw new \InvalidArgumentException(sprintf(esc_html__('%s JSON data is not valid.'), __METHOD__));
            }
        }

        // Initialize a Session.
        $cURL = curl_init($this->uri);

        // Add extra opts if sets.
        if ($this->cURLOpts) {
            foreach ($this->cURLOpts as $key => $val) {
                curl_setopt($cURL, constant('CURLOPT_' . $key), $val);
            }
        }

        // Be sure to set the content-type as json
        curl_setopt($cURL, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        // Append the data.
        curl_setopt($cURL, CURLOPT_POSTFIELDS, $data);

        // Retrieve the response.
        $response = curl_exec($cURL);

        // May be something goes wrong?
        if (curl_errno($cURL)) {
            throw new \Exception(curl_error($cURL));
        }

        // Close the session.
        curl_close($cURL);

        // Need to return something?
        if (isset($this->cURLOpts['RETURNTRANSFER']) && $this->cURLOpts['RETURNTRANSFER']) {
            return $response;
        }
    }
}
