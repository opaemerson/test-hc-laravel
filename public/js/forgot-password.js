$(document).ready(function () {
    let currentStep = 1;
    let userEmail = '';
    let resetToken = '';
    let timerInterval = null;

    // CSRF Token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Step Navigation
    function goToStep(step) {
        // Hide all steps
        $('.step-content').removeClass('active');
        $('.step').removeClass('active completed');

        // Show current step
        $(`#step${step}`).addClass('active');
        $(`.step[data-step="${step}"]`).addClass('active');

        // Mark previous steps as completed
        for (let i = 1; i < step; i++) {
            $(`.step[data-step="${i}"]`).addClass('completed');
        }

        currentStep = step;
        hideMessages();
    }

    // Message Functions
    function showSuccess(message) {
        $('#successText').text(message);
        $('#successMessage').addClass('show');
        $('#errorMessage').removeClass('show');
    }

    function showError(message) {
        $('#errorText').text(message);
        $('#errorMessage').addClass('show');
        $('#successMessage').removeClass('show');
    }

    function hideMessages() {
        $('#successMessage').removeClass('show');
        $('#errorMessage').removeClass('show');
    }

    // Loading State
    function setLoading(button, loading) {
        if (loading) {
            button.addClass('loading').prop('disabled', true);
        } else {
            button.removeClass('loading').prop('disabled', false);
        }
    }

    // Timer for code expiration
    function startTimer(duration) {
        if (timerInterval) clearInterval(timerInterval);

        let timeLeft = duration;
        const timerElement = $('#timer');

        timerInterval = setInterval(function () {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.text(`${minutes}:${seconds.toString().padStart(2, '0')}`);

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                showError('Código expirado. Solicite um novo código.');
                setTimeout(() => goToStep(1), 2000);
            }

            timeLeft--;
        }, 1000);
    }

    // Step 1: Send Code
    $('#emailForm').on('submit', function (e) {
        e.preventDefault();
        hideMessages();

        const email = $('#email').val().trim();
        const submitBtn = $(this).find('button[type="submit"]');

        setLoading(submitBtn, true);

        $.ajax({
            url: '/forgot-password/send-code',
            method: 'POST',
            data: { email: email },
            success: function (response) {
                setLoading(submitBtn, false);
                userEmail = email;
                $('#emailDisplay').text(email);
                showSuccess(response.message);

                setTimeout(() => {
                    goToStep(2);
                    startTimer(response.expires_in || 900);
                    $('.code-input').first().focus();
                }, 1500);
            },
            error: function (xhr) {
                setLoading(submitBtn, false);
                const message = xhr.responseJSON?.message || 'Erro ao enviar código. Tente novamente.';
                showError(message);
            }
        });
    });

    // Code Input Auto-focus and Validation
    $('.code-input').on('input', function (e) {
        const input = $(this);
        const value = input.val();

        // Only allow numbers
        if (!/^\d*$/.test(value)) {
            input.val('');
            return;
        }

        // Remove error state
        input.removeClass('error');

        if (value.length === 1) {
            input.addClass('filled');
            // Move to next input
            const nextInput = $(`.code-input[data-index="${parseInt(input.data('index')) + 1}"]`);
            if (nextInput.length) {
                nextInput.focus();
            }
        } else {
            input.removeClass('filled');
        }
    });

    // Code Input Backspace
    $('.code-input').on('keydown', function (e) {
        if (e.key === 'Backspace' && $(this).val() === '') {
            const prevInput = $(`.code-input[data-index="${parseInt($(this).data('index')) - 1}"]`);
            if (prevInput.length) {
                prevInput.focus().val('');
            }
        }
    });

    // Code Input Paste
    $('.code-input').first().on('paste', function (e) {
        e.preventDefault();
        const pastedData = e.originalEvent.clipboardData.getData('text').trim();

        if (/^\d{6}$/.test(pastedData)) {
            pastedData.split('').forEach((digit, index) => {
                $(`.code-input[data-index="${index}"]`).val(digit).addClass('filled');
            });
        }
    });

    // Step 2: Verify Code
    $('#codeForm').on('submit', function (e) {
        e.preventDefault();
        hideMessages();

        let code = '';
        $('.code-input').each(function () {
            code += $(this).val();
        });

        if (code.length !== 6) {
            showError('Por favor, digite o código completo de 6 dígitos.');
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        setLoading(submitBtn, true);

        $.ajax({
            url: '/forgot-password/verify-code',
            method: 'POST',
            data: {
                email: userEmail,
                code: code
            },
            success: function (response) {
                setLoading(submitBtn, false);
                resetToken = response.reset_token;
                showSuccess(response.message);

                if (timerInterval) clearInterval(timerInterval);

                setTimeout(() => {
                    goToStep(3);
                    $('#password').focus();
                }, 1500);
            },
            error: function (xhr) {
                setLoading(submitBtn, false);
                const message = xhr.responseJSON?.message || 'Código inválido. Tente novamente.';
                showError(message);

                // Mark inputs as error
                $('.code-input').addClass('error');

                // Clear inputs after error
                setTimeout(() => {
                    $('.code-input').val('').removeClass('filled error');
                    $('.code-input').first().focus();
                }, 1500);
            }
        });
    });

    // Resend Code
    $('#resendCode').on('click', function () {
        hideMessages();
        const btn = $(this);
        setLoading(btn, true);

        $.ajax({
            url: '/forgot-password/send-code',
            method: 'POST',
            data: { email: userEmail },
            success: function (response) {
                setLoading(btn, false);
                showSuccess('Novo código enviado!');

                // Clear code inputs
                $('.code-input').val('').removeClass('filled error');
                $('.code-input').first().focus();

                // Restart timer
                startTimer(response.expires_in || 900);
            },
            error: function (xhr) {
                setLoading(btn, false);
                const message = xhr.responseJSON?.message || 'Erro ao reenviar código.';
                showError(message);
            }
        });
    });

    // Password Strength Checker
    $('#password').on('input', function () {
        const password = $(this).val();
        const strengthFill = $('.strength-fill');
        const strengthText = $('.strength-text span');

        if (password.length === 0) {
            strengthFill.removeClass('weak medium strong');
            strengthText.text('').removeClass('weak medium strong');
            return;
        }

        let strength = 0;

        // Length
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;

        // Complexity
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;

        strengthFill.removeClass('weak medium strong');
        strengthText.removeClass('weak medium strong');

        if (strength <= 2) {
            strengthFill.addClass('weak');
            strengthText.text('Fraca').addClass('weak');
        } else if (strength <= 4) {
            strengthFill.addClass('medium');
            strengthText.text('Média').addClass('medium');
        } else {
            strengthFill.addClass('strong');
            strengthText.text('Forte').addClass('strong');
        }
    });

    // Toggle Password Visibility
    $('.toggle-password').on('click', function () {
        const btn = $(this);
        const targetId = btn.data('target');
        const input = $(`#${targetId}`);

        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            btn.addClass('active');
        } else {
            input.attr('type', 'password');
            btn.removeClass('active');
        }
    });

    // Step 3: Reset Password
    $('#passwordForm').on('submit', function (e) {
        e.preventDefault();
        hideMessages();

        const password = $('#password').val();
        const passwordConfirmation = $('#password_confirmation').val();

        if (password.length < 8) {
            showError('A senha deve ter no mínimo 8 caracteres.');
            return;
        }

        if (password !== passwordConfirmation) {
            showError('As senhas não coincidem.');
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        setLoading(submitBtn, true);

        $.ajax({
            url: '/forgot-password/reset-password',
            method: 'POST',
            data: {
                reset_token: resetToken,
                password: password,
                password_confirmation: passwordConfirmation
            },
            success: function (response) {
                setLoading(submitBtn, false);
                showSuccess(response.message);

                // Redirect to login after 2 seconds
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            },
            error: function (xhr) {
                setLoading(submitBtn, false);
                const message = xhr.responseJSON?.message || 'Erro ao redefinir senha. Tente novamente.';
                showError(message);
            }
        });
    });

    // Clear messages on input
    $('#email').on('input', hideMessages);
    $('#password, #password_confirmation').on('input', hideMessages);
});