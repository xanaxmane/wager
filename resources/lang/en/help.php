<?php

return [
    'contact_us' => 'Contact us',
    'vk' => [
        'title' => 'VKontakte - Support Service',
        'time' => 'Average response time: 30 minutes'
    ],
    'email' => [
        'title' => 'Email - Support Service',
        'time' => 'Average response time: 240 minutes'
    ],
    'block' => [
        'questions' => [
            'title' => 'FAQ',
            'about' => [
                'title' => 'About us',
                'description' => 'win5x.com - online bitcoin gambling. We have '.sizeof(\App\Games\Kernel\Game::list()).' games with exciting mechanics and high winning odds.'
            ],
            'how_to_play' => [
                'title' => 'How to play?',
                'description' => 'Go to the page of any game, specify the amount of the bet and click "Play".'
            ],
            'bonus' => [
                'title' => 'Bonuses',
                'description' => '<a href="/bonus">Check out this page</a>!'
            ],
            'fairness' => [
                'title' => 'Fairness',
                'description' => 'The random number generator creates provable and absolutely honest random numbers, which are used to determine the result of each game played on the site. Each user can check the outcome of any game in a completely deterministic way. By providing one parameter, Client Seed, to the inputs of a random number generator, Win5x cannot manipulate the results in its favor. The Win5X random number generator allows each game to request any number of random numbers from a given initial client number, initial server number and one-time number. You can learn more about how it works on the fair play page.'
            ],
            'partner' => [
                'title' => 'Affiliate program',
                'description' => 'Invite other players to our site through your referral link and earn money. Details can be found in your profile.'
            ],
            'chat_rules' => [
                'title' => 'Chat Rules',
                'description' => 'Prohibited:<ul><li>Spam</li><li>Begging</li><li>Flooding with messages like "rain" and "quiz"</li><li>External links/wallets/pages</li><li>Insults of any kind</li><li>Selling "Tactics" and services to raise the balance to other players, etc.</li></ul>'
            ],
            'p' => [
                'title' => 'Partnership',
                'description' => 'Contact us: <a href="mailto:support@win5x.com">support@win5x.com</a>'
            ]
        ],
        'payments' => [
            'title' => 'Deposit',
            'how_to' => [
                'title' => 'How to deposit?',
                'description' => 'To deposit, click on the button with the balance in the upper right corner of the site. Choose a payment method convenient for you and enter the amount of coins.'
            ],
            'com' => [
                'title' => 'Commissions',
                'description' => 'Commissions of all payment methods are indicated on deposit page'
            ],
            'time' => [
                'title' => 'Note',
                'description' => 'All transactions are processed instantly. Delays can occur only in the event of problems with the payment method. In case of problems, write to our support team and provide the most detailed information about your problem.'
            ]
        ],
        'withdraws' => [
            'title' => 'Withdraw funds',
            'how_to' => [
                'title' => 'How to withdraw money?',
                'description' => 'To withdraw money from your account, click on the Wallet button in the upper right corner of the site. Choose the withdrawal method convenient for you, enter the amount and click "Go to payment". To withdraw money, you must wager 100% of the amount of your deposit (if you played without a deposit - this is not required).'
            ],
            'com' => [
                'title' => 'Commissions',
                'description' => 'Commissions for the withdrawal of funds we undertake.'
            ],
            'time' => [
                'title' => 'Withdrawal Timing',
                'description' => 'All requests are processed on average for 1 day, the maximum period is 3 days. In rare cases, the withdrawal may be delayed, the reason for this is the loading of banks, etc.<br>Check that you have correctly entered the details for the output on the transaction page in your profile. Cancel the payment if they are incorrect. If the payment has already been made to the wrong number, the money will not be returned.'
            ]
        ],
        'other' => [
            'title' => 'Other',
            'rain' => [
                'title' => 'Rain/Snow',
                'description' => 'Rain or Snow is a chat bot that randomly distributes the balance to random players who are online on the site and made deposit in the last 24 hours.'
            ],
            'demo' => [
                'title' => 'Demo account',
                'description' => 'You can activate a demo account by clicking on the balance icon in the upper right corner of the site. It exists for training purposes only - you cannot withdraw these funds, and the history of the games will not be saved.'
            ],
            'quiz' => [
                'title' => 'Quiz',
                'description' => 'The quiz can be held automatically in the form of mathematical examples, or by site moderators. The first player to answer the question correctly receives a reward.'
            ],
            'tactics' => [
                'title' => 'Tactics',
                'description' => 'There are no tactics that are guaranteed to work. It all depends on your style of play: fast with great risk or slowly but surely.'
            ],
            'lost' => [
                'title' => 'I lost',
                'description' => 'There is no game on our site with a 100% chance of winning. Even 99.9% is a 1 in 1000 chance that you can lose. For example, in the game Stairs, when you play on 1 stone - this is a chance of 1 out of 20 (5%) that you lose. We never interfere in the gameplay, so it all depends on your luck. We can’t substitute the result of your game, because there’s a line on your part that says “Client seed”. Initially, the “Client seed” is generated automatically, but you can always change it and put any characters there. To check the results of your games, click on any of your games in history and in the window that appears below you will see 2 fields “Client seed” and “Server seed”. To check the result of the game, you need to click on “change server seed” and click the “Check” button - and you will see that the result of the game has remained unchanged. We advise you to play more comfortable. Develop your own strategies and play them. Do not flirt, be able to stop in time and withdraw money. Gambling is designed to entertain. Remember that you draw with money when you make bets. Do not spend more than you can afford to lose.'
            ],
            'reviews' => [
                'title' => 'Reviews',
                'description' => 'Reviews are available in our social network groups.'
            ],
            'job' => [
                'title' => 'Jobs',
                'description' => 'The list of vacancies is available at <a href="/job">this page</a>. Keep in mind that it can be empty for a long time - we do not always need new people in the team.'
            ]
        ]
    ]
];
