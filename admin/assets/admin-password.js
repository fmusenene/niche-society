(function () {
    const rules = {
        length: function (password) { return password.length >= 8; },
        upper: function (password) { return /[A-Z]/.test(password); },
        lower: function (password) { return /[a-z]/.test(password); },
        number: function (password) { return /[0-9]/.test(password); },
        special: function (password) { return /[^A-Za-z0-9]/.test(password); },
    };

    function evaluatePassword(password) {
        const result = {};
        Object.keys(rules).forEach(function (ruleId) {
            result[ruleId] = rules[ruleId](password);
        });
        return result;
    }

    function setRuleState(ruleEl, passed, touched) {
        ruleEl.classList.toggle('pass', passed);
        ruleEl.classList.toggle('fail', !passed && touched);
        var icon = ruleEl.querySelector('.rule-icon');
        if (icon) {
            icon.className = 'bi rule-icon ' + (passed ? 'bi-check-circle-fill' : (touched ? 'bi-x-circle-fill' : 'bi-circle'));
        }
    }

    function initPasswordForm(form) {
        var newInput = form.querySelector('.js-admin-new-password');
        var confirmInput = form.querySelector('.js-admin-confirm-password');
        var submitBtn = form.querySelector('.js-admin-password-submit');
        var ruleEls = form.querySelectorAll('[data-rule]');
        var matchEl = form.querySelector('.js-admin-password-match');

        if (!newInput || !confirmInput) {
            return;
        }

        function update() {
            var password = newInput.value;
            var confirm = confirmInput.value;
            var checks = evaluatePassword(password);
            var allPassed = true;

            ruleEls.forEach(function (ruleEl) {
                var ruleId = ruleEl.getAttribute('data-rule');
                var passed = !!checks[ruleId];
                if (!passed) {
                    allPassed = false;
                }
                setRuleState(ruleEl, passed, password.length > 0);
            });

            var matches = password !== '' && password === confirm;
            if (matchEl) {
                matchEl.classList.remove('pass', 'fail');
                if (confirm.length === 0) {
                    matchEl.textContent = '';
                } else if (matches) {
                    matchEl.textContent = 'Passwords match';
                    matchEl.classList.add('pass');
                } else {
                    matchEl.textContent = 'Passwords do not match';
                    matchEl.classList.add('fail');
                }
            }

            if (submitBtn) {
                submitBtn.disabled = !allPassed || !matches;
            }
        }

        newInput.addEventListener('input', update);
        confirmInput.addEventListener('input', update);
        form.addEventListener('submit', function (event) {
            update();
            if (submitBtn && submitBtn.disabled) {
                event.preventDefault();
            }
        });

        update();
    }

    document.querySelectorAll('.js-admin-password-form').forEach(initPasswordForm);
})();
