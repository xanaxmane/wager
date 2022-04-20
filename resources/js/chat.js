import bitcoin from 'bitcoin-units';
import * as workerTimers from 'worker-timers';
const xssFilters = require('xss-filters');

const commands = {
    'user [name]': function(args) {
        $.request('user/find', { name: args[0].replace('.', '') })
            .then((response) => window.open(`/user/${response.id}`, '_blank'), () => $.error($.lang('general.error.unknown_user')));
    },
    'bet [id]': function(args) {
        $.request('game/find', { 'id': args[0] })
            .then((response) => $.overview(response.id, response.game), () => $.error($.lang('general.error.unknown_game')));
    },
    'ignore [name]': function(args) {
        $.request('user/ignore', { name: args[0].replace('.', '') })
            .then((response) => {
                window.Laravel.ignore.push(response.id);
                $.success($.lang('general.ignore'))
            }, (error) => {
                switch (error) {
                    case 1: return $.error($.lang('general.error.unknown_user'));
                    case 2: return $.error($.lang('general.ignored_already'));
                }
            });
    },
    'unignore [name]': function(args) {
        $.request('user/unignore', { name: args[0].replace('.', '') })
            .then((response) => {
                _.remove(window.Laravel.ignore, (e) => e === response.id);
                $.success($.lang('general.unignore'));
            }, (error) => {
                switch (error) {
                    case 1: return $.error($.lang('general.error.unknown_user'));
                    case 2: return $.error($.lang('general.not_ignored'));
                }
            });
    },
    'tip': function() {
        $.modal('tip_modal').then(function() {
            $.updateBalanceSelector();
            $(`.currency-selector-withdraw`).val($.currency()).trigger('change');
        });
    },
    'rain': function() {
        $(`.currency-selector-withdraw`).val($.currency()).trigger('change');
        $.modal('rain_modal').then(function() {
            $.updateBalanceSelector();
            $(`.currency-selector-withdraw`).val($.currency()).trigger('change');
        });
    },
};

$.formatName = function(name) {
    if(name.count(" ") > 0) {
        name = `${name.split(" ")[0]} ${name.split(" ")[1].substr(0, 1)}.`;
    }
    return xssFilters.inHTMLData(name);
};

