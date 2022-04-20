import bitcoin from 'bitcoin-units';
let interval = null;

$.invest = () => {
    $.modal('invest').then(() => {
        $('[data-modal-tab]').on('click', function() {
            $('[data-modal-tab]').removeClass('active');
            $(this).addClass('active');

            const content = $('[data-modal-tab-content]');

            if(interval != null) clearInterval(interval);
            $('.invest').attr('style', '');

            const loader = () => {
                content.html(`
                    <div class="ph-item">
                        <div class="ph-col-12">
                            <div class="ph-picture"></div>
                            <div class="ph-row">
                                <div class="ph-col-6 big"></div>
                                <div class="ph-col-4 empty big"></div>
                                <div class="ph-col-2 big"></div>
                                <div class="ph-col-4"></div>
                                <div class="ph-col-8 empty"></div>
                                <div class="ph-col-6"></div>
                                <div class="ph-col-6 empty"></div>
                                <div class="ph-col-12"></div>
                            </div>
                        </div>
                    </div>`);
            }

            loader();

            switch ($(this).data('modal-tab')) {
                case 'overview':
                    const stats = () => {
                        $.request('investment/stats', {
                            currency: $.currency()
                        }).then(function(response) {
                            content.html(`
                            <div class="shares">
                                <div class="your_shares">${response.your_bankroll_share.toFixed(2)}%</div>
                                <div class="your_shares_desc">${$.lang('invest.sidebar.your_share')}</div>
                            </div>
                            <div class="divider">
                                <div class="line"></div>
                                <i class="fal fa-angle-down"></i>
                                <div class="line"></div>
                            </div>
                            <div class="stats">
                                <div class="stat">
                                    <div>${$.lang('invest.sidebar.your_bankroll')}</div>
                                    <div>${bitcoin(response.your_bankroll, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)} <i class="${window.Laravel.currency[$.currency()].icon}" style="color: ${window.Laravel.currency[$.currency()].style}"></i></div>
                                </div>
                                <div class="stat">
                                    <div>${$.lang('invest.sidebar.site_bankroll')}</div>
                                    <div>${bitcoin(response.investment_profit, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)} <i class="${window.Laravel.currency[$.currency()].icon}" style="color: ${window.Laravel.currency[$.currency()].style}"></i></div>
                                </div>
                            </div>
                            <div class="stats">
                                <div class="stat">
                                    <div>${$.lang('invest.sidebar.your_investing_profit')}</div>
                                    <div>${bitcoin(response.site_bankroll, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)} <i class="${window.Laravel.currency[$.currency()].icon}" style="color: ${window.Laravel.currency[$.currency()].style}"></i></div>
                                </div>
                                <div class="stat">
                                    <div>${$.lang('invest.sidebar.site_profit')}</div>
                                    <div>${bitcoin(response.site_profit, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)} <i class="${window.Laravel.currency[$.currency()].icon}" style="color: ${window.Laravel.currency[$.currency()].style}"></i></div>
                                </div>
                            </div>
                        `);
                        });
                    };

                    stats();
                    interval = setInterval(stats, 10000);
                    break;
                case 'invest':
                    content.html(`
                        <div class="warn">${$.lang('invest.invest_fee')}</div>
                        <div class="warn">${$.lang('invest.invest_fee_withdraw', { value: window.Laravel.currency[$.currency()].invest_commission })}</div>
                        <div class="mt-2 investBalance">${$.lang('invest.your_balance', { balance: `${$(`[data-currency-value="${$.currency()}"]`).html()} <i class="${window.Laravel.currency[$.currency()].icon}" style="color: ${window.Laravel.currency[$.currency()].style}"></i>` })}</div>
                        <div class="investBalance">${$.lang('invest.min', { min: `${bitcoin(window.Laravel.currency[$.currency()].investMin, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)} <i class="${window.Laravel.currency[$.currency()].icon}" style="color: ${window.Laravel.currency[$.currency()].style}"></i>` })}</div>
                        <input id="investment_amount" class="mt-2" type="number" step="0.00000001" value="${bitcoin(window.Laravel.currency[$.currency()].investMin, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)}">
                        <button class="btn btn-primary btn-block mt-2" id="invest">${$.lang('invest.invest')}</button>
                    `);

                    $('#invest').on('click', function() {
                        const amount = bitcoin(parseFloat($('#investment_amount').val()), $.unit()).to('btc').value();
                        loader();

                        $.request('invest', {
                            amount: amount
                        }).then(function() {
                            $('.invest [data-modal-tab="history"]').click();
                        }, function() {
                            $('.invest [data-modal-tab="invest"]').click();
                            $.error($.lang('general.chat_commands.modal.tip.invalid_amount'));
                        });
                    });
                    break;
                case 'history':
                    const history = () => {
                        $.request('investment/history').then(function(response) {
                            content.html(`<table class="live-table">
                                <thead>
                                    <tr>
                                        <th>
                                            ${$.lang('invest.history.amount')}
                                        </th>
                                        <th>
                                            ${$.lang('invest.history.your_share')}
                                        </th>
                                        <th>
                                            ${$.lang('invest.history.profit')}
                                        </th>
                                        <th>
                                            ${$.lang('invest.history.status')}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="live_games"></tbody>
                            </table>`);

                            _.forEach(response, (e) => {
                                content.find('.live_games').append(`
                                    <tr>
                                        <th>
                                            <div>
                                                ${bitcoin(e.amount, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)} <i class="${window.Laravel.currency[e.currency].icon}" style="color: ${window.Laravel.currency[e.currency].style}"></i>
                                            </div>
                                        </th>
                                        <th>
                                            <div>
                                                ${e.share.toFixed(2)}%
                                            </div>
                                        </th>
                                        <th>
                                            <div>
                                                <span class="text-${e.profit > e.amount ? 'success' : (e.profit === e.amount ? '' : 'danger')}">
                                                    ${bitcoin(e.profit, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)} <i class="${window.Laravel.currency[e.currency].icon}" style="color: ${window.Laravel.currency[e.currency].style}"></i>
                                                </span>
                                            </div>
                                        </th>
                                        <th>
                                            <div>
                                                ${e.status === 1 ? $.lang('invest.history.cancelled') : (e.profit > 0 ?
                                    `<a data-disinvest="${e.id}" class="disinvestButton" href="javascript:void(0)">${$.lang('invest.history.disinvest')}</a>`
                                    : $.lang('invest.history.dead'))}
                                            </div>
                                        </th>
                                    </tr>
                                `);

                                $('[data-disinvest]').on('click', function() {
                                    loader();

                                    const done = () => $('[data-modal-tab="history"]').click();
                                    $.request('disinvest', { id: $(this).data('disinvest') }).then(done, () => {
                                        done();
                                        $.error('Failed');
                                    });
                                });
                            });
                        });
                    };

                    $('.invest').css({ width: 500 });
                    history();
                    interval = setInterval(history, 10000);
                    break;
            }
        });

        $('[data-modal-tab]:first-child').click();
    });
};
