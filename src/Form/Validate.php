<?php
namespace Woozapp\Form;

use Woozapp\Form\Interfaces\Fields;
use Woozapp\Form\Interfaces\Forms;

/**
 * Form Validation Type
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
 * Class Validate
 *
 * @todo    Separate class for the Single Responsible Principle.
 *
 * @since   1.0.0
 * @author  Guido Scialfa <dev@guidoscialfa.com>
 */
class Validate
{
    /**
     * Form
     *
     * @since  1.0.0
     * @access private
     *
     * @var Interfaces\Forms The form to validate
     */
    private $form;

    /**
     * Validated Fields Values
     *
     * [
     *      'valid' => array
     *      'invalid' => array
     *      'attachments' => array
     * ]
     *
     * @since  1.0.0
     * @access private
     *
     * @var array A list of validated fields values.
     */
    private $validated;

    /**
     * Construct
     *
     * @todo   Add clean on success submit bool param.
     *
     * @since  1.0.0
     * @access public
     *
     * @param Forms $form The form to validate.
     */
    public function __construct(Forms $form)
    {
        $this->form      = $form;
        $this->validated = [
            'valid'       => array(),
            'invalid'     => array(),
            'attachments' => array(),
        ];
    }

    /**
     * Validate
     *
     * @since  1.0.0
     * @access public
     *
     * @todo   Allow Multiple files.
     * @todo Add check for required fields.
     *
     * @return array All of the valid submitted data. Empty array if the form has no fields.
     */
    public function validate()
    {
        // Get the form fields.
        $fields = $this->form->getFields();
        // Get the hidden types.
        $hiddenTypes = $this->form->getHiddenTypes();

        if (empty($fields)) {
            return array();
        }

        // Set the form input type.
        $formType = constant('INPUT_' . strtoupper($this->form->getArg('method')));

        // Form was not submitted.
        if (! filter_input_array($formType, FILTER_DEFAULT)) {
            return array();
        }

        if (is_admin()) {
            check_admin_referer($this->form->getArg('name'), $this->form->getArg('name') . '_nonce');
        } else {
            // Check for Nonce.
            $nonce = filter_input($formType, $this->form->getArg('name') . '_nonce', FILTER_DEFAULT);
            if (! wp_verify_nonce($this->form->getArg('name'), $nonce)) {
                wp_die();
            }
        }

        $fields = array_merge($fields, $hiddenTypes);

        foreach ($fields as $field) {
            // Get the input type.
            $input = $field instanceof Fields ? $field->getType() : $field;
            // The input type.
            $inputType = strtolower((new \ReflectionClass($input))->getShortName());
            // The input name.
            $inputName = sanitize_key($input->getArg('name'));

            // Different types may need different treatment.
            switch ($inputType) {
                case 'file':
                    if (empty($_FILES)) {
                        break;
                    }

                    try {
                        $handler = new Handlers\Files(
                            $input->getArg('path'),
                            explode(',', $input->getArg('attrs')['accept']),
                            absint($input->getArg('max_file_size'))
                        );

                        // Try to upload the file.
                        $filePath = $handler->uploadFile(reset($_FILES), 'app-icon');
                        // Set the input as valid.
                        $this->validated['valid'][$inputName] = $filePath;
                        // Store the attachment as valid.
                        $this->validated['attachments'][$input->getArg('name')] = $filePath;
                    } catch (\Exception $e) {
                        // Set the input as invalid.
                        $this->validated['invalid'][$inputName] = '';
                        // Set the error description.
                        $field->setArg('invalid_description', $e->getMessage());
                        // Set the class for the field.
                        $field->setArg('container_class', 'is-invalid', true);
                        // Set the type as invalid.
                        $input->setArg('is_invalid', true);
                    }
                    break;

                case 'submit':
                case 'reset':
                case 'button':
                    break;

                default:
                    // @todo Add a try catch.
                    // Get the filter input type.
                    $filter = $input->getArg('filter') ?: FILTER_DEFAULT;
                    // Get the field value from the submitted form.
                    // @todo check for options.
                    $value = filter_input($formType, $input->getArg('name'), $filter);

                    // Set the value in field.
                    // Set before the sanitization, we want to show the user the submitted value not the sanitized one.
                    $input->setArg('attrs', array_merge($input->getArg('attrs'), ['value' => $value]));

                    // Don't validate not required fields when there is no value to sanitize.
                    if (empty($input->getArg('attrs')['required']) && ! $value) {
                        break;
                    }

                    // Sanitize the value.
                    $value = call_user_func($input->getArg('sanitize_cb'), $value);

                    if ($value) {
                        $this->validated['valid'][$inputName] = $value;
                    } else {
                        $this->validated['invalid'][$inputName] = $value;
                        // Set the class for the field.
                        $field->setArg('container_class', 'is-invalid', true);
                        // Set the type as invalid.
                        $input->setArg('is_invalid', true);
                    }
                    break;
            }
        }

        // If there are no invalid fields, let's clean the input fields.
        if (empty($this->validated['invalid'])) {
            $fields = $this->form->getFields();
            array_walk($fields, function ($field) {
                // @todo Improve this.
                $field->getType()->setArg('attrs', array_merge($field->getType()->getArg('attrs'), ['value' => '']));
            });
        }

        return $this->validated;
    }
}
