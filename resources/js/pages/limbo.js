require('jquery.animate-number');

$.game('limbo', function(container, overviewData) {
    if($.isOverview(overviewData)) {
        $(container).html(`
            <div class="mt-2">
                ${$.lang('general.target_payout')}: ${overviewData.game.data.target.toFixed(2)} (${calculate(overviewData.game.data.target).toFixed(2)}%)
            </div>
            <div class="mt-2">
                ${$.lang('general.got')}: x${overviewData.game.data.number.toFixed(2)}
            </div>`);
    } else {
        $(container).append(`
            <div class="limbo_canvas">
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPYAAABABAMAAADbr5eTAAAAD1BMVEX///8AAAD///////////9cEmbOAAAABXRSTlMTAA4JBDkaubkAAAGqSURBVFjDxdjRjeIwEIDh/xwKwLELcBIKSBYKiDn6r+mk1Z5Gy2YzIszI/yM8fJp4YiGIL/T4GCGMt2jTC3Yu/G+KSsb2HaSwRCUrW2hJwY1soXXcw05gjuu2rNlzoUYlE3tlqy4qWdiJ7YaoZGDPbBfiU/Z2AvvBdVvG1gf3sDN4DK7bsuTaqrvY7FVd7Z69TlGyt2cAfdtcbParjnav2H+iZG2vAPqmu9gFpSgZ2xmtJUq2dg9OB67bMzgduG6jFrzshF6N0cU+ozc42TOScqWb2fk6lnG6RUDvdh0hXKuNnQoHulnYiWNd3rczRxvetmcOV4/Y+aOEaVGeuF53wL7LgcnY3k8dob/wBP6Diy0c06PwVstrdsaw7jV7Rclg1fNj005sZLttfwsw1Z/2imnh938tlmc7Y9zlc8ywPNGCi93j1GXjmqzfbfnKB7+D1H238Ugecf59E0n4FeQFlo8kehw7xbTzCnLGszrv3HvMeNbxo8XR1i/8x9fPSgotmj7tRnUNbYaGdmhoMzS0u4Y2lUKrhob2iZlWday0KnCmXZl2xXuhTd0/i6cAPsoSejMAAAAASUVORK5CYII=" class="cloud cloud-r" alt>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMAAAAAwBAMAAAC1YGgtAAAAD1BMVEX///8AAAD///////////9cEmbOAAAABXRSTlMTAA4IBCABiPgAAAEzSURBVFjDtdVhjoIwEEDhx+ABLO0BBtYDWPUAVPf+Z9qYuAGkRIHp95+8DNApbpNHB3TX5D7aFPDKy08qEfAMJNoHAhPRPKBMJeNAw5vaNhCYaU0DDXPJMqDMHQwDDTmGgTM5rVkgkFWbBY7kJauAktcaBTwLaqPAkSVGAWVJNAl4FlUmgZ5FtUmAZWIRaBjkTkJImwPhonzQPhQkbgt45VunLQHPCnF9ICgryOuh3+8DZ1ZpX+9Ubl8GPCsl50ffA+sBQC7jYbAbYE7SW8BfpLvN1sMOh2ngztPP7I7cIY0D9//scHqlU3apRgE/PY1BMSCjgE4Xl2IikluV9fBz7lWRvQ1jj5EDwwBFyDMQHtcrA/OAV0pyQSnK9ZTlKErwFCUcKaqmp6gapaiqdCBSlJzcHzwgmJkN1iU1AAAAAElFTkSuQmCC" class="cloud cloud-d" alt>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPYAAABABAMAAADbr5eTAAAAD1BMVEX///8AAAD///////////9cEmbOAAAABXRSTlMTAA4JBDkaubkAAAGqSURBVFjDxdjRjeIwEIDh/xwKwLELcBIKSBYKiDn6r+mk1Z5Gy2YzIszI/yM8fJp4YiGIL/T4GCGMt2jTC3Yu/G+KSsb2HaSwRCUrW2hJwY1soXXcw05gjuu2rNlzoUYlE3tlqy4qWdiJ7YaoZGDPbBfiU/Z2AvvBdVvG1gf3sDN4DK7bsuTaqrvY7FVd7Z69TlGyt2cAfdtcbParjnav2H+iZG2vAPqmu9gFpSgZ2xmtJUq2dg9OB67bMzgduG6jFrzshF6N0cU+ozc42TOScqWb2fk6lnG6RUDvdh0hXKuNnQoHulnYiWNd3rczRxvetmcOV4/Y+aOEaVGeuF53wL7LgcnY3k8dob/wBP6Diy0c06PwVstrdsaw7jV7Rclg1fNj005sZLttfwsw1Z/2imnh938tlmc7Y9zlc8ywPNGCi93j1GXjmqzfbfnKB7+D1H238Ugecf59E0n4FeQFlo8kehw7xbTzCnLGszrv3HvMeNbxo8XR1i/8x9fPSgotmj7tRnUNbYaGdmhoMzS0u4Y2lUKrhob2iZlWday0KnCmXZl2xXuhTd0/i6cAPsoSejMAAAAASUVORK5CYII=" class="cloud cloud-v" alt>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMAAAAAwBAMAAAC1YGgtAAAAD1BMVEX///8AAAD///////////9cEmbOAAAABXRSTlMTAA4IBCABiPgAAAEzSURBVFjDtdVhjoIwEEDhx+ABLO0BBtYDWPUAVPf+Z9qYuAGkRIHp95+8DNApbpNHB3TX5D7aFPDKy08qEfAMJNoHAhPRPKBMJeNAw5vaNhCYaU0DDXPJMqDMHQwDDTmGgTM5rVkgkFWbBY7kJauAktcaBTwLaqPAkSVGAWVJNAl4FlUmgZ5FtUmAZWIRaBjkTkJImwPhonzQPhQkbgt45VunLQHPCnF9ICgryOuh3+8DZ1ZpX+9Ubl8GPCsl50ffA+sBQC7jYbAbYE7SW8BfpLvN1sMOh2ngztPP7I7cIY0D9//scHqlU3apRgE/PY1BMSCjgE4Xl2IikluV9fBz7lWRvQ1jj5EDwwBFyDMQHtcrA/OAV0pyQSnK9ZTlKErwFCUcKaqmp6gapaiqdCBSlJzcHzwgmJkN1iU1AAAAAElFTkSuQmCC" class="cloud cloud-g" alt>
                <img src="/img/misc/mountain.png" alt class="limbo-bg">
                <div class="bg-star">
                    <div class="l-star e-r"></div>
                    <div class="l-star s-p"></div>
                    <div class="l-star r-p"></div>
                </div>
                <div class="game-rocket notranslate">
                    <div class="rocket-number"><span class="rocket_payout">x1.00</span>
                    <div class="rocket-boom"></div>
                </div>
                <div class="rocket-wrap fire">
                    <div class="rocket-img">
                        <img src="/img/misc/rocket.png" alt></div>
                        <div class="rocket-fire"></div>
                    </div>
                </div>
            </div>
            <div class="limbo-footer">
                <div class="row">
                    <div class="col-6 pr-2">
                        <div>${$.lang('general.target_payout')}</div>
                        <input type="number" oninput="$.triggerSidebarUpdate()" value="2.00" step=".01" placeholder="${$.lang('general.target_payout')}" id="target_payout">
                    </div>
                    <div class="col-6 pl-2">
                        <div>${$.lang('general.win_chance')}</div>
                        <input type="number" oninput="$.triggerSidebarUpdate()" value="50.00" step=".01" placeholder="${$.lang('general.win_chance')}" id="win_chance">
                    </div>
                </div>
            </div>
        `);

        let latest = 0, interval = setInterval(() => {
            if(!window.location.pathname.includes('limbo')) {
                clearInterval(interval);
                return;
            }

            latest++;
            $('.bg-star').attr('class', `bg-star show_${latest}`);
            if(latest >= 3) latest = 0;
        }, 10000);

        $('#target_payout, #win_chance').keypress(function(event) {
            if ((event.which !== 46 || $(this).val().indexOf('.') !== -1) && (event.which < 48 || event.which > 57)) event.preventDefault();
        });

        $('#target_payout').on('input', function() {
            const value = parseFloat($(this).val());
            if(isNaN(value) || value < 1.01 || value > 1000000) return;
            $('#win_chance').val(calculate(value).toFixed(8));
        });

        $('#win_chance').on('input', function() {
            const value = parseFloat($(this).val());
            if(isNaN(value) || value < 0.000099 || value > 98) return;
            $('#target_payout').val(calculate(value).toFixed(8));
        });
    }
}, function() {
    return {
        'target_payout': parseFloat($('#target_payout').val())
    };
}, function(response) {
    let win = response.game.win;

    $.playSound('/sounds/flying.mp3');

    $('.rocket-wrap').addClass('flying');

    const delay = 200;

    $('.rocket_payout').attr('class', 'rocket_payout').animateNumber({
        number: response.game.data.number,
        numberStep: function(now, tween) {
            $(tween.elem).html(`${now.toFixed(2)}x`);
        }
    }, delay * 3);

    $('.rocket-wrap').addClass('flying');

    setTimeout(() => {
        $('.rocket-wrap, .rocket-boom').addClass('boom');

        $.playSound('/sounds/boom.mp3');

        setTimeout(function () {
            $.playSound(`/sounds/${win ? 'guessed' : 'lose'}.mp3`);
            $.blockPlayButton(false);

            $('.rocket_payout').toggleClass('text-danger', !win).toggleClass('text-success', win);

            $.history().add(function (e) {
                e.toggleClass(`text-${win ? 'success' : 'danger'}`).html(response.server_seed.result[0].toFixed(2));
            });

            $('.rocket-wrap').removeClass('flying').removeClass('boom');
            $('.rocket-boom').removeClass('boom');
        }, delay * 2);
    }, delay);
}, function(error) {
    $.error($.lang('general.error.unknown_error', {'code': error}))
});

$.on('/game/limbo', function() {
    $.render('limbo');

    $.sidebar(function(component) {
        component.bet();
        component.profit();

        component.autoBets();
        component.play();

        component.footer().help().sound().stats();
        component.history('limbo', true);
    }, function() {
        const target = parseFloat($('#target_payout').val()), win_chance = parseFloat($('#win_chance').val());
        if(isNaN(target) || isNaN(win_chance) || target < 1.01 || target > 1000000 || win_chance < 0.000099 || win_chance > 98) {
            if(isNaN(target) || target < 1.01 || target > 1000000) $('#target_payout').toggleClass('error', true);
            if(isNaN(win_chance) || win_chance < 0.000099 || win_chance > 98) $('#win_chance').toggleClass('error', true);
            return;
        }

        $('#target_payout').toggleClass('error', false);
        $('#win_chance').toggleClass('error', false);

        $.sidebarData().profit(($.sidebarData().bet() * target).toFixed(8));
    });
}, ['/css/pages/limbo.css']);

const calculate = function(value) {
    return (1000000 / value) / 10000;
};
