$(document).ready(function () {
    function togglePassword(inputId, eyeId) {
        const $input = $('#' + inputId);
        const $eye = $('#' + eyeId);

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $eye.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>');
        } else {
            $input.attr('type', 'password');
            $eye.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>');
        }
    }

    $('#togglePasswordBtn').on('click', function () {
        togglePassword('password', 'passwordEye');
    });

    $('#toggleConfirmPasswordBtn').on('click', function () {
        togglePassword('confirmPassword', 'confirmPasswordEye');
    });

    $('#signupForm').on('submit', function (e) {
        e.preventDefault();

        $('.error-text').text('');
        $('.form-control-custom').css('border-color', '#e5e7eb');

        const formData = {
            login: $('[name="login"]').val(),
            email: $('[name="email"]').val(),
            password: $('[name="password"]').val(),
            password_confirmation: $('[name="password_confirmation"]').val()
        };

        let hasError = false;

        if (!$.trim(formData.login)) {
            showError('login', 'Login é obrigatório');
            hasError = true;
        }

        if (!$.trim(formData.email)) {
            showError('email', 'E-mail é obrigatório');
            hasError = true;
        } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
            showError('email', 'E-mail inválido');
            hasError = true;
        }

        if (!formData.password) {
            showError('password', 'Senha é obrigatória');
            hasError = true;
        } else if (formData.password.length < 6) {
            showError('password', 'Use pelo menos 6 caracteres');
            hasError = true;
        }

        if (!formData.password_confirmation) {
            showError('password_confirmation', 'Confirmação de senha é obrigatória');
            hasError = true;
        } else if (formData.password !== formData.password_confirmation) {
            showError('password_confirmation', 'Senhas não coincidem');
            hasError = true;
        }

        if (!hasError) {
            this.submit();
        }
    });

    function showError(field, message) {
        $('#error-' + field).text(message);
        $('[name="' + field + '"]').css('border-color', '#ef4444');
    }

    $('.form-control-custom').on('input', function () {
        const fieldName = $(this).attr('name');
        $('#error-' + fieldName).text('');
        $(this).css('border-color', '#e5e7eb');
    });

});

function handleSocialLogin(provider) {
    window.location.href = '/auth/' + provider + '/redirect';
}