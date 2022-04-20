$.on('/admin/wallet_ignored', function() {
    $('[data-unignore-withdraw]').on('click', function() {
        const id = $(this).attr('data-unignore-withdraw');
        $.request('/admin/wallet/unignore', { id: id }).then(function() {
            $(`[data-w-id="${id}"]`).remove();
            $.success('Success');
        }, function(error) {
            $.error(error);
        });
    });
});

$.on('/admin/wallet', function() {
    $('[data-accept-withdraw]').on('click', function() {
        const id = $(this).attr('data-accept-withdraw');
        $.request('/admin/wallet/accept', { id: id }).then(function() {
            $(`[data-w-id="${id}"]`).remove();
            $.success('Success');
        }, function(error) {
            $.error(error);
        });
    });
    $('[data-decline-withdraw]').on('click', function() {
        const id = $(this).attr('data-decline-withdraw');
        const reason = prompt('Причина отказа');
        if(reason == null) return;
        $.request('/admin/wallet/decline', { id: id, reason: reason }).then(function() {
            $(`[data-w-id="${id}"]`).remove();
            $.success('Success (Reason: '+reason+')');
        }, function(error) {
            $.error(error);
        });
    });
    $('[data-ignore-withdraw]').on('click', function() {
        const id = $(this).attr('data-ignore-withdraw');
        $.request('/admin/wallet/ignore', { id: id }).then(function() {
            $(`[data-w-id="${id}"]`).remove();
            $('.tooltip').remove();
            $.success('Success');
        }, function(error) {
            $.error(error);
        });
    });
});
