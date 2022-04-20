$.on('/admin/bankdeposits', function() {
    $('[data-accept]').on('click', function() {
        const id = $(this).attr('data-accept');
        const sum = parseFloat(prompt('Deposit amount'));
        $.request('/admin/wallet/bank/accept', { id: id, sum: sum }).then(function() {
            $(`[data-i-id="${id}"]`).remove();
        }, function(error) {
            $.error(error);
        });
    });

    $('[data-decline]').on('click', function() {
        const id = $(this).attr('data-decline');
        $.request('/admin/wallet/bank/decline', { id: id }).then(function() {
            $(`[data-i-id="${id}"]`).remove();
        }, function(error) {
            $.error(error);
        });
    });
});
