<?php

return [
    'rank' => [
        'level' => 'VIP level - :level',
        '0' => '-',
        '1' => 'Bronze',
        '2' => 'Silver',
        '3' => 'Gold',
        '4' => 'Platinum',
        '5' => 'Diamond'
    ],
    'description' => 'Your VIP level is calculated by your wager in your favorite currency (:currency)',
    'benefits_description' => 'VIP benefits:',
    'benefits' => 'Benefits',
    'benefit_list' => [
        'bronze' => [
            '1' => 'Weekly Bonus',
            '2' => 'Access to VIP codes',
            '3' => 'VIP role on the Discord server'
        ],
        'silver' => [
            '1' => 'Code activation limit per day is increased from 2 to 3',
            '2' => 'Increased Weekly Bonus'
        ],
        'gold' => [
            '1' => 'VIP codes do not affect the overall activation limit of promotional codes',
            '2' => 'Increased Weekly Bonus'
        ],
        'platinum' => [
            '1' => 'The required amount for withdrawal is reduced by 2 times',
            '2' => 'Increased Weekly Bonus'
        ],
        'diamond' => [
            '1' => 'Your withdrawals have a higher priority in the queue',
            '2' => 'Code activation limit now resets every 12 hours',
            '3' => 'Increased Weekly Bonus'
        ]
    ],
    'bonus' => [
        'tooltip' => 'Weekly VIP Bonus',
        'title' => 'Weekly Bonus',
        'progress_title' => 'Progress',
        'description' => "As a reward for being VIP on win5x.com, you get a weekly bonus, the maximum size of which is determined by your VIP status - :vip.<br>
                          <br>Each your bet adds 0.1% to progress bar.
                          <br>You can withdraw your weekly bonus at any time, but keep in mind that after this you will not be able to receive this bonus for the rest of the week.
                          <br><br>We reset weekly progress every Sunday. Remember to take the reward before this day!",
        'timeout' => "<br><strong>You have already received the bonus.</strong><br>Come back on Sunday!<br><br>"
    ]
];
