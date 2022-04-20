import bitcoin from 'bitcoin-units';
import * as workerTimers from 'worker-timers';

let currentTab = null;
const load = (type) => {
    if($.isGuest() && type === 'discord') return $.auth();

    $('[data-bonus-toggle]').removeClass('active');
    $(`[data-bonus-toggle="${type}"]`).addClass('active');

    $('[data-bonus-modal-contents]').fadeOut('fast', () => {
        $('[data-bonus-modal-contents]').html(window.loader()).show();
        $.get(`/modals.bonus.${type}`, function (response) {
            currentTab = type;
            $('[data-bonus-modal-contents]').hide().html(response).fadeIn('fast');

            const modal = new Modal();
            switch (type) {
                case 'discord':
                    modal.discord();
                    break;
                case 'promo':
                    modal.promocode();
                    break;
                case 'wheel':
                    modal.wheel();
                    break;
                case 'partner':
                    modal.partner();
                    break;
            }
        });
    });
};

$.on('/bonus', function() {
    load('wheel');

    $('[data-bonus-toggle]').on('click', function() {
        load($(this).data('bonus-toggle'));
    });

    if('serviceWorker' in navigator) {
        const urlBase64ToUint8Array = function(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
            return outputArray
        };

        const subscribe = function() {
            navigator.serviceWorker.ready.then(registration => {
                const options = { userVisibleOnly: true };
                const vapidPublicKey = window.Laravel.vapidPublicKey;

                if(vapidPublicKey) options.applicationServerKey = urlBase64ToUint8Array(vapidPublicKey);

                $('.bonus-overlay').fadeIn('fast');
                registration.pushManager.subscribe(options).then(subscription => {
                    updateSubscription(subscription);
                    $('.bonus-overlay').fadeOut('fast');
                }).catch(e => {
                    $('.bonus-overlay').fadeOut('fast');
                    if(Notification.permission === 'denied') {
                        console.log('Permission for Notifications was denied');
                        $.error($.lang('general.error.disabled_notifications'));
                    } else {
                        console.error('Unable to subscribe to push', e);
                    }
                });
            });
        };

        const updateSubscription = function(subscription) {
            const key = subscription.getKey('p256dh');
            const token = subscription.getKey('auth');
            const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
            const data = {
                endpoint: subscription.endpoint,
                publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
                contentEncoding
            };

            $.request('subscription/update', data).then(() => $('[data-notification-bonus]').stop().slideUp('fast', () => $('[data-notification-bonus]').remove()));
        };

        navigator.serviceWorker.register('/sw.js', { scope: '/' }).then(() => {
            if(!('showNotification' in ServiceWorkerRegistration.prototype)) {
                console.error('Notifications aren\'t supported');
                return;
            }

            if(!('PushManager' in window)) {
                console.error('Push messaging isn\'t supported');
                return;
            }

            navigator.serviceWorker.ready.then(registration => {
                registration.pushManager.getSubscription().then(subscription => {
                    if(!subscription) return;

                    updateSubscription(subscription);
                }).catch(e => {
                    console.error('Error during getSubscription()', e);
                });
            });
        });

        $('[data-notification-bonus]').slideDown('fast');
        $('[data-notification-bonus]').on('click', function() {
            if($.isGuest()) {
                $.auth();
                return;
            }

            subscribe();
        });
    } else console.error('ServiceWorker isn\'t supported');
}, ['/css/pages/bonus.css']);

$(document).on('wager:currencyChange', () => {
    load(currentTab);
});

class Modal {

