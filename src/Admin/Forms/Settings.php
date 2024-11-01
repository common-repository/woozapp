<?php
namespace Woozapp\Admin\Forms;

use \Woozapp\Functions;
use \Woozapp\Plugin;
use \Woozapp\Remote;
use \Woozapp\Form;
use \Woozapp\Mail;
use \Woozapp\AlertBox;
use \Woozapp\Logger;

/**
 * Admin Form Settings
 *
 * @package Woozapp\Admin\Pages
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
 * Class Settings
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
final class Settings
{
    /**
     * Send App Request
     *
     * Send a request to the woozapp server, to be processed by their servers.
     *
     * @since  1.1.0
     * @access private
     *
     * @throws \Exception Whatever exception thrown the call method.
     *
     * @param $data
     *
     * @return mixed|void The response of the server.
     */
    private static function storeRemoteRequest($data)
    {
        // Retrieve only the necessary data.
        $data = [
            'email'        => $data['user_email'],
            'settings_url' => home_url('/?woozapp=1&subpoint=settings'),
        ];

        $remote = new Remote(
            'http://demo.woozapp.com/?woozapp=1&handler=push',
            [
                'HEADER'         => false,
                'RETURNTRANSFER' => true,
                'POST'           => true,
                'SSL_VERIFYPEER' => false,
            ]
        );

        // Send data.
        return $remote->call($data);
    }

    /**
     * Form
     *
     * @since  1.0.0
     * @access private
     *
     * @return Form\Forms\BaseForm The form instance
     */
    public static function form()
    {
        static $form = null;

        if (null !== $form) {
            return $form;
        }

        // Retrieve the Settings.
        $settings = \Woozapp\Settings::getInstance()->get();

        $form = new Form\Forms\BaseForm([
            'action' => esc_url(admin_url('admin.php?page=woozapp')),
            'method' => 'post',
            'name'   => 'woozapp_form_settings',
            'attrs'  => [
                'id'         => 'woozapp_form_settings',
                'class'      => 'woozapp-form',
                'enctype'    => 'multipart/form-data',
                'novalidate' => 'novalidate',
            ],
        ]);

        $fields = [

            // App Name Field.
            new Form\Fields\BaseField(
                new Form\Types\Text([
                    'name'  => 'app_name',
                    'attrs' => [
                        'pattern'      => '^([a-zA-Z0-9\s]+)$',
                        'placeholder'  => esc_html__('App Name', 'woozapp'),
                        'required'     => 'required',
                        'data-invalid' => esc_html__('This field is mandatory', 'woozapp'),
                    ],
                ]),
                [
                    'container_class'     => [
                        'woozapp-field',
                        'woozapp-field-text',
                    ],
                    'label'               => esc_html__('App Name', 'woozapp'),
                    'invalid_description' => esc_html__('Only letters, numbers and spaces.', 'woozapp'),
                    'after_input'         => function ($field) {
                        $msg = '';
                        if ($field->getType()->getArg('is_invalid')) {
                            $msg = '<span class="woozapp-field-invalid-desc">' .
                                   $field->getArg('invalid_description') .
                                   '</span>';
                        }

                        return $msg;
                    },
                ]
            ),

            // App Icon Field.
            new Form\Fields\BaseField(
                new Form\Types\File([
                    'name'          => 'app_icon',
                    'max_file_size' => 2097152,
                    'path'          => '/woozapp/icon/',
//                    'resolution'    => [1024, 1024],
                    'attrs'         => [
                        'accept'               => 'image/png',
                        //'data-size'            => '1024x1024',
                        'data-max-file-size'   => 2097152,
                        'data-wrong-file-size' => esc_html__('File exceeded max size.', 'woozapp'),
                        'data-wrong-size'      => esc_html__('File must be squared, min 128px.', 'woozapp'),
                    ],
                ]),
                [
                    'container_class'     => [
                        'woozapp-field',
                        'woozapp-field-file',
                    ],
                    'label'               => esc_html__('App Icon', 'woozapp'),
                    'invalid_description' => esc_html__('Something went wrong during upload the file.', 'woozapp'),
                    'before_input'        => function () {
                        return '<span>' . esc_html__('(Must be squared, min 128px)', 'woozapp') .
                               '</span><div class="file-type-wrapper">';
                    },
                    'after_input'         => function ($field) {
                        $msg = '</div>';
                        if ($field->getType()->getArg('is_invalid')) {
                            $msg .= '<span class="woozapp-field-invalid-desc">' .
                                    $field->getArg('invalid_description') .
                                    '</span>';
                        }

                        return $msg;
                    },
                ]
            ),

            // Site Url.
            new Form\Fields\BaseField(
                new Form\Types\Url([
                    'name'  => 'site_url',
                    'attrs' => [
                        'placeholder'  => esc_html__('http://', 'woozapp'),
                        'required'     => 'required',
                        'pattern'      => '^http[s]?:\/\/(www\.|[a-zA-Z0-9]+\.)?[a-zA-Z0-9\\/]+(\.[a-zA-Z0-9]{2,6})([a-zA-z0-9\\/]+)?$',
                        'value'        => (isset($settings['site_url']) ? $settings['site_url'] : home_url()),
                        'data-invalid' => esc_html__('The url must be like http://www.domain.com.', 'woozapp'),
                    ],
                ]),
                [
                    'container_class'     => [
                        'woozapp-field',
                        'woozapp-field-url',
                    ],
                    'label'               => esc_html__('Site Url', 'woozapp'),
                    'invalid_description' => esc_html__('The url must be like http://www.domain.com.', 'woozapp'),
                    'after_input'         => function ($field) {
                        $msg = '';
                        if ($field->getType()->getArg('is_invalid')) {
                            $msg = '<span class="woozapp-field-invalid-desc">' .
                                   $field->getArg('invalid_description') .
                                   '</span>';
                        }

                        return $msg;
                    },
                ]
            ),

            // Email.
            new Form\Fields\BaseField(
                new Form\Types\Email([
                    'name'  => 'user_email',
                    'attrs' => [
                        'pattern'      => '^[a-zA-Z0-9\.\!\#\$\%\&\’\*\+\/=\?^_`{|}~-]+\@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-]{2,6}$',
                        'required'     => 'required',
                        'data-invalid' => esc_html__('This is not a valid email address.', 'woozapp'),
                    ],
                ]),
                [
                    'container_class'     => [
                        'woozapp-field',
                        'woozapp-field-email',
                    ],
                    'label'               => esc_html__('Your Email', 'woozapp'),
                    'invalid_description' => esc_html__('This is not a valid email address.', 'woozapp'),
                    'after_input'         => function ($field) {
                        $msg = '';
                        if ($field->getType()->getArg('is_invalid')) {
                            $msg = '<span class="woozapp-field-invalid-desc">' .
                                   $field->getArg('invalid_description') .
                                   '</span>';
                        }

                        return '<p class="woozapp-field-description">' . esc_html__(
                            'The email will be used to send you the app file. Be sure to add a valid email',
                            'woozapp'
                        ) . '</p>' . $msg;
                    },
                ]
            ),

            // Submit.
            new Form\Fields\BaseField(
                new Form\Types\Submit([
                    'name'  => 'submit',
                    'value' => esc_html__('Get the android Demo', 'woozapp'),
                    'attrs' => [
                        'class' => 'woozapp-field__type--text',
                    ],
                ]),
                [
                    'container_class' => [
                        'woozapp-field',
                        'woozapp-field-submit',
                        'woozapp-actions',
                        'u-text--big',
                    ],
                    'after_input'     => function () {
                        return esc_html__('or', 'woozapp') .
                               ' <a class="woozapp-btn woozapp-btn--normal" href="http://www.woozapp.com">' .
                               esc_html__('buy here', 'woozapp') .
                               '</a>';
                    },
                ]
            ),
        ];

        foreach ($fields as $field) {
            $form->addField($field);
        }

        // Hidden Fields.
        $hiddens = [
            new Form\Types\Hidden([
                'name'   => 'adddevice_endpoint',
                'filter' => FILTER_SANITIZE_URL,
                'attrs'  => [
                    'value' => untrailingslashit(home_url('/')) . '?woozapp=1&subpoint=adddevice',
                ],
            ]),
        ];

        foreach ($hiddens as $type) {
            $form->addHidden($type);
        }

        return $form;
    }

    /**
     * Handle Settings
     *
     * @since  1.0.0
     * @access public
     *
     * @return object An object containing the info of the form handle
     */
    public static function handle()
    {
        // Store into a variable, we use it multiple times.
        $doingAjax = defined('DOING_AJAX') && DOING_AJAX;

        // Build/Retrieve the form.
        $form = self::form();

        // Validate.
        $validator = new Form\Validate($form);
        $validated = $validator->validate();

        $success = false;
        $msg     = '';

        // Save the Settings if needed.
        if (empty($validated['invalid']) && ! empty($validated['valid'])) {
            // Convert file dir to url.
            if (! empty($validated['attachments'])) {
                foreach ($validated['attachments'] as &$item) {
                    $item = Functions\switchUploadDirPathUrl($item, 'dir>url');
                }
            }

            try {
                $settings = \Woozapp\Settings::getInstance();
                $data     = array_merge($validated['valid'], $validated['attachments']);

                if ($settings->store($data)) {
                    $success = true;

                    if ($doingAjax) {
                        // Template Data.
                        /** @noinspection PhpUnusedLocalVariableInspection */
                        $data = (object)[
                            'doingAjax' => $doingAjax,
                        ];

                        ob_start();
                        require_once Plugin::getPluginDirPath() . '/views/admin/settingsPageSuccess.php';
                        $msg = ob_get_clean();
                    } else {
                        $msg = esc_html__('Settings has been saved correctly.', 'woozapp');
                    }

                    // Store the request to woozapp.com.
                    $response = self::storeRemoteRequest($settings->get());

                    if (! $response) {
                        throw new \Exception(sprintf(
                            esc_html__('%s Data. Not stored in server. Server response: %s', 'woozapp'),
                            $settings->get(),
                            $response
                        ));
                    }

                    // Then send the email.
                    (new Mail(
                        esc_html__('New Demo App Request', 'woozapp'),
                        Plugin::getPluginDirPath() . '/views/admin/mailNewDemo.php',
                        'settings'
                    ))->send();
                }
            } catch (\Exception $e) {
                $success = false;
                $msg     = $e->getMessage();
                (new Logger())->log($e);
            }
        } else {
            $msg = esc_html__('Some fields are not valid. Please check them.', 'woozapp');

            // Insert the message instead of the value to the invalid element when work with ajax.
            if ($doingAjax) {
                foreach ($validated['invalid'] as $name => &$el) {
                    $el = call_user_func($form->getField($name)->getArg('after_input'), $form->getField($name));
                }

                // Build the alert box markup.
                $msg = (new AlertBox($msg, 'error'))->alertBox();
            }
        }

        return (object)compact('success', 'msg', 'validated');
    }
}
