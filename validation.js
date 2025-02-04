document.addEventListener('DOMContentLoaded', () => {
    // Password strength indicator
    function checkPasswordStrength(password) {
        const strength = {
            0: 'Weak',
            1: 'Moderate',
            2: 'Strong'
        };
        
        let score = 0;
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        return strength[Math.min(score, 2)];
    }

    // Real-time password strength update
    document.querySelectorAll('input[type="password"]').forEach(input => {
        const strengthIndicator = input.parentElement.querySelector('.password-strength');
        
        input.addEventListener('input', function() {
            if (this.value.length === 0) {
                strengthIndicator.textContent = '';
                return;
            }
            
            const strength = checkPasswordStrength(this.value);
            strengthIndicator.textContent = `Password strength: ${strength}`;
            strengthIndicator.className = `password-strength ${strength.toLowerCase()}`;
        });
    });

    // Form validation
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const inputs = this.querySelectorAll('input');
            
            // Clear previous errors
            this.querySelectorAll('.error-text').forEach(el => el.remove());

            inputs.forEach(input => {
                const parent = input.closest('.form-group');
                
                // Required fields
                if (input.required && !input.value.trim()) {
                    isValid = false;
                    showError(input, 'This field is required');
                }

                // Pattern validation
                if (input.pattern) {
                    const regex = new RegExp(input.pattern);
                    if (!regex.test(input.value)) {
                        isValid = false;
                        showError(input, input.title);
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                const firstError = this.querySelector('.error-text');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });

    function showError(input, message) {
        const parent = input.closest('.form-group');
        const error = document.createElement('div');
        error.className = 'error-text';
        error.textContent = message;
        parent.appendChild(error);
    }
});

// Additional validation functions
function validateUsername(username) {
    return /^[a-zA-Z0-9_]{4,20}$/.test(username);
}

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validatePassword(password) {
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(password);
}

// Real-time validation for registration form
document.getElementById('registerForm')?.addEventListener('input', function(e) {
    const input = e.target;
    const parent = input.closest('.form-group');
    
    if (parent) {
        // Clear previous errors
        const error = parent.querySelector('.error-text');
        if (error) error.remove();
        
        // Validate based on input type
        switch(input.name) {
            case 'username':
                if (!validateUsername(input.value)) {
                    showError(input, 'Username must be 4-20 characters (letters, numbers, underscores)');
                }
                break;
                
            case 'email':
                if (!validateEmail(input.value)) {
                    showError(input, 'Invalid email format');
                }
                break;
                
            case 'password':
                if (!validatePassword(input.value)) {
                    showError(input, 'Password must contain uppercase, lowercase, and number');
                }
                break;
        }
    }
});

// Password confirmation validation
document.getElementById('deleteForm')?.addEventListener('submit', function(e) {
    const password = this.querySelector('input[name="confirm_password"]');
    if (password && password.value.length < 8) {
        e.preventDefault();
        showError(password, 'Password must be at least 8 characters');
    }
});