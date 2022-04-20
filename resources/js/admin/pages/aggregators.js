$.on('/admin/aggregators', function() {
    if(window.location.pathname.count('/') < 3) return;
    $('[data-toggle-aggregator]').on('change', function() {
        $.request('/admin/toggle_aggregator', {
            id: $(this).attr('data-toggle-aggregator')
        }).then(function() {
            redirect(window.location.pathname);
        });
    });

    $('[data-input-setting]').on('input', function() {
        if($(this).val().length < 1) return;
        $.request('/admin/ag_option_value', {
            'id': $(this).attr('data-ag-id'),
            'option': $(this).attr('data-input-setting'),
            'value': $(this).val()
        });
    });
});