$.addChatMessage = function(message) {
    initScrollbars();

    if(message.type === 'rain') {
        if(message.data.from !== undefined && window.Laravel.chat_channel !== message.channel) return;

        let users = '', month = new Date().getMonth(), summer = !(month === 11 || month === 0 || month === 1);
        _.forEach(message.data.users, function(e) {
            users += `<a href="/user/${e._id}" class="disable-pjax" target="_blank">${$.formatName(e.name)}</a>${message.data.users.indexOf(e) === message.data.users.length - 1 ? '' : ', '}`;
        });

        $(`.chat .messages .os-content`).append(`
            <div class="message rain_bot">
               ${message.data.from === undefined ? '' : `
                    <div class="avatar">
                        <img src="${message.data.from.avatar}" alt onclick="redirect('/user/${message.data.from._id}')">
                        ${message.vipLevel > 0 ? `<div class="vipRank" onclick="$.vip()" data-toggle="tooltip" data-placement="left" title="${$.lang(`vip.rank.level`, { level: $.lang(`vip.rank.${message.vipLevel}`) })}">
                            ${$.vipIcon(message.vipLevel)}
                        </div>` : ''}
                    </div>
               `}
                <div class="message-content">
                    <div class="content">
                        <div class="rain_users">${users}</div>
                        <div class="mt-2 rain_desc">${$.lang(`general.${summer ? 'rain' : 'snow'}`, {
                            sum: bitcoin(message.data.reward, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
                            currency: window.Laravel.currency[message.data.currency].name
                        })}</div>
                        ${summer ? `<div class="rain front-row"></div>
                        <div class="rain back-row"></div>` : `<div class="snow-back"></div>`}
                    </div>
                </div>
            </div>
        `);

        makeItSnow();
        makeItRain();
    }

    if(message.type === 'quiz') {
        $(`.chat .messages .os-content`).append(`
            <div class="message quiz" data-message-type="quiz">
                <div class="message-content">
                   <div class="quiz_header">
                        ${$.lang('general.quiz')}
                    </div>
                    <div class="content">
                        ${message.data.question}
                    </div>
                </div>
            </div>
        `);
    }

    if(message.type === 'tip') {
        if(window.Laravel.chat_channel !== message.channel) return;

        $(`.chat .messages .os-content`).append(`
            <div class="message tip" data-message-type="tip">
                <div class="avatar">
                    <img src="${message.data.from.avatar}" alt onclick="redirect('/user/${message.data.from._id}')">
                    ${message.vipLevel > 0 ? `<div class="vipRank" onclick="$.vip()" data-toggle="tooltip" data-placement="left" title="${$.lang(`vip.rank.level`, { level: $.lang(`vip.rank.${message.vipLevel}`) })}">
                        ${$.vipIcon(message.vipLevel)}
                    </div>` : ''}
                </div>
                <div class="message-content">
                    <div class="tip_header">
                        ${$.lang('general.tip')}
                    </div>
                    <div class="content">
                        ${$.lang('general.tip_chat', {
            link: `/user/${message.data.from._id}`,
            name: $.formatName(message.data.from.name),
            value: bitcoin(parseFloat(message.data.amount), 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8),
            icon: window.Laravel.currency[message.data.currency].icon,
            style: window.Laravel.currency[message.data.currency].style,
            tolink: `/user/${message.data.to._id}`,
            toname: $.formatName(message.data.to.name)
        })}
                    </div>
                </div>
            </div>
        `);
    }

    if(message.type === 'quiz_answered') {
        $(`.chat .messages .os-content`).append(`
            <div class="message quiz" data-message-type="quiz">
                <div class="message-content">
                    <div class="quiz_header">
                        ${$.lang('general.quiz')}
                    </div>
                    <div class="content">
                        ${message.data.question}
                        <div class="answer">
                            <div class="answer_header">${$.lang('general.quiz_answer')}</div>
                            ${message.data.correct}
                            <div class="answer_user"><span>${$.lang('general.quiz_user')}</span> <a class="disable-pjax" href="/user/${message.data.user._id}" target="_blank">${message.data.user.name}</a></div>
                            <div>${bitcoin(message.data.reward, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)} ${window.Laravel.currency[message.data.currency].name}</div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    if(message.type === 'message') {
        if(window.Laravel.ignore.includes(message.user._id) || window.Laravel.chat_channel !== message.channel) return;

        let userMessage = xssFilters.inHTMLData(message.data);
        if(userMessage.includes('@')) userMessage = userMessage.replace('@'+window.Laravel.userName, '<span class="highlight">@'+xssFilters.inHTMLData(window.Laravel.userName)+'</span>');

        if($(`.chat .messages .os-content .message`).last().attr('data-message-user-id') === message.user._id
            && $(`.chat .messages .os-content .message`).last().attr('data-message-type') === 'message') {
            $(`.chat .messages .os-content .message`).last().find('.content').append(`<div id="${message._id}">${userMessage}</div>`);
        } else $(`.chat .messages .os-content`).append(`
            <div id="${message._id}" class="message from-${message.user.access}" data-message-type="message" data-message-user-id="${message.user._id}">
                <div class="avatar">
                    <img src="${message.user.avatar}" alt onclick="redirect('/user/${message.user._id}')">
                    ${message.vipLevel > 0 ? `<div class="vipRank" onclick="$.vip()" data-toggle="tooltip" data-placement="left" title="${$.lang(`vip.rank.level`, { level: $.lang(`vip.rank.${message.vipLevel}`) })}">
                        ${$.vipIcon(message.vipLevel)}
                    </div>` : ''}
                </div>
                <div class="message-content">
                    <div class="content">
                        <span class="name" onclick="redirect('/user/${message.user._id}')">${$.formatName(message.user.name)}</span>
                        <div class="firstMessage">${userMessage}</div>
                    </div>
                </div>
            </div>
        `);
    }

    if(message.type === 'game_link') {
        $(`.chat .messages .os-content`).append(`
            <div id="${message._id}" class="message from-${message.user.access}" data-message-type="game_link" data-message-user-id="${message.user._id}">
                <div class="avatar">
                    <img src="${message.user.avatar}" alt onclick="redirect('/user/${message.user._id}')">
                    ${message.vipLevel > 0 ? `<div class="vipRank" onclick="$.vip()" data-toggle="tooltip" data-placement="left" title="${$.lang(`vip.rank.level`, { level: $.lang(`vip.rank.${message.vipLevel}`) })}">
                        ${$.vipIcon(message.vipLevel)}
                    </div>` : ''}
                </div>
                <div class="message-content">
                    <div class="content">
                        <span class="name" onclick="redirect('/user/${message.user._id}')">${$.formatName(message.user.name)}</span>
                         <div class="game-link" onclick="$.overview('${message.data._id}', '${message.data.game}')">
                            <div class="icon"><i class="${message.data.icon ?? 'fal fa-question-circle'}"></i></div>
                            <div class="content">
                                <div>${message.data.game.capitalize()}: #${message.data.id}</div>
                                <div>${$.lang('general.bets.bet')}: ${bitcoin(message.data.wager, 'btc').to($.unit()).value().toFixed($.unit() === 'satoshi' ? 0 : 8)} <i class="${window.Laravel.currency[message.data.currency].icon}" style="color: ${window.Laravel.currency[message.data.currency].style}"></i></div>
                                <div>${$.lang('general.bets.win')}: ${message.data.multiplier.toFixed(2)}x</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    if(Laravel.access === 'admin' || Laravel.access === 'moderator') {
        if(message._id !== undefined) $.contextMenu({
            selector: `#${message._id}`,
            items: {
                deleteMessage: {
                    name: "Remove this message", callback: function() {
                        $.request('chat/moderate/removeMessage', { id: message._id });
                    }
                },
                deleteAllMessages: {
                    name: "Remove all messages from this user", callback: function() {
                        $.request('chat/moderate/removeAllFrom', { id: message.user._id });
                    }
                },
                mute: {
                    name: "Mute",
                    items: {
                        five: {
                            name: "5m", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 5 });
                            }
                        },
                        ten: {
                            name: "10m", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 10 })
                            }
                        },
                        fifteen: {
                            name: "15m", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 15 });
                            }
                        },
                        halfhour: {
                            name: "30m", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 30 });
                            }
                        },
                        hour: {
                            name: "1h", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 60 });
                            }
                        },
                        sixhours: {
                            name: "6h", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 60 * 6 })
                            }
                        },
                        twhours: {
                            name: "12h", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 60 * 12 });
                            }
                        },
                        day: {
                            name: "1d", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 60 * 24 });
                            }
                        },
                        week: {
                            name: "1w", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 60 * 24 * 7 });
                            }
                        },
                        month: {
                            name: "1m", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 60 * 24 * 31 });
                            }
                        },
                        year: {
                            name: "1y", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 525600 });
                            }
                        },
                        forever: {
                            name: "Forever", callback: function() {
                                $.request('chat/moderate/mute', { id: message.user._id, minutes: 525600 * 100 });
                            }
                        }
                    }
                }
            }
        });
    }

    setTimeout(function() {
        $(`.chat .messages`).overlayScrollbars().scroll({ y : "100%" });
    }, 50);
};

