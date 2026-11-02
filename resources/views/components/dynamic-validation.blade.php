<!-- Global Real-Time Dynamic Form Validation Engine (Tailwind + Alpine friendly) -->
<style>
    @keyframes dynamicErrorFadeIn {
        from { opacity: 0; transform: translateY(-3px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .dynamic-field-error {
        animation: dynamicErrorFadeIn 0.18s ease-out forwards;
    }
    .has-dynamic-error {
        border-color: #F43F5E !important; /* Rose-500 */
        box-shadow: 0 0 0 1px #F43F5E !important;
    }
    .has-dynamic-error:focus {
        border-color: #E11D48 !important; /* Rose-600 */
        box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.25) !important;
    }
    .is-dynamic-valid {
        border-color: #10B981 !important; /* Emerald-500 */
    }
</style>

<script>
(function() {
    'use strict';

    // Regex Rules
    const NAME_REGEX = /^[\p{L}\s\-\.\'\u2019]+$/u;
    const USERNAME_REGEX = /^[a-zA-Z0-9_\-]+$/;
    const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Helper: Normalize contact numbers
    function cleanContactNumber(val) {
        if (!val) return '';
        let cleaned = String(val).replace(/[^\d+]/g, '').trim();
        if (cleaned.startsWith('+63')) {
            cleaned = '0' + cleaned.substring(3);
        } else if (cleaned.startsWith('63') && cleaned.length === 12) {
            cleaned = '0' + cleaned.substring(2);
        }
        return cleaned;
    }

    // Helper: Section minimum age mapping for SPED
    function getSectionMinAge(className) {
        if (!className) return 2;
        const lower = String(className).toLowerCase();
        if (lower.includes('playgroup')) return 2;
        if (lower.includes('kinder')) return 4;
        if (/grade\s*1\b/i.test(lower)) return 5;
        if (/grade\s*2\b/i.test(lower)) return 6;
        if (/grade\s*3\b/i.test(lower)) return 7;
        if (/grade\s*4\b/i.test(lower)) return 8;
        if (/grade\s*5\b/i.test(lower)) return 9;
        if (/grade\s*6\b/i.test(lower)) return 10;
        return 2;
    }

    // Core field validator: returns error string or null
    function evaluateInput(input, trigger = 'input') {
        // Skip ignored inputs
        if (!input || input.disabled || input.readOnly || input.type === 'hidden' || input.type === 'submit' || input.type === 'button') {
            return null;
        }

        const name = (input.name || '').toLowerCase();
        const id = (input.id || '').toLowerCase();
        const type = (input.type || '').toLowerCase();
        const rawVal = input.value || '';
        const val = rawVal.trim();
        const isRequired = input.required || input.getAttribute('aria-required') === 'true';

        // Skip search filter inputs
        if (type === 'search' || name.includes('search') || id.includes('search')) {
            return null;
        }

        // Required check (only on blur or submit or if already touched)
        if (!val) {
            if (isRequired && (trigger === 'blur' || trigger === 'submit' || input.dataset.dynamicTouched === 'true')) {
                if (type === 'file') return 'Please select a required file.';
                if (input.tagName.toLowerCase() === 'select') return 'Please select an option.';
                return 'This field is required.';
            }
            return null;
        }

        // 1. Name validation (First Name, Last Name, Full Name)
        if (name === 'name' || name === 'first_name' || name === 'last_name' || input.dataset.validate === 'name') {
            // Check for numbers or forbidden symbols immediately
            if (/\d/.test(val)) {
                return 'Names cannot contain numbers.';
            }
            if (!NAME_REGEX.test(val)) {
                return 'May only contain letters, spaces, hyphens, periods, and apostrophes.';
            }
            if (val.length < 2 && (trigger === 'blur' || trigger === 'submit')) {
                return 'Must be at least 2 characters.';
            }
        }

        // 2. Contact / Phone Number validation
        if (name === 'contact_number' || name.includes('phone') || input.dataset.validate === 'phone') {
            const cleaned = cleanContactNumber(val);
            // Check if invalid characters exist
            if (/[^\d\s\-\+\(\)\.]/.test(rawVal)) {
                return 'Contact number may only contain digits, spaces, and hyphens.';
            }
            // Strict length & prefix check on blur or submit
            if (trigger === 'blur' || trigger === 'submit' || cleaned.length >= 11) {
                if (!/^09\d{9}$/.test(cleaned)) {
                    return 'Must be a valid 11-digit Philippine mobile number starting with 09 (e.g., 09123456789).';
                }
            }
        }

        // 3. Learner's Reference Number (LRN)
        if (name === 'learners_reference_number' || name === 'lrn' || input.dataset.validate === 'lrn') {
            if (!/^\d+$/.test(val)) {
                return 'LRN may only contain numbers.';
            }
            if (val.length !== 12 && (trigger === 'blur' || trigger === 'submit' || val.length > 12)) {
                return `LRN must be exactly 12 digits (currently ${val.length}/12).`;
            }
        }

        // 4. System Username
        if (name === 'username' || input.dataset.validate === 'username') {
            if (!USERNAME_REGEX.test(val)) {
                return 'Username may only contain letters, numbers, dashes, and underscores.';
            }
            if (val.length < 3 && (trigger === 'blur' || trigger === 'submit')) {
                return 'Username must be at least 3 characters.';
            }
        }

        // 5. Email format
        if (type === 'email' || name === 'email') {
            if ((trigger === 'blur' || trigger === 'submit') && !EMAIL_REGEX.test(val)) {
                return 'Please enter a valid email address (e.g., name@dalc.com).';
            }
        }

        // 6. Password & Confirmation
        if (type === 'password' || name === 'password') {
            const minLen = parseInt(input.getAttribute('minlength') || '8', 10);
            if (val.length < minLen && (trigger === 'blur' || trigger === 'submit')) {
                return `Password must be at least ${minLen} characters.`;
            }
        }

        if (name === 'password_confirmation' || id.includes('password_confirmation') || input.dataset.validate === 'password-confirmation') {
            const form = input.closest('form');
            if (form) {
                const pwd = form.querySelector('input[type="password"]:not([name="password_confirmation"]), input[name="password"]');
                if (pwd && pwd.value !== rawVal) {
                    return 'Passwords do not match.';
                }
            }
        }

        // 7. Date of Birth
        if (name === 'date_of_birth' || input.dataset.validate === 'dob') {
            const birthDate = new Date(val);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (isNaN(birthDate.getTime())) {
                return 'Please enter a valid date.';
            }
            if (birthDate >= today) {
                return 'Date of birth must be a past date.';
            }

            // Realistic maximum age (35 years)
            const maxAgeDate = new Date();
            maxAgeDate.setFullYear(today.getFullYear() - 35);
            if (birthDate < maxAgeDate) {
                return 'Date of birth must be a realistic date (maximum 35 years old).';
            }

            // Section-based minimum age
            const form = input.closest('form');
            if (form) {
                const classSelect = form.querySelector('select[name="class_name"], input[name="class_name"]');
                if (classSelect && classSelect.value) {
                    const minAge = getSectionMinAge(classSelect.value);
                    const minAgeDate = new Date();
                    minAgeDate.setFullYear(today.getFullYear() - minAge);
                    if (birthDate > minAgeDate) {
                        return `Students in ${classSelect.value} must be at least ${minAge} years old.`;
                    }
                }
            }
        }

        // 8. Max file size validation (5MB default)
        if (type === 'file' && input.files && input.files.length > 0) {
            const maxBytes = 5 * 1024 * 1024; // 5MB
            for (let i = 0; i < input.files.length; i++) {
                if (input.files[i].size > maxBytes) {
                    return `File "${input.files[i].name}" exceeds the maximum allowed size of 5MB.`;
                }
            }
        }

        return null;
    }

    // Attach visual error
    function applyInputError(input, errorMsg) {
        input.classList.add('has-dynamic-error');
        input.classList.remove('is-dynamic-valid');

        const parent = input.parentElement;
        if (!parent) return;

        const fieldKey = input.id || input.name || Math.random().toString();
        let errorEl = parent.querySelector(`.dynamic-field-error[data-field="${fieldKey}"]`);

        if (!errorEl) {
            errorEl = document.createElement('p');
            errorEl.className = 'dynamic-field-error text-[11px] font-bold text-rose-600 mt-1 flex items-center gap-1.5 transition-all duration-150';
            errorEl.setAttribute('data-field', fieldKey);
            input.insertAdjacentElement('afterend', errorEl);
        }

        errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation text-[10px] shrink-0 text-rose-500"></i><span>${errorMsg}</span>`;
        errorEl.style.display = 'flex';
    }

    // Clear visual error
    function removeInputError(input) {
        input.classList.remove('has-dynamic-error');

        const parent = input.parentElement;
        if (!parent) return;

        const fieldKey = input.id || input.name || '';
        if (fieldKey) {
            const errorEl = parent.querySelector(`.dynamic-field-error[data-field="${fieldKey}"]`);
            if (errorEl) {
                errorEl.remove();
            }
        }
    }

    // Event handler: input (real-time typing)
    document.addEventListener('input', function(e) {
        const input = e.target;
        if (!input || !input.form) return;

        // If field was already touched or flagged with error, revalidate continuously
        if (input.dataset.dynamicTouched === 'true' || input.classList.contains('has-dynamic-error')) {
            const error = evaluateInput(input, 'input');
            if (error) {
                applyInputError(input, error);
            } else {
                removeInputError(input);
            }
        } else {
            // First time typing: only flag if illegal characters typed (e.g. digits in name)
            const error = evaluateInput(input, 'input');
            if (error && (error.includes('numbers') || error.includes('symbols') || error.includes('only contain'))) {
                applyInputError(input, error);
                input.dataset.dynamicTouched = 'true';
            }
        }

        // Recheck password confirmation if primary password changed
        const name = (input.name || '').toLowerCase();
        if (input.type === 'password' && name === 'password') {
            const confirmInput = input.form.querySelector('input[name="password_confirmation"], input[id*="password_confirmation"]');
            if (confirmInput && confirmInput.value) {
                const cError = evaluateInput(confirmInput, 'input');
                if (cError) applyInputError(confirmInput, cError);
                else removeInputError(confirmInput);
            }
        }
    }, true);

    // Event handler: blur (leaving input field)
    document.addEventListener('blur', function(e) {
        const input = e.target;
        if (!input || !input.form) return;

        input.dataset.dynamicTouched = 'true';
        const error = evaluateInput(input, 'blur');
        if (error) {
            applyInputError(input, error);
        } else {
            removeInputError(input);
        }
    }, true);

    // Event handler: change (for dropdown selects and date pickers)
    document.addEventListener('change', function(e) {
        const input = e.target;
        if (!input || !input.form) return;

        input.dataset.dynamicTouched = 'true';
        const error = evaluateInput(input, 'blur');
        if (error) {
            applyInputError(input, error);
        } else {
            removeInputError(input);
        }

        // When class_name changes, re-validate date_of_birth
        const name = (input.name || '').toLowerCase();
        if (name === 'class_name') {
            const dobInput = input.form.querySelector('input[name="date_of_birth"]');
            if (dobInput && dobInput.value) {
                const dobError = evaluateInput(dobInput, 'blur');
                if (dobError) applyInputError(dobInput, dobError);
                else removeInputError(dobInput);
            }
        }
    }, true);

    // Event handler: form submit (prevent invalid submits and scroll to first error)
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || !form.tagName || form.tagName.toLowerCase() !== 'form') return;

        // Skip non-POST forms, logout actions, and delete actions
        const method = (form.getAttribute('method') || 'get').toLowerCase();
        const action = (form.getAttribute('action') || '').toLowerCase();
        if (method === 'get' || action.includes('logout') || form.querySelector('input[name="_method"][value="DELETE"]')) {
            return;
        }

        const elements = form.querySelectorAll('input, select, textarea');
        let firstInvalid = null;
        let errorCount = 0;

        elements.forEach(input => {
            input.dataset.dynamicTouched = 'true';
            const error = evaluateInput(input, 'submit');
            if (error) {
                applyInputError(input, error);
                errorCount++;
                if (!firstInvalid) firstInvalid = input;
            } else {
                removeInputError(input);
            }
        });

        if (errorCount > 0) {
            e.preventDefault();
            e.stopPropagation();

            if (firstInvalid) {
                firstInvalid.focus();
                if (typeof firstInvalid.scrollIntoView === 'function') {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            if (typeof window.showToast === 'function') {
                window.showToast('Please fix the highlighted field errors before saving.', 'warning');
            }
        }
    }, true);

    // Event handler: form reset (cleans up any error messages when modal cancels)
    document.addEventListener('reset', function(e) {
        const form = e.target;
        if (!form) return;
        form.querySelectorAll('.dynamic-field-error').forEach(el => el.remove());
        form.querySelectorAll('.has-dynamic-error').forEach(el => {
            el.classList.remove('has-dynamic-error');
            delete el.dataset.dynamicTouched;
        });
    }, true);

})();
</script>

