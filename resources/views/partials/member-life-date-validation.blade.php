<script>
(function() {
    const msgs = {
        deathWhileAlive: @json(__('messages.Death date cannot be entered while the member is alive.')),
        deathRequired: @json(__('messages.Death date is required when the member is deceased.')),
        sameDate: @json(__('messages.Birth date and death date cannot be the same.')),
        deathBeforeBirth: @json(__('messages.Death date must be after birth date.')),
    };

    window.validateMemberLifeDates = function(form) {
        if (!form) return true;

        const aliveCheckbox = form.querySelector('[name="death"]');
        const isAlive = aliveCheckbox ? aliveCheckbox.checked : false;
        const birth = (form.querySelector('[name="birthdate"]')?.value || '').trim();
        const death = (form.querySelector('[name="deathdate"]')?.value || '').trim();

        if (isAlive && death) {
            toastr.error(msgs.deathWhileAlive);
            return false;
        }

        if (!isAlive && !death) {
            toastr.error(msgs.deathRequired);
            return false;
        }

        if (birth && death) {
            if (birth === death) {
                toastr.error(msgs.sameDate);
                return false;
            }
            if (death < birth) {
                toastr.error(msgs.deathBeforeBirth);
                return false;
            }
        }

        return true;
    };

    window.toggleMemberDeathDateField = function(form) {
        if (!form) return;

        const aliveCheckbox = form.querySelector('[name="death"]');
        const deathInput = form.querySelector('[name="deathdate"]');
        if (!aliveCheckbox || !deathInput) return;

        const isAlive = aliveCheckbox.checked;

        if (isAlive) {
            deathInput.value = '';
            deathInput.disabled = true;
            deathInput.removeAttribute('required');
        } else {
            deathInput.disabled = false;
            deathInput.setAttribute('required', 'required');
        }
    };

    window.showMemberValidationToasts = function(errors) {
        if (!errors) return;
        Object.keys(errors).forEach(function(key) {
            const messages = errors[key];
            (Array.isArray(messages) ? messages : [messages]).forEach(function(msg) {
                toastr.error(msg);
            });
        });
    };

    document.addEventListener('change', function(e) {
        if (e.target && e.target.matches('[name="death"]')) {
            const form = e.target.closest('form');
            if (form) window.toggleMemberDeathDateField(form);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('form').forEach(function(form) {
            if (form.querySelector('[name="death"]') && form.querySelector('[name="deathdate"]')) {
                window.toggleMemberDeathDateField(form);
            }
        });
    });

    document.addEventListener('shown.bs.modal', function(e) {
        const form = e.target.querySelector('form');
        if (form) window.toggleMemberDeathDateField(form);
    });
})();
</script>
