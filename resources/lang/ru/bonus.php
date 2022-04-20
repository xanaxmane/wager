<?php

return [
    'vip' => [
        'title' => 'VIP',
        'description' => 'Как участнику VIP программы, Вам полагается еженедельный VIP бонус. Узнайте о нем больше!',
        'unavailable_description' => 'Всем участникам VIP программы полагается еженедельный бонус.'
    ],
    'wheel' => [
        'description' => 'Крути колесо раз в несколько минут для получения бесплатного бонуса!',
        'title' => 'Колесо'
    ],
    'promo' => [
        'description' => 'Есть промокод? Введи его и получи бонус!',
        'activate' => 'Активировать',
        'title' => 'Промокод',
        'placeholder' => 'Введите промокод',
        'success' => 'Промокод успешно активирован!',
        'invalid' => 'Недействительный промокод',
        'expired_time' => 'Недействительный промокод: время на активацию вышло',
        'expired_usages' => 'Недействительный промокод: превышен лимит на количество активаций',
        'used' => 'Вы уже использовали этот промокод'
    ],
    'partner' => [
        'description' => 'Приглашайте друзей на win5x.com и получайте бонусы за каждых 10 активных рефералов!',
        'title' => 'Партнерская программа'
    ],
    'rain' => [
        'description' => 'Каждые несколько часов в чате происходит событие, раздающее бонус случайным людям.',
        'title' => 'Дождь',
        'modal_description' => 'Дождь - это чат-бот, который раздает монеты случайным людям.<br>Критерии для попадания под дождь:<ul class="mt-2"><li>Требуется пополнения счета за последние 24 часа</li><li>Бот отправляет определенное количество монет в случайное время (минимальное количество раздач - 5 в день, максимальное - 15)</li><li>Количество людей - от 5 до 15</li><li>Полученные монеты можно использования для игр на реальный баланс и использовать их для вывода средств</li></ul>'
    ],
    'discord' => [
        'title' => 'Discord',
        'common_desc' => 'Вступи в наш сервер Discord и получи бонус на счет!',
        'description' => 'Вступи в наш сервер Discord и получи бонус на счет!',
        'redirect' => 'Привязать аккаунт',
//        'link' => 'Для получения бонуса требуется:<ul><li>Привязать аккаунт Discord</li><li>Вступить в <a href="'.\App\Settings::where('name', 'discord_invite_link')->first()->value.'" target="_blank">наш Discord сервер</a></li></ul>После выполнения всех условий вернитесь на эту страницу и нажмите на кнопку "Проверить".',
//        'subscribe' => 'Для получения бонуса <a href="'.\App\Settings::where('name', 'discord_invite_link')->first()->value.'" target="_blank">присоединитесь к нашему Discord серверу</a>.<br>После вступления нажмите на кнопку "Проверить" и бонус моментально зачислится!',
        'check' => 'Проверить',
        'redirect_group' => 'Перейти в Discord',
        'success' => 'Бонус за вступление успешно зачислен!',
        'error' => [
            '1' => 'Вы не присоединились к нашему Discord серверу.',
            '2' => 'Вы уже получили этот бонус.'
        ]
    ],
    'notifications' => [
        'title' => 'Уведомления',
        'description' => 'Включи уведомления и узнавай о промокодах и обновлениях Win5X среди первых!'
    ]
];