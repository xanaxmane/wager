<?php

return [
    'tabs' => [
        'deposit' => 'Deposit',
        'withdraw' => 'Withdraw',
        'history' => 'History',
        'deposits' => 'Deposits',
        'withdraws' => 'Withdraws'
    ],
    'set_email' => 'Set email in your profile to use this payment method.',
    'bank' => [
        'title' => 'Bank Deposit',
        'description' => 'Verification request sent successfully. You can view it\'s status in "History" -> "Deposits" tab.',
        'transfer_here' => 'Transfer funds to this bank number:',
        'bank_check' => 'Request verification',
        'transaction_id' => 'Transaction id',
        'notify' => 'After transfer is completed, click on the "Request verification" button.<br>Our operators will check your transaction and add funds to your account manually.<br>This payment method <strong>isn\'t</strong> instant.',
        'invalid_txid' => 'Invalid transaction id',
        'already_have_one_in_moderation' => 'You already have one verification request pending.',
        'cancelled' => 'Bank deposit verification request was cancelled'
    ],
    's' => [
        'amount' => 'Deposit amount',
        'deposit' => 'Deposit',
        'invalid_amount' => 'Invalid amount',
        'conversion_rate' => 'Conversion rate: :usd USD = :token <i class=":icon" style="color: :style"></i>'
    ],
    'pending' => 'Pending',
    'method' => 'Payment method:',
    'troubles' => 'Need help?<br>Contact our <a href="/help">support team</a>',
    'fast' => 'You will be notified instantly once you make deposit and will be able to view your transaction confirmation count',
    'deposit' => [
        'address' => 'Your :currency deposit address',
        'confirmations' => 'Only send :currency to this address, :confirmations confirmation(s) required.'
    ],
    'withdraw' => [
        'title' => 'Withdraw',
        'address' => '<i class=":icon"></i> :currency Address',
        'amount' => 'Amount (Min :min <i class=":icon" style="color: :style"></i>)',
        'button' => 'Withdraw',
        'fee' => 'Your withdrawal will have :fee <i class=":icon" style="color: :style"></i> subtracted from your remaining balance to cover the transaction fee.',
        'method' => 'Withdraw method:',
        'enter_wallet' => 'Wallet:',
        'wallet' => 'Wallet',
        'go' => 'Withdraw',
        'content' => 'Withdraw was successfully ordered. You can check its status in the "History" section.',
        'vip_content' => '<div class="mt-2">Since your VIP level is <svg style="width: 14px; height: 14px;"><use href="#vip-diamond"></use></svg>, your request was placed at the very beginning of the queue.</div>'
    ],
    'history' => [
        'empty' => 'You haven’t ordered anything yet.',
        'name' => 'Currency',
        'sum' => 'Amount',
        'date' => 'Date',
        'status' => 'Status',
        'confirmations' => 'confirmations',
        'id' => 'ID: :id',
        'paid' => 'Successful',
        'wallet' => 'Address: :wallet',
        'cancel' => 'Cancel',
        'withdraw_cancelled' => 'Payment has been cancelled.',
        'withdraw_status' => [
            'moderation' => 'Moderation',
            'accepted' => 'Successful',
            'declined' => 'Declined',
            'reason' => 'Reason:',
            'cancelled' => 'Cancelled by user'
        ]
    ],
    'copy' => 'Copy',
    'copied' => 'Copied!'
];
