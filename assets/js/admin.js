/**
 * Admin JavaScript
 *
 * @since 1.0.0
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
;(
    function ($)
    {
        "use strict";

        /**
         * Iframe Source Loader
         *
         * @since 1.0.0
         */
        function iframeSourceLoder()
        {
            // Get the url field reference.
            var $urlField = $('#site_url');

            // Clone the loader.
            $('.main-content .woozapp-loader')
                .clone()
                .appendTo('.site-frame')
                .css({
                    'backgroundColor': 'transparent'
                })
                .children('img')
                .css({
                    maxWidth: '30px'
                });

            // Update iframe src.
            if ($urlField.length) {
                $urlField.on('blur', function ()
                {
                    // Get the Iframe reference.
                    var $iframe = $('iframe#site');

                    $iframe.fadeOut(350, function ()
                    {
                        $('.site-frame .woozapp-loader').fadeIn();

                        var regexp = /^http[s]?:\/\/(www\.|[a-zA-Z0-9]+\.)?[a-zA-Z0-9\\/]+(\.[a-zA-Z0-9]{2,6})([a-zA-z0-9\\/]+)?$/,
                            src = $urlField.val();

                        if (regexp.test(src)) {
                            $.ajax({
                                url: ajaxurl, // @todo Add the default value.
                                data: {
                                    action: 'delegate',
                                    delegate: 'frame',
                                    src: src
                                },
                                method: 'POST',
                                success: function (data)
                                {
                                    $iframe.replaceWith(data);

                                    $('.site-frame .woozapp-loader').delay(300).fadeOut(350, function ()
                                    {
                                        $iframe.fadeIn();
                                    });

                                }
                            });
                        }
                    });
                });
            }
        }

        /**
         * Remove the Invalid Field Description
         *
         * @since 1.0.0
         */
        function removeInvalidFieldDescription()
        {
            var invalidDesc = this.parentElement.getElementsByClassName('woozapp-field-invalid-desc');

            if (invalidDesc.length) {
                Array.prototype.forEach.call(invalidDesc, function (el)
                {
                    el.remove();
                });
            }
        }

        /**
         * Add the Invalid Field Description
         *
         * @since 1.0.0
         */
        function addInvalidFieldDescription(msg, wrap)
        {
            // Get the description of the field.
            var description = this.parentElement.getElementsByClassName('woozapp-field-description')[0];

            // And remove it before add the invalid field description.
            if (typeof description !== 'undefined' && ! wrap) {
                this.parentElement.removeChild(description);
            }

            if (wrap) {
                msg = '<span class="woozapp-field-invalid-desc">' + msg + '</span>';
            }

            // Add the invalid field description at the end of the field wrapper.
            this.parentElement.insertAdjacentHTML('beforeend', msg);
        }

        /**
         * Validate File Input
         *
         * @since 1.0.0
         */
        function checkFileInputImageResolution(file)
        {
            var current = this,
                fileReader = new FileReader();

            // Handler for on load file data.
            fileReader.onload = function ()
            {
                switch (file.type) {
                    case 'image/jpeg':
                    case 'image/png':
                        // Get the Resolution for images as Int values.
                        var dataSize = current.getAttribute('data-size');
                        if (dataSize) {
                            var res = dataSize.split('x').map(function (el)
                            {
                                return parseInt(el)
                            });
                        }

                        var image = new Image();
                        image.src = this.result;
                        image.onload = function ()
                        {
                            if (dataSize) {
                                // Show the Invalid field description if the resolution of the image is
                                // not correct.
                                if (
                                    res[0] < parseInt(this.width) ||
                                    res[1] < parseInt(this.height)
                                ) {
                                    // Add the invalid field description markup.
                                    addInvalidFieldDescription.call(
                                        current,
                                        current.getAttribute('data-wrong-size')
                                    );
                                }
                            }
                        };
                        break;
                    default:
                        break;
                }
            };

            // Read the file data.
            fileReader.readAsDataURL(file);
        }

        /**
         * Validate Inputs
         *
         * @since 1.0.0
         */
        function validateInputs()
        {
            // Get inputs.
            var inputs = document.querySelectorAll('.woozapp-form')[0].elements;

            if (!inputs.length) {
                return false;
            }

            // Add inputs event listener to text the regexp.
            Array.prototype.forEach.call(inputs, function (el)
            {
                var type = el.getAttribute('type');

                // Basic input.
                if (type) {
                    switch (type) {
                        case 'file':
                            el.addEventListener('change', function ()
                            {
                                // The current field.
                                var current = this;

                                if (!current.files.length) {
                                    return false;
                                }

                                // Remove the invalid field description markup.
                                removeInvalidFieldDescription.call(current);

                                // Get the file object.
                                var file = this.files[0];

                                // Get the max file size if exists.
                                var maxSize = parseInt(this.getAttribute('data-max-file-size'));

                                // Insert the file name into the input file wrapper.
                                var fileName = current.parentElement.querySelectorAll('.filename');

                                if (!fileName.length) {
                                    current.parentElement.insertAdjacentHTML(
                                        'afterbegin',
                                        '<span class="filename">' + file.name + '</span>'
                                    );
                                } else {
                                    fileName[0].innerHTML = file.name;
                                }

                                // Check for image resolution.
                                //checkFileInputImageResolution.call(current, file);

                                // Check for file size.
                                if (file.size > maxSize) {
                                    addInvalidFieldDescription.call(this, this.getAttribute('data-wrong-file-size'));
                                }
                            }, false);
                            break;
                        default:
                            el.addEventListener('keyup', function ()
                            {
                                // Input doesn't have a pattern to sanitize the value.
                                if (!this.getAttribute('pattern')) {
                                    return false;
                                }

                                var regExp = new RegExp(this.getAttribute('pattern'));

                                // Try to get the invalid desc element and remove it.
                                removeInvalidFieldDescription.call(this);

                                // Test regExp.
                                if (regExp.test(this.value)) {
                                    this.classList.remove('invalid');
                                    this.classList.add('valid');
                                } else {
                                    // Show the invalid field description.
                                    this.getAttribute('data-invalid') &&
                                    addInvalidFieldDescription.call(this, this.getAttribute('data-invalid'), true);
                                    this.classList.remove('valid');
                                    this.classList.add('invalid');
                                }
                            }, false);
                            break;
                    }
                }
            });
        }

        /**
         * Ajax Submit
         *
         * @since 1.0.0
         */
        function ajaxSubmit()
        {
            // Get the current form page.
            var form = document.querySelector('.woozapp-form');

            if (!form.length) {
                return false;
            }

            form.addEventListener('submit', function (e)
            {
                // Not support for FormData? Submit the form in a traditional way.
                if (typeof window.FormData !== 'undefined') {
                    e.preventDefault();

                    // @todo Submit if FormData doesn't exists.
                    var fd = new FormData(this);

                    // Add Action for WordPress.
                    fd.append('action', 'delegate');
                    // Add Action for Woozapp.
                    fd.append('delegate', form.getAttribute('name'));

                    $.ajax({
                        url: ajaxurl, // @todo Add the default value.
                        data: fd,
                        enctype: 'multipart/form-data',
                        processData: false,  // tell jQuery not to process the data
                        contentType: false,   // tell jQuery not to set contentType
                        method: 'POST',
                        beforeSend: function ()
                        {
                            // Show the loader.
                            $(form).parent().find('.woozapp-loader').stop().fadeIn();
                        },
                        complete: function ()
                        {
                            //$('.woozapp-loader').fadeOut();
                        },
                        success: function (data)
                        {
                            if (!data.success) {
                                // Retrieve the properties and use them as selector for the input.
                                var invalid = data.validated.invalid;
                                for (var p in invalid) {
                                    // Get the dom element.
                                    var el = document.getElementById(p);
                                    // Clean the previous invalid message description.
                                    removeInvalidFieldDescription.call(el);
                                    // And store the msg.
                                    addInvalidFieldDescription.call(el, invalid[p]);
                                }
                            } else {
                                // Select the main content and add the html returned by the server.
                                // Finally add the modifier class needed to stylize correctly the content.
                                var content = document.querySelector('.woozapp-wrapper .row .main-content');
                                content.innerHTML = '';
                                $(content).hide();

                                content.innerHTML = data.msg;
                                $(content).fadeIn(1200);

                                content.classList.add('main-content--success');
                            }

                            $(form).parent().find('.woozapp-loader').fadeOut(1400);
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                        }
                    });
                }
            }, false);
        }

        $(window).ready(function ()
        {
            // iFrame Source Loader.
            iframeSourceLoder();
            // Validate Inputs
            validateInputs();
            // Ajax Submit.
            ajaxSubmit();
        });

    }(jQuery)
);