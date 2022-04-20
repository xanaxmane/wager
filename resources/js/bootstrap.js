window._ = require('lodash');
window.Popper = require('popper.js').default;
window.$ = window.jQuery = require('jquery');

require('./cookie');
require('./request');
require('./lang');

require('bootstrap');
require('select2');
require('./superwheel');
require('jquery-ui/ui/widgets/slider');
require('jquery-ui-touch-punch');
require('overlayscrollbars');
require('bootstrap4-toggle');
require('datatables.net');
require('jquery-contextmenu');
require('bootstrap4-toggle');

require('./icons');
require('./modals/modals');

import * as workerTimers from 'worker-timers';

$.ajaxPrefilter(function(options) {
    if(options.type === 'GET' && options.dataType === 'script') options.cache = true;
});

$.mixManifest = function(asset) {
    return window._mixManifest[asset] ?? asset;
}

$.isGuest = function() {
    return window.Laravel.userId == null;
};

$.userId = function() {
    return window.Laravel.userId;
};

$.randomId = function() {
    return '_' + Math.random().toString(36).substr(2, 64) + ($.isGuest() ? 'g' : $.userId());
};

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';

window.io = require('socket.io-client');

window.loader = () => {
    let id = $.randomId(), e = `
    <div class="dice-loader dice-loader--3" id="${id}">
        <div class="dice-loader__dot"></div>
        <div class="dice-loader__dot"></div>
        <div class="dice-loader__dot"></div>
        <div class="dice-loader__dot"></div>
        <div class="dice-loader__dot"></div>
        <div class="dice-loader__dot"></div>
    </div>`, index = 0, increment = () => {
        if(!$(`#${id}`).is(':visible')) return;

        index++;
        if(index === 7) index = 1;
        $(`#${id}`).attr('class', `dice-loader dice-loader--${index}`);
    };

    setInterval(increment, 300);
    return e;
}

$('.pageLoader .loader').append(window.loader());

let assetsLoaded = false, successfullyGrantedToken = false, connectedToSocket = false, pingSuccessful = false;
const reconnect = function() {
    const error = function(callback) {
        let secondsLeft = 5 + 1;
        const timer = function() {
            secondsLeft--;
            $('.pageLoader .error').html($.lang('general.error.token_grant_error', { seconds: secondsLeft })).fadeIn('fast');

            if(secondsLeft <= 0) {
                $('.pageLoader .error').html($.lang('general.error.token_grant_reconnecting'));
                callback();
            } else setTimeout(timer, 1000);
        };
        timer();
    };

    const grantToken = (callback) => {
        $.request('/auth/token', {
            refresh: $.getCookie('token') == null
        }).then(function(response) {
            if(response.token === '-' && !$.isGuest()) {
                grantToken(callback);
                console.warn('Got guest token while being authenticated, sending request again...');
                return;
            }

            callback(response);
        }, () => reconnect);
    };

    grantToken((response) => {
        $.setBearer(response.token);

        window.Echo = new Echo({
            broadcaster: 'socket.io',
            host: `${window.location.hostname}:2096`,
            auth: {
                headers: {
                    Authorization: `Bearer ${response.token}`
                }
            }
        });

        $.registerWhisperListener();

        let connectingMs = 0, shownConnectionMessage = false, connectionInterval = workerTimers.setInterval(function() {
            if(window.Echo.connector.socket.connected) {
                connectedToSocket = true;
                workerTimers.clearInterval(connectionInterval);

                $.whisper('Ping');

                setTimeout(() => {
                    const ping = () => $.whisper('Ping', {}, 3000).then(() => pingSuccessful = true, () => error(ping));
                    ping();
                }, 1000);
            }

            connectingMs += 10;
            if(connectingMs >= 2000 && assetsLoaded && successfullyGrantedToken && !shownConnectionMessage) {
                $('.pageLoader .error').html($.lang('general.error.websocket_connect_error')).fadeIn('fast');
                shownConnectionMessage = true;
            }
        }, 10);

        if ($.getCookie('token') == null) $.setCookie('token', '', 365 - 1);

        $.getScript($.mixManifest('/js/app.js'), function () {
            successfullyGrantedToken = true;
        });
    });
};

reconnect();

$(window).on('load', function() {
    assetsLoaded = true;
});

const unloadLoader = workerTimers.setInterval(function() {
    if(assetsLoaded && successfullyGrantedToken && connectedToSocket && pingSuccessful) {
        $('.pageLoader').fadeOut('fast');
        workerTimers.clearInterval(unloadLoader);
    }
}, 10);