$.sendChatMessage = function(selector) {
    $('.chatCommands').fadeOut('fast');

    let message = $(selector).find('textarea').val();
    for(let i = 0; i < Object.keys(commands).length; i++) {
        const command = Object.keys(commands)[i];
        if($(selector).find('textarea').val().startsWith(`/${command.substr(0, command.indexOf(' '))}`)) {
            commands[command](message.includes(' ') ? message.substr(message.indexOf(' ') + 1).split(' ') : []);
            $(selector).find('textarea').val('');
            return;
        }
    }

    $(selector).find('textarea').val('');
    $.whisper('ChatMessage', {
        'message': message
    }).then(function() {}, function(error) {
        if(error === 1) $.error($.lang('chat.error.length'));
        if(error === 2) $.error($.lang('chat.error.muted'));
    });
    $('[data-user-tag]').fadeOut('fast');
    sentNotify = false;
};

$.unicodeEmoji = function(emoji) {
    const area = $('.message-send').find('textarea');
    area.val(`${area.val()}${area.val().substr(area.val().length - 1, area.val().length) === ' ' ? emoji : ` ${emoji}`}`);
};

$.unicodeEmojiInit = function() {
    $('[data-fill-emoji-target] .os-content').html('');
    const unicodeEmoji = ['😀', '😁', '😂', '🤣', '😃', '😄', '😅', '😆', '😉', '😊', '😋', '😎', '😍', '😘', '😗', '😙', '😚', '🙂', '🤗', '🤔', '😐', '😑', '😶', '🙄', '😏', '😣', '😥', '😮', '🤐', '😯', '😪', '😫', '😴', '😌', '🤓', '😛', '😜', '😝', '🤤', '😒', '😓', '😔', '😕', '🙃', '🤑', '😲', '☹', '🙁', '😖', '😞', '😟', '😤', '😢', '😭', '😦', '😧', '😨', '😩', '😬', '😰', '😱', '😳', '😵', '😡', '😠', '😇', '🤠', '🤡', '🤥', '😷', '🤒', '🤕', '🤢', '🤧', '😈', '👿', '👹', '👺', '💀', '☠', '👻', '👽', '👾', '🤖', '💩', '😺', '😸', '😹', '😻', '😼', '😽', '🙀', '😿', '😾', '🙈', '🙉', '🙊', '👦', '👦🏻', '👦🏼', '👦🏽', '👦🏾', '👦🏿', '👧', '👧🏻', '👧🏼', '👧🏽', '👧🏾', '👧🏿', '👨', '👨🏻', '👨🏼', '👨🏽', '👨🏾', '👨🏿', '👩', '👩🏻', '👩🏼', '👩🏽', '👩🏾', '👩🏿', '👴', '👴🏻', '👴🏼', '👴🏽', '👴🏾', '👴🏿', '👵', '👵🏻', '👵🏼', '👵🏽', '👵🏾', '👵🏿', '👶', '👶🏻', '👶🏼', '👶🏽', '👶🏾', '👶🏿', '👼', '👼🏻', '👼🏼', '👼🏽', '👼🏾', '👼🏿', '👮', '👮🏻', '👮🏼', '👮🏽', '👮🏾', '👮🏿', '🕵', '🕵🏻', '🕵🏼', '🕵🏽', '🕵🏾', '🕵🏿', '💂', '💂🏻', '💂🏼', '💂🏽', '💂🏾', '💂🏿', '👷', '👷🏻', '👷🏼', '👷🏽', '👷🏾', '👷🏿', '👳', '👳🏻', '👳🏼', '👳🏽', '👳🏾', '👳🏿', '👱', '👱🏻', '👱🏼', '👱🏽', '👱🏾', '👱🏿', '🎅', '🎅🏻', '🎅🏼', '🎅🏽', '🎅🏾', '🎅🏿', '🤶', '🤶🏻', '🤶🏼', '🤶🏽', '🤶🏾', '🤶🏿', '👸', '👸🏻', '👸🏼', '👸🏽', '👸🏾', '👸🏿', '🤴', '🤴🏻', '🤴🏼', '🤴🏽', '🤴🏾', '🤴🏿', '👰', '👰🏻', '👰🏼', '👰🏽', '👰🏾', '👰🏿', '🤵', '🤵🏻', '🤵🏼', '🤵🏽', '🤵🏾', '🤵🏿', '🤰', '🤰🏻', '🤰🏼', '🤰🏽', '🤰🏾', '🤰🏿', '👲', '👲🏻', '👲🏼', '👲🏽', '👲🏾', '👲🏿', '🙍', '🙍🏻', '🙍🏼', '🙍🏽', '🙍🏾', '🙍🏿', '🙎', '🙎🏻', '🙎🏼', '🙎🏽', '🙎🏾', '🙎🏿', '🙅', '🙅🏻', '🙅🏼', '🙅🏽', '🙅🏾', '🙅🏿', '🙆', '🙆🏻', '🙆🏼', '🙆🏽', '🙆🏾', '🙆🏿', '💁', '💁🏻', '💁🏼', '💁🏽', '💁🏾', '💁🏿', '🙋', '🙋🏻', '🙋🏼', '🙋🏽', '🙋🏾', '🙋🏿', '🙇', '🙇🏻', '🙇🏼', '🙇🏽', '🙇🏾', '🙇🏿', '🤦', '🤦🏻', '🤦🏼', '🤦🏽', '🤦🏾', '🤦🏿', '🤷', '🤷🏻', '🤷🏼', '🤷🏽', '🤷🏾', '🤷🏿', '💆', '💆🏻', '💆🏼', '💆🏽', '💆🏾', '💆🏿', '💇', '💇🏻', '💇🏼', '💇🏽', '💇🏾', '💇🏿', '🚶', '🚶🏻', '🚶🏼', '🚶🏽', '🚶🏾', '🚶🏿', '🏃', '🏃🏻', '🏃🏼', '🏃🏽', '🏃🏾', '🏃🏿', '💃', '💃🏻', '💃🏼', '💃🏽', '💃🏾', '💃🏿', '🕺', '🕺🏻', '🕺🏼', '🕺🏽', '🕺🏾', '🕺🏿', '👯', '🕴', '🗣', '👤', '👥', '🤺', '🏇', '⛷', '🏂', '🏌', '🏄', '🏄🏻', '🏄🏼', '🏄🏽', '🏄🏾', '🏄🏿', '🚣', '🚣🏻', '🚣🏼', '🚣🏽', '🚣🏾', '🚣🏿', '🏊', '🏊🏻', '🏊🏼', '🏊🏽', '🏊🏾', '🏊🏿', '⛹', '⛹🏻', '⛹🏼', '⛹🏽', '⛹🏾', '⛹🏿', '🏋', '🏋🏻', '🏋🏼', '🏋🏽', '🏋🏾', '🏋🏿', '🚴', '🚴🏻', '🚴🏼', '🚴🏽', '🚴🏾', '🚴🏿', '🚵', '🚵🏻', '🚵🏼', '🚵🏽', '🚵🏾', '🚵🏿', '🏎', '🏍', '🤸', '🤸🏻', '🤸🏼', '🤸🏽', '🤸🏾', '🤸🏿', '🤼', '🤼🏻', '🤼🏼', '🤼🏽', '🤼🏾', '🤼🏿', '🤽', '🤽🏻', '🤽🏼', '🤽🏽', '🤽🏾', '🤽🏿', '🤾', '🤾🏻', '🤾🏼', '🤾🏽', '🤾🏾', '🤾🏿', '🤹', '🤹🏻', '🤹🏼', '🤹🏽', '🤹🏾', '🤹🏿', '👫', '👬', '👭', '💏', '👩‍❤️‍💋‍👨', '👨‍❤️‍💋‍👨', '👩‍❤️‍💋‍👩', '💑', '👩‍❤️‍👨', '👨‍❤️‍👨', '👩‍❤️‍👩', '👪', '👨‍👩‍👦', '👨‍👩‍👧', '👨‍👩‍👧‍👦', '👨‍👩‍👦‍👦', '👨‍👩‍👧‍👧', '👨‍👨‍👦', '👨‍👨‍👧', '👨‍👨‍👧‍👦', '👨‍👨‍👦‍👦', '👨‍👨‍👧‍👧', '👩‍👩‍👦', '👩‍👩‍👧', '👩‍👩‍👧‍👦', '👩‍👩‍👦‍👦', '👩‍👩‍👧‍👧', '🏻', '🏼', '🏽', '🏾', '🏿', '💪', '💪🏻', '💪🏼', '💪🏽', '💪🏾', '💪🏿', '🤳', '🤳🏻', '🤳🏼', '🤳🏽', '🤳🏾', '🤳🏿', '👈', '👈🏻', '👈🏼', '👈🏽', '👈🏾', '👈🏿', '👉', '👉🏻', '👉🏼', '👉🏽', '👉🏾', '👉🏿', '☝', '☝🏻', '☝🏼', '☝🏽', '☝🏾', '☝🏿', '👆', '👆🏻', '👆🏼', '👆🏽', '👆🏾', '👆🏿', '🖕', '🖕🏻', '🖕🏼', '🖕🏽', '🖕🏾', '🖕🏿', '👇', '👇🏻', '👇🏼', '👇🏽', '👇🏾', '👇🏿', '✌', '✌🏻', '✌🏼', '✌🏽', '✌🏾', '✌🏿', '🤞', '🤞🏻', '🤞🏼', '🤞🏽', '🤞🏾', '🤞🏿', '🖖', '🖖🏻', '🖖🏼', '🖖🏽', '🖖🏾', '🖖🏿', '🤘', '🤘🏻', '🤘🏼', '🤘🏽', '🤘🏾', '🤘🏿', '🤙', '🤙🏻', '🤙🏼', '🤙🏽', '🤙🏾', '🤙🏿', '🖐', '🖐🏻', '🖐🏼', '🖐🏽', '🖐🏾', '🖐🏿', '✋', '✋🏻', '✋🏼', '✋🏽', '✋🏾', '✋🏿', '👌', '👌🏻', '👌🏼', '👌🏽', '👌🏾', '👌🏿', '👍', '👍🏻', '👍🏼', '👍🏽', '👍🏾', '👍🏿', '👎', '👎🏻', '👎🏼', '👎🏽', '👎🏾', '👎🏿', '✊', '✊🏻', '✊🏼', '✊🏽', '✊🏾', '✊🏿', '👊', '👊🏻', '👊🏼', '👊🏽', '👊🏾', '👊🏿', '🤛', '🤛🏻', '🤛🏼', '🤛🏽', '🤛🏾', '🤛🏿', '🤜', '🤜🏻', '🤜🏼', '🤜🏽', '🤜🏾', '🤜🏿', '🤚', '🤚🏻', '🤚🏼', '🤚🏽', '🤚🏾', '🤚🏿', '👋', '👋🏻', '👋🏼', '👋🏽', '👋🏾', '👋🏿', '👏', '👏🏻', '👏🏼', '👏🏽', '👏🏾', '👏🏿', '✍', '✍🏻', '✍🏼', '✍🏽', '✍🏾', '✍🏿', '👐', '👐🏻', '👐🏼', '👐🏽', '👐🏾', '👐🏿', '🙌', '🙌🏻', '🙌🏼', '🙌🏽', '🙌🏾', '🙌🏿', '🙏', '🙏🏻', '🙏🏼', '🙏🏽', '🙏🏾', '🙏🏿', '🤝', '🤝🏻', '🤝🏼', '🤝🏽', '🤝🏾', '🤝🏿', '💅', '💅🏻', '💅🏼', '💅🏽', '💅🏾', '💅🏿', '👂', '👂🏻', '👂🏼', '👂🏽', '👂🏾', '👂🏿', '👃', '👃🏻', '👃🏼', '👃🏽', '👃🏾', '👃🏿', '👣', '👀', '👁', '👁‍🗨', '👅', '👄', '💋', '💘', '❤', '💓', '💔', '💕', '💖', '💗', '💙', '💚', '💛', '💜', '🖤', '💝', '💞', '💟', '❣', '💌', '💤', '💢', '💣', '💥', '💦', '💨', '💫', '💬', '🗨', '🗯', '💭', '🕳', '👓', '🕶', '👔', '👕', '👖', '👗', '👘', '👙', '👚', '👛', '👜', '👝', '🛍', '🎒', '👞', '👟', '👠', '👡', '👢', '👑', '👒', '🎩', '🎓', '⛑', '📿', '💄', '💍', '💎', '🐵', '🐒', '🦍', '🐶', '🐕', '🐩', '🐺', '🦊', '🐱', '🐈', '🦁', '🐯', '🐅', '🐆', '🐴', '🐎', '🦌', '🦄', '🐮', '🐂', '🐃', '🐄', '🐷', '🐖', '🐗', '🐽', '🐏', '🐑', '🐐', '🐪', '🐫', '🐘', '🦏', '🐭', '🐁', '🐀', '🐹', '🐰', '🐇', '🐿', '🦇', '🐻', '🐨', '🐼', '🐾', '🦃', '🐔', '🐓', '🐣', '🐤', '🐥', '🐦', '🐧', '🕊', '🦅', '🦆', '🦉', '🐸', '🐊', '🐢', '🦎', '🐍', '🐲', '🐉', '🐳', '🐋', '🐬', '🐟', '🐠', '🐡', '🦈', '🐙', '🐚', '🦀', '🦐', '🦑', '🦋', '🐌', '🐛', '🐜', '🐝', '🐞', '🕷', '🕸', '🦂', '💐', '🌸', '💮', '🏵', '🌹', '🥀', '🌺', '🌻', '🌼', '🌷', '🌱', '🌲', '🌳', '🌴', '🌵', '🌾', '🌿', '☘', '🍀', '🍁', '🍂', '🍃', '🍇', '🍈', '🍉', '🍊', '🍋', '🍌', '🍍', '🍎', '🍏', '🍐', '🍑', '🍒', '🍓', '🥝', '🍅', '🥑', '🍆', '🥔', '🥕', '🌽', '🌶', '🥒', '🍄', '🥜', '🌰', '🍞', '🥐', '🥖', '🥞', '🧀', '🍖', '🍗', '🥓', '🍔', '🍟', '🍕', '🌭', '🌮', '🌯', '🥙', '🥚', '🍳', '🥘', '🍲', '🥗', '🍿', '🍱', '🍘', '🍙', '🍚', '🍛', '🍜', '🍝', '🍠', '🍢', '🍣', '🍤', '🍥', '🍡', '🍦', '🍧', '🍨', '🍩', '🍪', '🎂', '🍰', '🍫', '🍬', '🍭', '🍮', '🍯', '🍼', '🥛', '☕', '🍵', '🍶', '🍾', '🍷', '🍸', '🍹', '🍺', '🍻', '🥂', '🥃', '🍽', '🍴', '🥄', '🔪', '🏺', '🌍', '🌎', '🌏', '🌐', '🗺', '🗾', '🏔', '⛰', '🌋', '🗻', '🏕', '🏖', '🏜', '🏝', '🏞', '🏟', '🏛', '🏗', '🏘', '🏙', '🏚', '🏠', '🏡', '🏢', '🏣', '🏤', '🏥', '🏦', '🏨', '🏩', '🏪', '🏫', '🏬', '🏭', '🏯', '🏰', '💒', '🗼', '🗽', '⛪', '🕌', '🕍', '⛩', '🕋', '⛲', '⛺', '🌁', '🌃', '🌄', '🌅', '🌆', '🌇', '🌉', '♨', '🌌', '🎠', '🎡', '🎢', '💈', '🎪', '🎭', '🖼', '🎨', '🎰', '🚂', '🚃', '🚄', '🚅', '🚆', '🚇', '🚈', '🚉', '🚊', '🚝', '🚞', '🚋', '🚌', '🚍', '🚎', '🚐', '🚑', '🚒', '🚓', '🚔', '🚕', '🚖', '🚗', '🚘', '🚙', '🚚', '🚛', '🚜', '🚲', '🛴', '🛵', '🚏', '🛣', '🛤', '⛽', '🚨', '🚥', '🚦', '🚧', '🛑', '⚓', '⛵', '🛶', '🚤', '🛳', '⛴', '🛥', '🚢', '✈', '🛩', '🛫', '🛬', '💺', '🚁', '🚟', '🚠', '🚡', '🚀', '🛰', '🛎', '🚪', '🛌', '🛏', '🛋', '🚽', '🚿', '🛀', '🛀🏻', '🛀🏼', '🛀🏽', '🛀🏾', '🛀🏿', '🛁', '⌛', '⏳', '⌚', '⏰', '⏱', '⏲', '🕰', '🕛', '🕧', '🕐', '🕜', '🕑', '🕝', '🕒', '🕞', '🕓', '🕟', '🕔', '🕠', '🕕', '🕡', '🕖', '🕢', '🕗', '🕣', '🕘', '🕤', '🕙', '🕥', '🕚', '🕦', '🌑', '🌒', '🌓', '🌔', '🌕', '🌖', '🌗', '🌘', '🌙', '🌚', '🌛', '🌜', '🌡', '☀', '🌝', '🌞', '⭐', '🌟', '🌠', '☁', '⛅', '⛈', '🌤', '🌥', '🌦', '🌧', '🌨', '🌩', '🌪', '🌫', '🌬', '🌀', '🌈', '🌂', '☂', '☔', '⛱', '⚡', '❄', '☃', '⛄', '☄', '🔥', '💧', '🌊', '🎃', '🎄', '🎆', '🎇', '✨', '🎈', '🎉', '🎊', '🎋', '🎍', '🎎', '🎏', '🎐', '🎑', '🎀', '🎁', '🎗', '🎟', '🎫', '🎖', '🏆', '🏅', '🥇', '🥈', '🥉', '⚽', '⚾', '🏀', '🏐', '🏈', '🏉', '🎾', '🎱', '🎳', '🏏', '🏑', '🏒', '🏓', '🏸', '🥊', '🥋', '🥅', '🎯', '⛳', '⛸', '🎣', '🎽', '🎿', '🎮', '🕹', '🎲', '♠', '♥', '♦', '♣', '🃏', '🀄', '🎴', '🔇', '🔈', '🔉', '🔊', '📢', '📣', '📯', '🔔', '🔕', '🎼', '🎵', '🎶', '🎙', '🎚', '🎛', '🎤', '🎧', '📻', '🎷', '🎸', '🎹', '🎺', '🎻', '🥁', '📱', '📲', '☎', '📞', '📟', '📠', '🔋', '🔌', '💻', '🖥', '🖨', '⌨', '🖱', '🖲', '💽', '💾', '💿', '📀', '🎥', '🎞', '📽', '🎬', '📺', '📷', '📸', '📹', '📼', '🔍', '🔎', '🔬', '🔭', '📡', '🕯', '💡', '🔦', '🏮', '📔', '📕', '📖', '📗', '📘', '📙', '📚', '📓', '📒', '📃', '📜', '📄', '📰', '🗞', '📑', '🔖', '🏷', '💰', '💴', '💵', '💶', '💷', '💸', '💳', '💹', '💱', '💲', '✉', '📧', '📨', '📩', '📤', '📥', '📦', '📫', '📪', '📬', '📭', '📮', '🗳', '✏', '✒', '🖋', '🖊', '🖌', '🖍', '📝', '💼', '📁', '📂', '🗂', '📅', '📆', '🗒', '🗓', '📇', '📈', '📉', '📊', '📋', '📌', '📍', '📎', '🖇', '📏', '📐', '✂', '🗃', '🗄', '🗑', '🔒', '🔓', '🔏', '🔐', '🔑', '🗝', '🔨', '⛏', '⚒', '🛠', '🗡', '⚔', '🔫', '🏹', '🛡', '🔧', '🔩', '⚙', '🗜', '⚗', '⚖', '🔗', '⛓', '💉', '💊', '🚬', '⚰', '⚱', '🗿', '🛢', '🔮', '🛒', '🏧', '🚮', '🚰', '♿', '🚹', '🚺', '🚻', '🚼', '🚾', '🛂', '🛃', '🛄', '🛅', '⚠', '🚸', '⛔', '🚫', '🚳', '🚭', '🚯', '🚱', '🚷', '📵', '🔞', '☢', '☣', '⬆', '↗', '➡', '↘', '⬇', '↙', '⬅', '↖', '↕', '↔', '↩', '↪', '⤴', '⤵', '🔃', '🔄', '🔙', '🔚', '🔛', '🔜', '🔝', '🛐', '⚛', '🕉', '✡', '☸', '☯', '✝', '☦', '☪', '☮', '🕎', '🔯', '♈', '♉', '♊', '♋', '♌', '♍', '♎', '♏', '♐', '♑', '♒', '♓', '⛎', '🔀', '🔁', '🔂', '▶', '⏩', '⏭', '⏯', '◀', '⏪', '⏮', '🔼', '⏫', '🔽', '⏬', '⏸', '⏹', '⏺', '⏏', '🎦', '🔅', '🔆', '📶', '📳', '📴', '♻', '📛', '⚜', '🔰', '🔱', '⭕', '✅', '☑', '✔', '✖', '❌', '❎', '➕', '➖', '➗', '➰', '➿', '〽', '✳', '✴', '❇', '‼', '⁉', '❓', '❔', '❕', '❗', '〰', '©', '®', '™', '#️⃣', '*️⃣', '0️⃣', '1️⃣', '2️⃣', '3️⃣', '4️⃣', '5️⃣', '6️⃣', '7️⃣', '8️⃣', '9️⃣', '🔟', '💯', '🔠', '🔡', '🔢', '🔣', '🔤', '🅰', '🆎', '🅱', '🆑', '🆒', '🆓', 'ℹ', '🆔', 'Ⓜ', '🆕', '🆖', '🅾', '🆗', '🅿', '🆘', '🆙', '🆚', '🈁', '🈂', '🈷', '🈶', '🈯', '🉐', '🈹', '🈚', '🈲', '🉑', '🈸', '🈴', '🈳', '㊗', '㊙', '🈺', '🈵', '▪', '▫', '◻', '◼', '◽', '◾', '⬛', '⬜', '🔶', '🔷', '🔸', '🔹', '🔺', '🔻', '💠', '🔘', '🔲', '🔳', '⚪', '⚫', '🔴', '🔵', '🏁', '🚩', '🎌', '🏴', '🏳', '🇦🇨', '🇦🇩', '🇦🇪', '🇦🇫', '🇦🇬', '🇦🇮', '🇦🇱', '🇦🇲', '🇦🇴', '🇦🇶', '🇦🇷', '🇦🇸', '🇦🇹', '🇦🇺', '🇦🇼', '🇦🇽', '🇦🇿', '🇧🇦', '🇧🇧', '🇧🇩', '🇧🇪', '🇧🇫', '🇧🇬', '🇧🇭', '🇧🇮', '🇧🇯', '🇧🇱', '🇧🇲', '🇧🇳', '🇧🇴', '🇧🇶', '🇧🇷', '🇧🇸', '🇧🇹', '🇧🇻', '🇧🇼', '🇧🇾', '🇧🇿', '🇨🇦', '🇨🇨', '🇨🇩', '🇨🇫', '🇨🇬', '🇨🇭', '🇨🇮', '🇨🇰', '🇨🇱', '🇨🇲', '🇨🇳', '🇨🇴', '🇨🇵', '🇨🇷', '🇨🇺', '🇨🇻', '🇨🇼', '🇨🇽', '🇨🇾', '🇨🇿', '🇩🇪', '🇩🇬', '🇩🇯', '🇩🇰', '🇩🇲', '🇩🇴', '🇩🇿', '🇪🇦', '🇪🇨', '🇪🇪', '🇪🇬', '🇪🇭', '🇪🇷', '🇪🇸', '🇪🇹', '🇪🇺', '🇫🇮', '🇫🇯', '🇫🇰', '🇫🇲', '🇫🇴', '🇫🇷', '🇬🇦', '🇬🇧', '🇬🇩', '🇬🇪', '🇬🇫', '🇬🇬', '🇬🇭', '🇬🇮', '🇬🇱', '🇬🇲', '🇬🇳', '🇬🇵', '🇬🇶', '🇬🇷', '🇬🇸', '🇬🇹', '🇬🇺', '🇬🇼', '🇬🇾', '🇭🇰', '🇭🇲', '🇭🇳', '🇭🇷', '🇭🇹', '🇭🇺', '🇮🇨', '🇮🇩', '🇮🇪', '🇮🇱', '🇮🇲', '🇮🇳', '🇮🇴', '🇮🇶', '🇮🇷', '🇮🇸', '🇮🇹', '🇯🇪', '🇯🇲', '🇯🇴', '🇯🇵', '🇰🇪', '🇰🇬', '🇰🇭', '🇰🇮', '🇰🇲', '🇰🇳', '🇰🇵', '🇰🇷', '🇰🇼', '🇰🇾', '🇰🇿', '🇱🇦', '🇱🇧', '🇱🇨', '🇱🇮', '🇱🇰', '🇱🇷', '🇱🇸', '🇱🇹', '🇱🇺', '🇱🇻', '🇱🇾', '🇲🇦', '🇲🇨', '🇲🇩', '🇲🇪', '🇲🇫', '🇲🇬', '🇲🇭', '🇲🇰', '🇲🇱', '🇲🇲', '🇲🇳', '🇲🇴', '🇲🇵', '🇲🇶', '🇲🇷', '🇲🇸', '🇲🇹', '🇲🇺', '🇲🇻', '🇲🇼', '🇲🇽', '🇲🇾', '🇲🇿', '🇳🇦', '🇳🇨', '🇳🇪', '🇳🇫', '🇳🇬', '🇳🇮', '🇳🇱', '🇳🇴', '🇳🇵', '🇳🇷', '🇳🇺', '🇳🇿', '🇴🇲', '🇵🇦', '🇵🇪', '🇵🇫', '🇵🇬', '🇵🇭', '🇵🇰', '🇵🇱', '🇵🇲', '🇵🇳', '🇵🇷', '🇵🇸', '🇵🇹', '🇵🇼', '🇵🇾', '🇶🇦', '🇷🇪', '🇷🇴', '🇷🇸', '🇷🇺', '🇷🇼', '🇸🇦', '🇸🇧', '🇸🇨', '🇸🇩', '🇸🇪', '🇸🇬', '🇸🇭', '🇸🇮', '🇸🇯', '🇸🇰', '🇸🇱', '🇸🇲', '🇸🇳', '🇸🇴', '🇸🇷', '🇸🇸', '🇸🇹', '🇸🇻', '🇸🇽', '🇸🇾', '🇸🇿', '🇹🇦', '🇹🇨', '🇹🇩', '🇹🇫', '🇹🇬', '🇹🇭', '🇹🇯', '🇹🇰', '🇹🇱', '🇹🇲', '🇹🇳', '🇹🇴', '🇹🇷', '🇹🇹', '🇹🇻', '🇹🇼', '🇹🇿', '🇺🇦', '🇺🇬', '🇺🇲', '🇺🇸', '🇺🇾', '🇺🇿', '🇻🇦', '🇻🇨', '🇻🇪', '🇻🇬', '🇻🇮', '🇻🇳', '🇻🇺', '🇼🇫', '🇼🇸', '🇽🇰', '🇾🇪', '🇾🇹', '🇿🇦', '🇿🇲', '🇿🇼']
    $.chain(unicodeEmoji.length, 1, function(i) {
        $('[data-fill-emoji-target] .os-content').append(`
            <div class="emoji" onclick="$.unicodeEmoji('${unicodeEmoji[i - 1]}')">${unicodeEmoji[i - 1]}</div>
        `);
    });
};

