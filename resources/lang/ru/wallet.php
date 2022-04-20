<?php

return [
    'tabs' => [
        'deposit' => 'Пополнение',
        'withdraw' => 'Вывод',
        'history' => 'История',
        'deposits' => 'Пополнения',
        'withdraws' => 'Выводы'
    ],
    'set_email' => 'Установите email в своем профиле для использования этого платежного способа.',
    'bank' => [
        'title' => 'Перевод на банковский счет',
        'description' => 'Запрос на проверку перевода был отправлен. Вы можете следить за статусом во вкладке "История".',
        'transfer_here' => 'Отправьте перевод на данный банковский счет:',
        'bank_check' => 'Запросить проверку',
        'transaction_id' => 'Идентификатор транзакции',
        'notify' => 'После перевода нажмите на кнопку "Запросить проверку".<br>Наши операторы подтвердят перевод и зачислят сумму на ваш аккаунт вручную.<br>Этот метод пополнения счета <strong>не моментальный</strong>.',
        'invalid_txid' => 'Неверный идентификатор транзакции',
        'already_have_one_in_moderation' => 'Вы уже запрашивали проверку банковского перевода.',
        'cancelled' => 'Запрос на проверку был отменен'
    ],
    's' => [
        'amount' => 'Сумма пополнения',
        'deposit' => 'Пополнить счет',
        'invalid_amount' => 'Неверная сумма',
        'conversion_rate' => 'Курс обмена: :usd USD = :token <i class=":icon" style="color: :style"></i>'
    ],
    'pending' => 'Ожидание',
    'method' => 'Метод оплаты:',
    'troubles' => 'Нужна помощь?<br>Свяжитесь с <a href="/help">нашей службой поддержки</a>',
    'fast' => 'Вы будете уведомлены сразу после внесения депозита и сможете увидеть количество подтверждений ваших транзакций',
    'deposit' => [
        'address' => 'Ваш адрес для депозита :currency',
        'confirmations' => 'Отправляйте :currency только на этот адрес, количество подтверждений: :confirmations.'
    ],
    'withdraw' => [
        'title' => 'Выплата',
        'address' => '<i class=":icon"></i> :currency адрес',
        'amount' => 'Сумма (Минимальная: :min <i class=":icon" style="color: :style"></i>)',
        'button' => 'Вывести',
        'fee' => 'С вашего баланса дополнительно спишется :fee <i class=":icon" style="color: :style"></i> для покрытия комиссии.',
        'method' => 'Вывести на:',
        'enter_wallet' => 'Введите кошелек',
        'wallet' => 'Кошелек',
        'go' => 'Вывести',
        'content' => 'Выплата была успешно заказана. Вы можете проверить ее статус во вкладке "История".',
        'vip_content' => '<div class="mt-2">Так как ваш VIP уровень - <svg style="width: 14px; height: 14px;"><use href="#vip-diamond"></use></svg>, ваша заявка была помещена в самое начало очереди.</div>'
    ],
    'history' => [
        'empty' => 'Вы еще ничего не заказывали.',
        'name' => 'Валюта',
        'sum' => 'Сумма',
        'date' => 'Дата',
        'confirmations' => 'Подтверждения',
        'status' => 'Статус',
        'not_paid' => 'Не оплачено',
        'paid' => 'Оплачено',
        'wallet' => 'Кошелек: :wallet',
        'cancel' => 'Отменить',
        'withdraw_cancelled' => 'Выплата была отменена.',
        'withdraw_status' => [
            'moderation' => 'Модерация',
            'accepted' => 'Выплачено',
            'declined' => 'Отклонено модератором',
            'reason' => 'Причина:',
            'cancelled' => 'Отменено пользователем'
        ]
    ],
    'copy' => 'Скопировать',
    'copied' => 'Скопировано!'
];