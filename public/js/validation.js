$(document).ready(function() {
    // Escape utility for jQuery selector strings
    function escapeId(id) {
        return id.replace(/(:|\.|\[|\]|,|=|@)/g, "\\$1");
    }

    // Identify if a field is required dynamically
    function isFieldRequired(field) {
        return $(field).prop('required') || typeof $(field).attr('required') !== 'undefined';
    }

    function cleanLabelText(text) {
        if (!text) return '';
        text = text.replace(/<[^>]*>/g, '');
        text = text.replace(/\*/g, '');
        text = text.replace(/^(please\s+)?(enter|select|input|choose|write|fill|type)\s+/i, '');
        text = text.replace(/:\s*$/, '');
        text = text.replace(/\s+/g, ' ').trim();
        return text.replace(/\b\w/g, function(l){ return l.toUpperCase(); });
    }

    function getLabelText(field) {
        var labelText = '';
        var $field = $(field);
        var id = $field.attr('id');
        
        if (id) {
            try {
                var $label = $('label[for="' + escapeId(id) + '"]');
                if ($label.length) {
                    labelText = $label.text();
                }
            } catch(e) {}
        }
        if (!labelText) {
            var $parentLabel = $field.closest('label');
            if ($parentLabel.length) {
                labelText = $parentLabel.text();
            }
        }
        if (!labelText && $field.attr('placeholder')) {
            labelText = $field.attr('placeholder');
        }
        if (!labelText) {
            var name = $field.attr('name') || 'This field';
            labelText = name.replace(/_|-/g, ' ');
        }
        return cleanLabelText(labelText);
    }

    function generateValidationMessage(field, labelText) {
        var $field = $(field);
        var name = ($field.attr('name') || '').toLowerCase();
        var type = ($field.attr('type') || '').toLowerCase();
        
        if (name.includes('mobile') || name.includes('phone') || name.includes('contact')) {
            return 'Please enter a valid 10-digit Mobile Number.';
        }
        if (type === 'email' || name.includes('email')) {
            return 'Please enter a valid Email Address.';
        }
        if (name.includes('gst')) {
            return 'Please enter the GST Number.';
        }
        if (name.includes('pan')) {
            return 'Please enter the PAN Number.';
        }
        if (name.includes('aadhaar') || name.includes('adhaar')) {
            return 'Please enter the Aadhaar Number.';
        }
        if (type === 'file') {
            if (name.includes('image') || name.includes('photo') || name.includes('logo') || name.includes('thumb')) {
                return 'Please select an image.';
            }
            return 'Please select a file.';
        }

        if ($field.is('select') || type === 'date' || name.includes('status') || name.includes('state') || name.includes('country') || name.includes('mode') || name.includes('date') || name.includes('terms') || name.includes('type')) {
            return 'Please select the ' + labelText + '.';
        }

        return 'Please enter the ' + labelText + '.';
    }

    function validateField(field) {
        var $field = $(field);
        if ($field.is(':submit') || $field.is(':button') || $field.attr('type') === 'hidden') {
            return true;
        }

        var valid = true;
        var errorMsg = '';
        var val = $.trim($field.val());
        var labelText = getLabelText(field);

        if (isFieldRequired(field)) {
            if ($field.is(':checkbox')) {
                if (!$field.is(':checked')) {
                    valid = false;
                    errorMsg = generateValidationMessage(field, labelText);
                }
            } else if ($field.is(':radio')) {
                var name = $field.attr('name');
                if (name) {
                    var $group = $('input[type="radio"][name="' + name + '"]');
                    if (!$group.is(':checked')) {
                        valid = false;
                        errorMsg = 'Please select a value.';
                    }
                }
            } else {
                if (!val) {
                    valid = false;
                    errorMsg = generateValidationMessage(field, labelText);
                }
            }
        }

        applyValidationUI(field, valid, errorMsg);
        return valid;
    }

    function applyValidationUI(field, valid, errorMsg) {
        var $field = $(field);
        var $parent = $field.parent();
        
        if (valid) {
            $field.removeClass('is-invalid');
            var $errEl = $parent.find('.dw-invalid-feedback');
            if ($errEl.length) {
                $errEl.removeClass('show');
                setTimeout(function() {
                    $errEl.remove();
                }, 150);
            }
        } else {
            $field.addClass('is-invalid');
            var $errEl = $parent.find('.dw-invalid-feedback');
            if (!$errEl.length) {
                $errEl = $('<div class="dw-invalid-feedback"></div>');
                $parent.append($errEl);
            }
            $errEl.text(errorMsg);
            // Trigger reflow
            $errEl[0].offsetHeight;
            $errEl.addClass('show');
        }
    }

    function clearBackendError(field) {
        var $field = $(field);
        $field.removeClass('is-invalid');
        $field.parent().find('.text-error, .invalid-feedback').remove();
    }

    // Set novalidate to prevent browser bubble on all forms except logout and search
    $('form').each(function() {
        var $form = $(this);
        if (!$form.hasClass('logout-form') && !$form.hasClass('search-form') && !$form.attr('novalidate-exempt')) {
            $form.attr('novalidate', '');
        }
    });

    // Event delegation for real-time validation
    $(document).on('input keyup change focusout', 'input, select, textarea', function(e) {
        var $form = $(this).closest('form');
        if (!$form.length || $form.hasClass('logout-form') || $form.hasClass('search-form') || $form.attr('novalidate-exempt')) {
            return;
        }
        clearBackendError(this);
        validateField(this);
    });

    $(document).on('focusin', 'input, select, textarea', function() {
        clearBackendError(this);
    });

    // Form submit listener
    $(document).on('submit', 'form', function(e) {
        var $form = $(this);
        if ($form.hasClass('logout-form') || $form.hasClass('search-form') || $form.attr('novalidate-exempt')) {
            return;
        }

        var isValid = true;
        var firstInvalidField = null;

        $form.find('input, select, textarea').each(function() {
            clearBackendError(this);
            if (!validateField(this)) {
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = this;
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
            if (firstInvalidField) {
                $('html, body').animate({
                    scrollTop: $(firstInvalidField).offset().top - 100
                }, 300);
                firstInvalidField.focus();
            }
        } else {
            var $submitBtn = $form.find('button[type="submit"], input[type="submit"]');
            if ($submitBtn.length) {
                $submitBtn.prop('disabled', true);
                var originalHTML = $submitBtn.html();
                $submitBtn.attr('data-original-html', originalHTML);
                $submitBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');
            }
        }
    });
});