$.setChatChannel = function(channel) {
    $('.chat .messages .os-content').html(`<div class="loader">${window.loader()}</div>`);
    $('.chat_channel').html(channel.toUpperCase().substr(channel.indexOf('_') + 1));
    window.Laravel.chat_channel = channel;

    $.request('chat/history', { channel: channel }).then(function(response) {
        $('.chat .messages .os-content').html('');

        _.forEach(response.reverse(), function(message) {
            $.addChatMessage(message);
        });

        $(`.chat .messages`).overlayScrollbars().scroll({ y : "100%" });
    });
}

$(document).ready(function() {
    initScrollbars();

    $.setChatChannel(window.Laravel.chat_channel);

    $('.emoji-container .content').overlayScrollbars({
        scrollbars: {
            autoHide: 'leave'
        },
        className: "os-theme-thin-light"
    });

    $(document).click(function(event) {
        if(!$(event.target).closest('.emoji-container').length && !$(event.target).closest('#emoji-container-toggle').length) $('.emoji-container').removeClass('active');
    });

    _.forEach(Object.keys(commands), function(command) {
        const e = $(`<div class="chatCommand"><strong>/${command}</strong> ${$.lang(`general.chat_commands./${command.substring(0, !command.includes(' ') ? command.length : command.indexOf(' '))}`)}</div>`);
        $('.chatCommands').append(e);
        e.on('click', function() {
            command.includes(' ') ? $('.text-message').val(`/${command.substring(0, command.indexOf(' '))} `).focus() : commands[command]();
            $('.chatCommands').fadeOut('fast');
        });
    });

    $('#chatCommandsToggle').on('click', function() {
        $('.chatCommands').fadeToggle('fast');
    });

    $(document).on('click', '.rain_modal .btn', function() {
        $('.rain_modal').uiBlocker(true);
        $.request('chat/rain', { amount: $('#rainamount').val(), users: $('#rainusers').val() }).then(function() {
            $.modal('rain_modal').then((e) => e.uiBlocker(false));
        }, function(error) {
            $('.rain_modal').uiBlocker(false);
            if(error === 1) $.error($.lang('general.chat_commands.modal.rain.invalid_users_length'));
            if(error === 2) $.error($.lang('general.chat_commands.modal.rain.invalid_amount'));
        });
    });

    $(document).on('click', '.tip_modal .btn', function() {
        $('.tip_modal').uiBlocker(true);
        $.request('chat/tip', { amount: $('#tipamount').val(), user: $('#tipname').val(), public: $('#tippublic').is(':checked') }).then(function() {
            $.modal('tip_modal').then((e) => e.uiBlocker(false));
        }, function(error) {
            $('.tip_modal').uiBlocker(false);
            if(error === 1) $.error($.lang('general.chat_commands.modal.tip.invalid_name'));
            if(error === 2) $.error($.lang('general.chat_commands.modal.tip.invalid_amount'));
        });
    });

    if(Laravel.access === 'admin') {
        workerTimers.setInterval(function() {
            $.whisper('OnlineUsers').then(function(response) {
                $('.online').html(response.length);
            });
        }, 15000);
    }
});

