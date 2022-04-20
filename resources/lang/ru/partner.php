<?php

return [
    'tabs' => [
        'overview' => 'Обзор',
        'list' => 'Рефералы',
        'analytics' => 'Аналитика'
    ],
    'overview' => [
        'guest_content' => "<p>Рекламируйте win5x.com и зарабатывайте деньги, участвуя в нашей партнерской программе!</p>
                      <p>Вы будете получать различные бонусы за каждого активного пользователя, который зарегистрируется и будет играть на Win5X посредством вашей ссылки.</p>
                      <button class='btn btn-primary' onclick='$.auth();'>Войти</button>",
        'content' => "<p>Рекламируйте win5x.com и зарабатывайте деньги, участвуя в нашей партнерской программе!</p>
                      <p>Вы будете получать различные бонусы за каждого активного пользователя, который зарегистрируется и будет играть на Win5X посредством вашей ссылки.</p>
                      <strong class='mt-2'>Реферальная ссылка</strong>
                      <input readonly class='mt-2' style='cursor: pointer !important;' id='link' data-toggle='tooltip' data-placement='top' title='Скопировать ссылку' value='https://win5x.com/?c=".(auth()->guest() ? '' : auth()->user()->_id)."'>
                      <p class='mt-4'><strong>Бонусы</strong><ul>
                      <li>Человек, который зарегистрируется по вашей ссылке получит бонус на свой счет.</li>
                      <li>Вы так же получите бонус, если реферал окажется активным. Отследить это можно через вкладку \"Рефералы\".</li>
                      <li>Если вы пригласили 10 активных рефералов - посетите страницу \"Бонус\", нажмите на \"Партнерская программа\" и получите увеличенную награду!</li>
                      <li>Вы будете получать 5% на свой счет с каждого депозита всех приглашенных вами рефералов.</li></ul></p>"
    ],
    'list' => [
        'name' => 'Имя',
        'activity' => 'Получен бонус за активность'
    ],
    'analytics' => [
        'referrals' => '<strong>Количество рефералов:</strong> :count',
        'referrals_bonus' => '<strong>Активные рефералы, за которых вы получили бонус:</strong> :count',
        'referrals_wheel' => '<strong>Получено бонусов за 10 активных рефералов:</strong> :count'
    ]
];