    wheel() {
        const v = window.Laravel.currency[$.currency()].bonusWheel, rewards = [
            {
                value: bitcoin(v, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#f46e42'
            },
            {
                value: bitcoin(v * 1.15, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#508bf0'
            },
            {
                value: bitcoin(v * 1.3, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#df1347'
            },
            {
                value: bitcoin(v * 1.15, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#d1d652'
            },
            {
                value: bitcoin(v * 1.5, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#ffc645'
            },
            {
                value: bitcoin(v, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#f46e42'
            },
            {
                value: bitcoin(v * 2, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#508bf0'
            },
            {
                value: bitcoin(v, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#df1347'
            },
            {
                value: bitcoin(v * 1.15, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#d1d652'
            },
            {
                value: bitcoin(v * 1.3, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#ffc645'
            },
            {
                value: bitcoin(v * 1.15, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#f46e42'
            },
            {
                value: bitcoin(v * 1.5, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#508bf0'
            },
            {
                value: bitcoin(v, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#df1347'
            },
            {
                value: bitcoin(v * 2, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#d1d652'
            }
        ];
        let slides = [];
        _.forEach(rewards, function(reward) {
            slides.push({
                text: `${reward.value} <i class="${window.Laravel.currency[$.currency()].icon}" style="color: ${reward.color}"></i>`,
                value: slides.length,
                border: {
                    radius: 3.25,
                    fill: reward.color
                }
            });
        });

        $('.wheel').wheel({
            slices: slides,
            selector: 'value',
            width: 350,
            text: {
                color: "white",
                size: 12,
                offset: 5,
                arc: false
            },
            outer: {
                width: 0,
                color: 'transparent'
            },
            inner: {
                width: 11,
                color: '#222127'
            },
            line: {
                width: 3,
                color: '#222127'
            },
            slice: {
                background: '#2a2a2f'
            }
        });

        $('.wheel').wheel('onStep', function() {
            $.playSound('/sounds/tick.mp3');
        });

        $('.wheel').wheel('onComplete', function() {
            timeout();
            $('.wheelBlock').fadeIn('fast');
        });

        $('[data-bonus-modal-contents] .btn').on('click', function() {
            if($.isGuest()) return $.auth();
            if($(this).hasClass('disabled')) return;
            $(this).toggleClass('disabled', true);

            $.request('promocode/bonus').then(function(response) {
                $('.wheelBlock').fadeOut('fast');
                window.next = response.next;
                $('.wheel').wheel('start', response.slice);
            }, function(error) {
                $('[data-bonus-modal-contents] .btn').toggleClass('disabled', false);
                if(error === 2) $.error($.lang('general.error.should_have_empty_balance'));
                else $.error($.lang('general.error.unknown_error', { code: error }));
            });
        });
    }

    partner() {
        const v = window.Laravel.currency[$.currency()].referralBonusWheel, rewards = [
            {
                value: bitcoin(v, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#f46e42'
            },
            {
                value: bitcoin(v * 1.15, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#508bf0'
            },
            {
                value: bitcoin(v * 1.3, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#df1347'
            },
            {
                value: bitcoin(v * 1.15, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#d1d652'
            },
            {
                value: bitcoin(v * 1.5, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#ffc645'
            },
            {
                value: bitcoin(v, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#f46e42'
            },
            {
                value: bitcoin(v * 2, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#508bf0'
            },
            {
                value: bitcoin(v, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#df1347'
            },
            {
                value: bitcoin(v * 1.15, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#d1d652'
            },
            {
                value: bitcoin(v * 1.3, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#ffc645'
            },
            {
                value: bitcoin(v * 1.15, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#f46e42'
            },
            {
                value: bitcoin(v * 1.5, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#508bf0'
            },
            {
                value: bitcoin(v, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#df1347'
            },
            {
                value: bitcoin(v * 2, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                color: '#d1d652'
            }
        ];
        let slides = [];
        _.forEach(rewards, function(reward) {
            slides.push({
                text: `${reward.value} <i class="${window.Laravel.currency[$.currency()].icon}" style="color: ${reward.color}"></i>`,
                value: slides.length,
                border: {
                    radius: 3.25,
                    fill: reward.color
                }
            });
        });

        $('.wheel').wheel({
            slices: slides,
            selector: 'value',
            width: 350,
            text: {
                color: "white",
                size: 12,
                offset: 5,
                arc: false
            },
            outer: {
                width: 0,
                color: 'transparent'
            },
            inner: {
                width: 11,
                color: '#222127'
            },
            line: {
                width: 3,
                color: '#222127'
            },
            slice: {
                background: '#2a2a2f'
            }
        });

        $('.wheel').wheel('onStep', function() {
            $.playSound('/sounds/tick.mp3');
        });

        $('.wheel').wheel('onComplete', function() {
            $('.wheelBlock').fadeIn('fast');
        });

        $('[data-bonus-modal-contents] .btn').on('click', function() {
            if($.isGuest()) return $.auth();
            if($(this).hasClass('disabled')) return;
            $(this).toggleClass('disabled', true);

            $.request('promocode/partner_bonus').then(function(response) {
                $('.wheel').wheel('start', response.slice);
                $('.wheelBlock').fadeOut('fast');
            }, function(error) {
                $('[data-bonus-modal-contents] .btn').toggleClass('disabled', false);
                if(error === 1) {
                    $('.bonus-overlay').click();
                    redirect('/partner');
                } else $.error($.lang('general.error.unknown_error', { code: error }));
            });
        });
    }

    promocode() {
        $('#activate').on('click', function() {
            if($.isGuest()) return $.auth();
            if($(this).hasClass('disabled')) return;
            $(this).addClass('disabled');

            $.request('promocode/activate', { code: $('#code').val() }).then(function() {
                $('#activate').removeClass('disabled');
                $.success($.lang('bonus.promo.success'));
            }, function(error) {
                if(error === 1) $.error($.lang('bonus.promo.invalid'));
                if(error === 2) $.error($.lang('bonus.promo.expired_time'));
                if(error === 3) $.error($.lang('bonus.promo.expired_usages'));
                if(error === 4) $.error($.lang('bonus.promo.used'));
                if(error === 5) $.error($.lang('general.error.promo_limit'));
                if(error === 7) $.error($.lang('general.error.vip_only_promocode'));

                $('#activate').removeClass('disabled');
            });
        });
    }

    discord() {
        $('[data-check-subscription]').on('click', function() {
            if($.isGuest()) return $.auth();
            if($(this).hasClass('disabled')) return;
            $(this).addClass('disabled');

            $.request('/auth/discord_bonus').then(function() {
                $.success($.lang('bonus.discord.success'));
                redirect(window.location.pathname);
            }, function(error) {
                $.error($.lang('bonus.discord.error.'+error));
                $('[data-check-subscription]').removeClass('disabled');
            });
        });
    }

}

let interval = null;
window.timeout = function() {
    if(interval != null) {
        workerTimers.clearInterval(interval);
        interval = null;
    }

    if(window.next !== undefined && +new Date() / 1000 < window.next) {
        const timer = function() {
            const diff = ((window.next - (Date.now() / 1000)) | 0);
            let minutes = ((diff % 3600) / 60) | 0;
            let seconds = (diff % 60) | 0;

            if(minutes === 0 && seconds < 1) {
                workerTimers.clearInterval(interval);
                $('[data-bonus-modal-contents] .btn').toggleClass('disabled', false);
                $('#reload').html('3:00');
                return;
            }

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            $('#reload').html(`${minutes}:${seconds}`);
            $('.wheelBlock .btn').toggleClass('disabled', true);
        };

        interval = workerTimers.setInterval(function() {
            if($('#reload').length === 0) {
                workerTimers.clearInterval(interval);
                return;
            }

            timer();
        }, 1000);
        timer();
    } else {
        $('#reload').html('3:00');
        $('.wheelBlock .btn').toggleClass('disabled', false);
    }
};