let sentNotify = false;
const initScrollbars = function() {
    if($('.chat .messages .os-content').length === 0) {
        $('.message-send textarea').on('input', function() {
            if($(this).val().length <= 1) sentNotify = false;
            if($(this).val().includes('@') && !sentNotify) {
                $('[data-user-tag]').fadeIn('fast');

                const tags = $(this).val().match(/@\w+/g);
                if((tags !== null && tags.length > 0) || $(this).val() === '@') {
                    const tag = $(this).val() === '@' ? '@' : tags[0].substr(1);
                    $('[data-user-tag] .hint-content').html('');
                    let prev = $(this).val();
                    $.whisper('OnlineUsers').then(function(response) {
                        if(prev !== $('.message-send textarea').val()) return;
                        $('[data-user-tag] .hint-content').html('');
                        _.forEach(response, function(name) {
                            if($('.message-send textarea').val().length > 1 && !name.includes(tag)) return;
                            const l = $(`<div class="hint-tag-name">@${name}</div>`);
                            $('[data-user-tag] .hint-content').append(l);
                            l.on('click', function() {
                                $('.message-send textarea').val($('.message-send textarea').val().replace(tag, (tag === '@' ? '@' : '')+name));
                                $('[data-user-tag]').fadeOut('fast');
                                sentNotify = true;
                            });
                        });
                    });
                }
            } else $('[data-user-tag]').fadeOut('fast');
        });

        $('.message-send textarea, .chat .messages, .chat .hint-content').overlayScrollbars({
            scrollbars: {
                autoHide: 'leave'
            },
            className: "os-theme-thin-light"
        });
    }
};

