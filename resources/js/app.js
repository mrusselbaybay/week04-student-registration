document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registration-form');

    if (!form) {
        return;
    }

    const steps = Array.from(form.querySelectorAll('[data-step]'));
    const navItems = Array.from(document.querySelectorAll('[data-step-nav]'));
    const backButton = form.querySelector('[data-action="back"]');
    const nextButton = form.querySelector('[data-action="next"]');
    const submitButton = form.querySelector('[data-action="submit"]');

    const maskers = {
        student_number: (value) => {
            const digits = value.replace(/\D/g, '').slice(0, 8);

            return digits.length > 4 ? `${digits.slice(0, 4)}-${digits.slice(4)}` : digits;
        },
        mobile_number: (value) => value.replace(/\D/g, '').slice(0, 11),
        first_name: (value) => value.replace(/[^\p{L}\s'-]/gu, ''),
        middle_name: (value) => value.replace(/[^\p{L}\s'-]/gu, ''),
        last_name: (value) => value.replace(/[^\p{L}\s'-]/gu, ''),
    };

    const maskWarnings = {
        student_number: 'Student number can only contain digits (format 0000-0000).',
        mobile_number: 'Mobile number can only contain digits.',
        first_name: 'Names can only contain letters.',
        middle_name: 'Names can only contain letters.',
        last_name: 'Names can only contain letters.',
    };

    const showFieldWarning = (input, message, { persist = false } = {}) => {
        let warning = input.parentElement.querySelector('.js-field-warning');

        if (!warning) {
            warning = document.createElement('p');
            warning.className = 'js-field-warning mt-1 text-xs font-medium text-amber-600 dark:text-amber-400';
            input.insertAdjacentElement('afterend', warning);
        }

        warning.textContent = message;
        warning.classList.remove('hidden');
        clearTimeout(warning.hideTimeout);

        if (!persist) {
            warning.hideTimeout = setTimeout(() => warning.classList.add('hidden'), 2500);
        }
    };

    const clearFieldWarning = (input) => {
        const warning = input.parentElement.querySelector('.js-field-warning');

        if (warning) {
            warning.classList.add('hidden');
        }
    };

    const invalidMessage = (input) => {
        if (input.validity.valueMissing) {
            return 'This field is required.';
        }

        if (input.validity.rangeOverflow && input.name === 'date_of_birth') {
            return 'Date of birth cannot be in the future.';
        }

        if (input.validity.patternMismatch) {
            return maskWarnings[input.name] || 'Please match the requested format.';
        }

        if (input.validity.typeMismatch && input.type === 'email') {
            return 'Please enter a valid email address.';
        }

        return input.validationMessage || 'Please check this field.';
    };

    const markInvalid = (input) => {
        input.classList.add('border-red-400', 'dark:border-red-500');
        input.classList.remove('border-gray-300', 'dark:border-gray-600');
        showFieldWarning(input, invalidMessage(input), { persist: true });
    };

    const markValid = (input) => {
        input.classList.remove('border-red-400', 'dark:border-red-500');
        input.classList.add('border-gray-300', 'dark:border-gray-600');
        clearFieldWarning(input);
    };

    form.querySelectorAll('input, select, textarea').forEach((input) => {
        input.addEventListener('invalid', (event) => {
            event.preventDefault();
            markInvalid(input);
        });

        input.addEventListener('input', () => {
            const masker = maskers[input.name];

            if (masker) {
                const masked = masker(input.value);

                if (masked !== input.value) {
                    showFieldWarning(input, maskWarnings[input.name]);
                }

                input.value = masked;
            }

            if (input.checkValidity()) {
                markValid(input);
            }
        });
    });

    let current = 0;

    const stepIndexOfFirstError = () => {
        const invalidField = form.querySelector('[data-server-error="true"]');

        if (!invalidField) {
            return 0;
        }

        const step = invalidField.closest('[data-step]');

        return step ? steps.indexOf(step) : 0;
    };

    const reviewFields = Array.from(document.querySelectorAll('[data-review]'));

    const formatReviewValue = (input) => {
        if (!input) {
            return '—';
        }

        if (input.tagName === 'SELECT') {
            return input.selectedOptions[0]?.textContent.trim() || '—';
        }

        if (input.type === 'date' && input.value) {
            return new Date(`${input.value}T00:00:00`).toLocaleDateString(undefined, {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        }

        return input.value.trim() || '—';
    };

    const populateReview = () => {
        reviewFields.forEach((el) => {
            const input = form.elements.namedItem(el.dataset.review);
            el.textContent = formatReviewValue(input);
        });
    };

    const updateNav = () => {
        navItems.forEach((item, index) => {
            const circle = item.querySelector('[data-step-circle]');
            item.classList.toggle('opacity-100', index <= current);
            item.classList.toggle('opacity-50', index > current);
            circle.classList.remove('bg-blue-600', 'text-white', 'bg-emerald-600', 'bg-gray-200', 'text-gray-500', 'dark:bg-gray-700', 'dark:text-gray-400');

            if (index < current) {
                circle.classList.add('bg-emerald-600', 'text-white');
                circle.textContent = '✓';
            } else if (index === current) {
                circle.classList.add('bg-blue-600', 'text-white');
                circle.textContent = String(index + 1);
            } else {
                circle.classList.add('bg-gray-200', 'text-gray-500', 'dark:bg-gray-700', 'dark:text-gray-400');
                circle.textContent = String(index + 1);
            }
        });
    };

    const showStep = (index) => {
        current = Math.max(0, Math.min(index, steps.length - 1));
        steps.forEach((step, i) => step.classList.toggle('hidden', i !== current));
        backButton.classList.toggle('hidden', current === 0);
        nextButton.classList.toggle('hidden', current === steps.length - 1);
        submitButton.classList.toggle('hidden', current !== steps.length - 1);

        if (current === steps.length - 1) {
            populateReview();
        }

        updateNav();
        steps[current].scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const validateStep = (index) => {
        const inputs = Array.from(steps[index].querySelectorAll('input, select, textarea'));
        let firstInvalid = null;

        inputs.forEach((input) => {
            if (!input.checkValidity()) {
                markInvalid(input);
                firstInvalid = firstInvalid || input;
            }
        });

        if (firstInvalid) {
            firstInvalid.focus();

            return false;
        }

        return true;
    };

    nextButton.addEventListener('click', () => {
        if (validateStep(current)) {
            showStep(current + 1);
        }
    });

    backButton.addEventListener('click', () => showStep(current - 1));

    navItems.forEach((item, index) => {
        item.addEventListener('click', () => {
            if (index <= current) {
                showStep(index);
            }
        });
    });

    showStep(stepIndexOfFirstError());
});
