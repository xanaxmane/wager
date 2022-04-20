<?php

return [
    'tabs' => [
        'overview' => 'Overview',
        'list' => 'Referrals',
        'analytics' => 'Analytics'
    ],
    'overview' => [
        'guest_content' => "<p>Advertise wager.co.nz and earn money by participating in our affiliate program!</p>
                      <p>You will receive various bonuses for each active user who will register and play on Wanger through your link.</p>
                      <button class='btn btn-primary' onclick='$.auth();'>Login</button>",
        'content' => "<p>Advertise wager.co.nz and earn money by participating in our affiliate program!</p>
                      <p>You will receive various bonuses for each active user who will register and play on Wager through your link.</p>
                      <strong class='mt-2'>Referral link</strong>
                      <input readonly class='mt-2' style='cursor: pointer !important;' id='link' data-toggle='tooltip' data-placement='top' title='Copy link' value='https://wager.co.nz/?c=:id'>
                      <p class='mt-4'><strong>Rewards</strong><ul>
                      <li>Your referral will receive a bonus after registration.</li>
                      <li>You will also receive a bonus if the referral is active. You can track this through the \"Referrals\" tab.</li>
                      <li>If you have invited 10 active referrals - visit the \"Bonus\" page, click on the \"Affiliate Program\" and get an increased reward!</li>
                      <li>You will get 5% from your referral deposit</li></ul></p>"
    ],
    'list' => [
        'name' => 'Name',
        'activity' => 'Activity bonus received'
    ],
    'analytics' => [
        'referrals' => '<strong>Number of referrals:</strong> :count',
        'referrals_bonus' => '<strong>Active referrals:</strong> :count',
        'referrals_wheel' => '<strong>Received bonuses for 10 active referrals:</strong> :count'
    ]
];
