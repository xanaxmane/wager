let currentAuthMethod = 'auth';

$.auth = function() {
    $.modal('auth', 'show').then(() => {
        currentAuthMethod = 'auth';

        $('.auth .heading').html($.lang('general.auth.login'));
        $('.auth .btn-block').html($.lang('general.auth.login'));
        $('#auth-footer').show();
        $('#register-footer').hide();
    });
};

$.register = function() {
    $.modal('auth', 'show').then(() => {
        currentAuthMethod = 'register';

        $('.auth .heading').html($.lang('general.auth.register'));
        $('.auth .btn-block').html($.lang('general.auth.register'));
        $('#auth-footer').hide();
        $('#register-footer').show();
    });
};

$(document).ready(function() {
    $(document).on('click', '.auth .btn-block', function() {
        $.eraseCookie('token');

        const login = $('#login').val(), password = $('#password').val();
        if(currentAuthMethod === 'auth') {
            $('.auth').uiBlocker();
            $.request('/auth/login', {
                'name': login,
                'password': password
            }).then(function() {
                window.location.reload();
            }, function(reason) {
                $('.auth').uiBlocker(false);
                if(reason === 1) $.error($.lang('general.auth.wrong_credentials'));
                else $.error($.parseValidation(reason, {
                    'name': 'general.auth.credentials.login',
                    'password': 'general.auth.credentials.password'
                }));
            });
        } else {
            $('.auth').uiBlocker();
            $.request('/auth/register', {
                'name': login,
                'password': password
            }).then(function() {
                window.location.reload();
            }, function(error) {
                $('.auth').uiBlocker(false);
                $.error($.parseValidation(error, {
                    'name': 'general.auth.credentials.login',
                    'password': 'general.auth.credentials.password'
                }));
            });
        }
    });

    $(document).on('click', '[data-social]', function() {
        $('.auth').uiBlocker();
        window.location.href = '/auth/'+$(this).attr('data-social');
    });
});