const makeItSnow = function() {
    $('.snow-back').empty();
    let increment = 0;

    while(increment < 200) {
        $('.snow-back').append('<div class="snow"></div>');
        increment++;
    }
};

const makeItRain = function() {
    $('.rain').empty();

    let increment = 0;
    let drops = "", backDrops = "";

    while (increment < 100) {
        const h = (Math.floor(Math.random() * 98 + 1));
        const fiver = (Math.floor(Math.random() * 5 + 2));
        const height = (Math.floor(Math.random() * 70 + 25));
        increment += fiver;
        drops += `<div class="drop" style="left: ${increment}%; height: ${height}px; bottom: ${fiver + fiver - 1 + 100}%; animation-delay: 0.${h}s; animation-duration: 0.5${h}s;"><div class="stem" style="animation-delay: 0.${h}s; animation-duration: 0.5${h}s;"></div></div>`;
        backDrops += `<div class="drop" style="right:${increment}%; height: ${height}px; bottom: ${fiver + fiver - 1 + 100}%; animation-delay: 0.${h}s; animation-duration: 0.5${h}s;"><div class="stem" style="animation-delay: 0.${h}s; animation-duration: 0.5${h}s;"></div></div>`;
    }

    $('.rain.front-row').append(drops);
    $('.rain.back-row').append(backDrops);
};
