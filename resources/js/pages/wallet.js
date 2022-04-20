const clipboard = require('clipboard-polyfill');
const qr = require('qrcode');
import bitcoin from 'bitcoin-units';

let wallet = {
    deposit: null,
    withdraw: null,
    sum: 0
};``

$.on('/wallet', function() {
    const setPaymentMethodDescription = function(tab) {
        $(`[data-wallet-tab="${tab}"] .paymentDesc`).html($(`[data-wallet-tab="${tab}"] .paymentMethod.active`).find('.icon').html() + ' ' + $(`[data-wallet-tab="${tab}"] .paymentMethod.active`).find('.name').html());
        wallet[tab] = $(`[data-wallet-tab="${tab}"] .paymentMethod.active`).data(`${tab}-type`);
    };

    $('[data-toggle-wallet-tab]').on('click', function() {
        if($(this).hasClass('active')) return;
        $(`[data-toggle-wallet-tab]`).removeClass('active');
        $(this).addClass('active');

        $(`[data-wallet-tab]`).hide();
        $(`[data-wallet-tab="${$(this).attr('data-toggle-wallet-tab')}"]`).fadeIn('fast');

        if($(this).data('toggle-wallet-tab') === 'history') $('[data-toggle-history-tab]:first-child').click();
    });

    $('[data-toggle-history-tab]').on('click', function() {
        $(`[data-toggle-history-tab]`).removeClass('active');
        $(this).addClass('active');

        $(`[data-history-tab]`).hide();
        $(`[data-history-tab="${$(this).attr('data-toggle-history-tab')}"]`).fadeIn('fast');

        loadHistory($(this).data('toggle-history-tab'));
    });

    $('[data-wallet-tab="deposit"] .paymentMethod').on('click', function() {
        $('[data-wallet-tab="deposit"] .paymentMethod').removeClass('active');
        $(this).addClass('active');
        setPaymentMethodDescription('deposit');

        const type = $(this).data('deposit-type');
        switch ($(this).data('deposit-action')) {
            case 'qr':
                $.setCurrency(type);
                const currency = window.Laravel.currency[type];
                const canvas = $(`<canvas></canvas>`);

                $('[data-wallet-tab="deposit"] .walletOut').html(`
                    <div>${$.lang('wallet.deposit.address', { currency: currency.name })}</div>
                    <div class="input-loader">
                        <input onclick="this.select()" style="cursor: pointer !important;" data-toggle="tooltip" data-placement="top" title="${$.lang('wallet.copy')}" type="text" readonly>
                        <div class="loader">${window.loader()}</div>
                    </div>
                    <div class="qr"><div class="loader">${window.loader()}</div></div>
                    <div>${$.lang('wallet.deposit.confirmations', { currency: currency.name, confirmations: currency.requiredConfirmations })}</div>
                `).find('.qr').append(canvas);

                $.request('wallet/getDepositWallet', { currency: $.currency() }).then(function(response) {
                    if(response.currency !== $.currency()) return;

                    $(`.input-loader .loader`).remove();
                    $(`.input-loader input`).val(response.wallet);

                    qr.toCanvas(canvas[0], response.wallet, function () {
                        $(`.qr .loader`).remove();
                    });
                });
                break;
        }
    });

    $('[data-wallet-tab="withdraw"] .paymentMethod').on('click', function() {
        $('[data-wallet-tab="withdraw"] .paymentMethod').removeClass('active');
        $(this).addClass('active');
        setPaymentMethodDescription('withdraw');

        const currency = window.Laravel.currency[$(this).data('withdraw-type')];
        $.setCurrency(currency.id);
        $('#withdrawSum').html($.lang('wallet.withdraw.amount', { min: bitcoin(currency.minimalWithdraw, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8), icon: currency.icon, style: currency.style }));
        $('#withdrawFee').html($.lang('wallet.withdraw.fee', { fee: bitcoin(currency.withdrawFee, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8), icon: currency.icon, style: currency.style }));
    });

    $('[data-deposit-type]:first-child').click();
    $('[data-withdraw-type]:first-child').click();

    setPaymentMethodDescription('deposit');
    setPaymentMethodDescription('withdraw');

    $('.close-action-notify').on('click', function() {
        $('.successfulWalletAction, .walletUiBlocker').fadeOut('fast');
    });

    $('#withdraw').on('click', function() {
        if($('#wallet').val().length < 5) {
            $.error($.lang('general.error.enter_wallet'));
            return;
        }

        $('.walletUiBlocker').html(`<div class="loader">${window.loader()}</div>`).fadeIn('fast');
        $.request('wallet/withdraw', {
            sum: bitcoin(parseFloat($('#walletWithValue').val()), $.unit()).to('btc').value(),
            currency: wallet.withdraw,
            wallet: $('#wallet').val()
        }).then(function(response) {
            $('.successfulWalletAction .heading').html($.lang('wallet.withdraw.title'));
            $('.successfulWalletAction .content').html(`${$.lang('wallet.withdraw.content')} ${response.notifyAboutVip ? $.lang('wallet.withdraw.vip_content') : ''}`);
            $('.successfulWalletAction, .walletUiBlocker').fadeIn('fast');
        }, function(error) {
            $('.walletUiBlocker').fadeOut('fast', () => $(this).find('.loader').remove());
            if(error === 1) $.error($.lang('general.error.invalid_withdraw'));
            if(error === 2) $.error($.lang('general.error.invalid_wager'));
            if(error === 3) $.error($.lang('general.error.only_one_withdraw'));
        });
    });
}, ['/css/pages/wallet.css']);

$(document).on('click', `.input-loader input`, function() {
    clipboard.writeText($(this).val());
    $.success($.lang('wallet.copied'));
});

function loadHistory(type) {
    $('.walletUiBlocker').html(`<div class="loader">${window.loader()}</div>`).fadeIn('fast');
    $.get(`/wallet/${type}`, function(response) {
        $(`[data-history-tab="${type}"]`).html(response);
        $('.walletUiBlocker').fadeOut('fast', () => $(this).find('.loader').remove());
    });
}

$.cancelWithdraw = function(id) {
    $('.walletUiBlocker').html(`<div class="loader">${window.loader()}</div>`).fadeIn('fast');
    $.request('wallet/cancel_withdraw', { id: id }).then(function() {
        $.success($.lang('wallet.history.withdraw_cancelled'));
        $('[data-toggle-history-tab="withdraws"]').click();
    }, function(error) {
        $('.walletUiBlocker').fadeOut('fast', () => $(this).find('.loader').remove());
        $.error(error);
    });
};

$.cancelBankDeposit = function(id) {
    $('.walletUiBlocker').html(`<div class="loader">${window.loader()}</div>`).fadeIn('fast');
    $.request('wallet/cancel_bank', { id: id }).then(function() {
        $.success($.lang('wallet.bank.cancelled'));
        $('[data-toggle-history-tab="payments"]').click();
    }, function(error) {
        $('.walletUiBlocker').fadeOut('fast', () => $(this).find('.loader').remove());
        $.error(error);
    });
};
