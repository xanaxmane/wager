$.on('/admin/promo', function() {
    flatpickr('#expires', {
        enableTime: true,
        dateFormat: "d-m-Y H:i",
        time_24hr: true
    });

    $('#finish').on('click', function() {
        $('#close').click();
        $.request('/admin/promocode/create', {
            code: $('#code').val(),
            usages: $('#usages').val(),
            expires: $('#expires').val(),
            sum: $('#sum').val(),
            currency: $('#currency').val()
        }).then(function() {
            window.location.reload();
        }, function(error) {
            if(error >= 1) $.error('Ошибка ' + error);
            else $.error($.parseValidation(error, {
                code: 'Code',
                usages: 'Max usages',
                expires: 'Expires',
                sum: 'Sum',
                currency: 'Currency'
            }));
        });
    });

    $('[data-remove]').on('click', function() {
        $.request('/admin/promocode/remove', {
            'id': $(this).attr('data-remove')
        }).then(function() {
            window.location.reload();
        });
    });
});
