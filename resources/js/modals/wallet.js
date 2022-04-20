$.demoWallet = function() {
    $.modal('demo-wallet').then(() => {
        if(parseFloat($(`[data-demo-currency-value="${$.currency()}"]`).html()) > 0) {
            $('.demo-wallet .wallet-content').html(`<div class="notice">${$.lang('general.wallet.demo.error')}</div>`);
        } else {
            $('.demo-wallet .wallet-content').html(`<div class="notice">
                <div class="btn btn-primary">${$.lang('general.wallet.demo.obtain')}</div>
            </div>`);
            $('.demo-wallet .btn').on('click', function() {
                $('.demo-wallet').uiBlocker(true);
                $.request('promocode/demo').then(function() {
                    $('.demo-wallet').uiBlocker(false);
                    $.modal('demo-wallet', 'hide');
                }, function() {
                    $('.demo-wallet').uiBlocker(false);
                    $.error($.lang('general.wallet.demo.error'));
                });
            });
        }
    });
};

$(document).ready(() => {
    _.forEach(window.Laravel.currency, (c) => {
        c.convert = {
            usd: (token) => {
                return token * c.price;
            },
            token: (usd) => {
                return usd / c.price;
            }
        };
    });
});
