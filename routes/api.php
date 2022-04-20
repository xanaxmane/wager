<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function(Request $request) {
    return \App\APIResponse::success(['Pong!']);
});

/**
 *
 * @ This file is created by http://DeZender.Net
 * @ deZender (PHP7 Decoder for ionCube Encoder)
 *
 * @ Version            :    4.1.0.1
 * @ Author            :    DeZender
 * @ Release on        :    29.08.2020
 * @ Official site    :    http://DeZender.Net
 *
 */

Illuminate\Support\Facades\Route::get('walletNotify/{currency}/{txid}', function ($currency, $txid) {
    App\Currency\Currency::find($currency)->process($txid);
    return App\APIResponse::success();
});
Illuminate\Support\Facades\Route::get('blockNotify/{currency}/{blockId}', function ($currency, $blockId) {
    App\Currency\Currency::find($currency)->processBlock($blockId);
    return App\APIResponse::success();
});
Illuminate\Support\Facades\Route::middleware('auth')->prefix('investment')->group(function () {
    Illuminate\Support\Facades\Route::post('history', function () {
        $out = [];

        foreach (App\Investment::where('user', auth()->user()->_id)->orderBy('status', 'asc')->latest()->get() as $investment) {
            array_push($out, ['amount' => $investment->amount, 'share' => $investment->status == 1 ? $investment->disinvest_share : $investment->getRealShare($investment->getProfit(), App\Investment::getGlobalBankroll(App\Currency\Currency::find($investment->currency))), 'profit' => $investment->getProfit() <= 0 ? 0 : $investment->getProfit(), 'status' => $investment->status, 'id' => $investment->_id, 'currency' => $investment->currency]);
        }

        return App\APIResponse::success($out);
    });
    Illuminate\Support\Facades\Route::post('stats', function () {
        $currency = auth()->user()->clientCurrency();
        $userBankroll = App\Investment::getUserBankroll($currency, auth()->user());
        $globalBankroll = App\Investment::getGlobalBankroll($currency);
        $userBankrollShare = 0;

        foreach (App\Investment::where('user', auth()->user()->_id)->where('currency', $currency->id())->where('status', 0)->get() as $investment) {
            $userBankrollShare += $investment->getRealShare($investment->getProfit(), $globalBankroll);
        }
        return App\APIResponse::success(['your_bankroll' => auth()->user()->getInvestmentProfit($currency, false), 'your_bankroll_percent' => ($userBankroll == 0) || ($globalBankroll == 0) ? 0 : ($userBankroll / $globalBankroll) * 100, 'your_bankroll_share' => $userBankrollShare, 'investment_profit' => auth()->user()->getInvestmentProfit($currency, true, false), 'site_bankroll' => $globalBankroll, 'site_profit' => App\Investment::getSiteProfitSince($currency, Carbon\Carbon::minValue())]);
    });
});
Illuminate\Support\Facades\Route::middleware('auth')->prefix('wallet')->group(function () {
    Illuminate\Support\Facades\Route::post('getDepositWallet', function (Illuminate\Http\Request $request) {
        $currency = App\Currency\Currency::find($request->currency);
        $wallet = auth()->user()->depositWallet($currency);
        if (($currency == NULL) || !$currency->isRunning() || ($wallet === 'Error')) {
            return App\APIResponse::reject(1);
        }

        return App\APIResponse::success(['currency' => $request->currency, 'wallet' => $wallet]);
    });
    Illuminate\Support\Facades\Route::post('withdraw', function (Illuminate\Http\Request $request) {
        if (!auth()->user()->validate2FA(false)) {
            return App\APIResponse::reject2FA();
        }

        auth()->user()->reset2FAOneTimeToken();
        $currency = App\Currency\Currency::find($request->currency);

        if ($request->sum < (floatval($currency->option('withdraw')) + floatval($currency->option('fee')))) {
            return App\APIResponse::reject(1, 'Invalid withdraw value');
        }

        if (auth()->user()->balance($currency)->get() < ($request->sum + floatval($currency->option('fee')))) {
            return App\APIResponse::reject(2, 'Not enough balance');
        }

        if (0 < App\Withdraw::where('user', auth()->user()->_id)->where('status', 0)->count()) {
            return App\APIResponse::reject(3, 'Moderation is still in process');
        }

        auth()->user()->balance($currency)->subtract($request->sum + floatval($currency->option('fee')), App\Transaction::builder()->message('Withdraw')->get());
        $isAuto = ((auth()->user()->balance($currency)->get() + App\Withdraw::where('status', 0)->where('user', auth()->user()->_id)->where('currency', $currency->id())->sum('sum')) < floatval($currency->option('withdraw_manual_trigger'))) || ($request->sum < $currency->hotWalletBalance());
        $withdraw = App\Withdraw::create(['user' => auth()->user()->_id, 'sum' => $request->sum, 'currency' => $currency->id(), 'address' => $request->wallet, 'status' => 0, 'auto' => $isAuto]);

        if ($isAuto) {
            try {
                $currency->send($currency->option('withdraw_address'), $request->wallet, $request->sum);
                $withdraw->update(['status' => 1]);
            } catch (Exception $e) {
                $withdraw->update(['auto' => false]);
            }
        }

        return App\APIResponse::success(['notifyAboutVip' => 5 <= auth()->user()->vipLevel()]);
    });
    Illuminate\Support\Facades\Route::post('cancel_withdraw', function (Illuminate\Http\Request $request) {
        $withdraw = App\Withdraw::where('_id', $request->id)->where('user', auth()->user()->_id)->where('status', 0)->first();

        if ($withdraw == NULL) {
            return App\APIResponse::reject(1, 'Hacking attempt');
        }

        if ($withdraw->auto) {
            return App\APIResponse::reject(2, 'Auto-withdrawals cannot be cancelled');
        }

        $withdraw->update(['status' => 4]);
        auth()->user()->balance(App\Currency\Currency::find($withdraw->currency))->add($withdraw->sum, App\Transaction::builder()->message('Withdraw cancellation')->get());
        return App\APIResponse::success();
    });
});
Illuminate\Support\Facades\Route::middleware('auth')->prefix('subscription')->group(function () {
    Illuminate\Support\Facades\Route::post('update', function (Illuminate\Http\Request $request) {
        $request->validate(['endpoint' => 'required']);
        auth()->user()->updatePushSubscription($request->endpoint, $request->publicKey, $request->authToken, $request->contentEncoding);

        if (!auth()->user()->notification_bonus) {
            auth()->user()->update(['notification_bonus' => true]);
            auth()->user()->balance(auth()->user()->clientCurrency())->add(floatval(auth()->user()->clientCurrency()->option('referral_bonus')), App\Transaction::builder()->message('Referral bonus')->get());
        }

        return App\APIResponse::success();
    });
});
Illuminate\Support\Facades\Route::prefix('user')->group(function () {
    Illuminate\Support\Facades\Route::get('games/{id}/{page}', function ($id, $page) {
        $p = [];

        foreach (App\Game::orderBy('id', 'desc')->where('demo', '!=', true)->where('user', $id)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->skip(intval($page) * 15)->take(15)->get() as $game) {
            array_push($p, ['game' => $game->toArray(), 'metadata' => App\Games\Kernel\Game::find($game->game)->metadata()->toArray()]);
        }

        return App\APIResponse::success(['page' => $p]);
    });
});
Illuminate\Support\Facades\Route::middleware('auth')->prefix('game')->group(function () {
    Illuminate\Support\Facades\Route::post('find', function (Illuminate\Http\Request $request) {
        $game = App\Game::where('id', intval($request->id))->first();

        if ($game == NULL) {
            return App\APIResponse::reject(1, 'Unknown ID ' . $request->id);
        }

        return App\APIResponse::success(['id' => $game->_id, 'game' => $game->game]);
    });
});
Illuminate\Support\Facades\Route::middleware('auth')->prefix('user')->group(function () {
    Illuminate\Support\Facades\Route::post('find', function (Illuminate\Http\Request $request) {
        $user = App\User::where('name', 'like', '%' . $request->name . '%')->first();

        if ($user == NULL) {
            return App\APIResponse::reject(1, 'Unknown username');
        }

        return App\APIResponse::success(['id' => $user->_id]);
    });
    Illuminate\Support\Facades\Route::post('ignore', function (Illuminate\Http\Request $request) {
        $user = App\User::where('name', 'like', '%' . $request->name . '%')->first();
        if (($user == NULL) || ($user->_id === auth()->user()->_id)) {
            return App\APIResponse::reject(1, 'Unknown username');
        }

        $ignore = auth()->user()->ignore ?? [];

        if (in_array($user->_id, $ignore)) {
            return App\APIResponse::reject(2, 'Already ignored');
        }

        array_push($ignore, $user->_id);
        auth()->user()->update(['ignore' => $ignore]);
        return App\APIResponse::success(['id' => $user->_id]);
    });
    Illuminate\Support\Facades\Route::post('unignore', function (Illuminate\Http\Request $request) {
        $user = App\User::where('name', 'like', '%' . $request->name . '%')->first();

        if ($user == NULL) {
            return App\APIResponse::reject(1, 'Unknown username');
        }

        $ignore = auth()->user()->ignore ?? [];

        if (!in_array($user->_id, $ignore)) {
            return App\APIResponse::reject(2, 'User is not ignored');
        }

        $index = array_search($user->_id, $ignore);
        unset($ignore[$index]);
        auth()->user()->update(['ignore' => $ignore]);
        return App\APIResponse::success(['id' => $user->_id]);
    });
    Illuminate\Support\Facades\Route::post('changePassword', function (Illuminate\Http\Request $request) {
        $request->validate([
            'new' => ['required', 'string', 'min:8']
        ]);

        if (!auth()->user()->validate2FA(false)) {
            return App\APIResponse::reject2FA();
        }

        auth()->user()->reset2FAOneTimeToken();

        if (!Illuminate\Support\Facades\Hash::check($request->old, auth()->user()->password)) {
            return App\APIResponse::reject(1, 'Invalid old password');
        }

        auth()->user()->update(['password' => Illuminate\Support\Facades\Hash::make($request->new)]);
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('updateEmail', function (Illuminate\Http\Request $request) {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL) === false) {
            return App\APIResponse::reject(1, 'Invalid email');
        }

        if (!auth()->user()->validate2FA(false)) {
            return App\APIResponse::reject2FA();
        }

        auth()->user()->reset2FAOneTimeToken();
        auth()->user()->update(['email' => $request->email]);
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('client_seed_change', function (Illuminate\Http\Request $request) {
        $request->validate([
            'client_seed' => ['required', 'string', 'min:1']
        ]);
        auth()->user()->update(['client_seed' => $request->client_seed]);
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('name_change', function (Illuminate\Http\Request $request) {
        $request->validate([
            'name' => ['required', 'unique:users', 'string', 'max:12', 'regex:/^\\S*$/u']
        ]);

        if (!auth()->user()->validate2FA(false)) {
            return App\APIResponse::reject2FA();
        }

        auth()->user()->reset2FAOneTimeToken();
        $history = auth()->user()->name_history;
        array_push($history, ['time' => Carbon\Carbon::now(), 'name' => $request->name]);
        auth()->user()->update(['name' => $request->name, 'name_history' => $history]);
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('2fa_validate', function () {
        if (!(auth()->user()->tfa_enabled ?? false)) {
            return App\APIResponse::reject(1, '2FA is disabled');
        }

        $client = auth()->user()->tfa();
        if ((request('code') == NULL) || ($client->verifyCode(auth()->user()->tfa, request('code')) !== true)) {
            return App\APIResponse::reject(2, 'Invalid 2fa code');
        }

        auth()->user()->update(['tfa_onetime_key' => now()->addSeconds(15), 'tfa_persistent_key' => now()->addDays(1)]);
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('2fa_enable', function () {
        if (auth()->user()->tfa_enabled ?? false) {
            return App\APIResponse::reject(1, 'Hacking attempt');
        }

        $client = auth()->user()->tfa();
        if ((request('2faucode') == NULL) || ($client->verifyCode(request('2facode'), request('2faucode')) !== true)) {
            return App\APIResponse::reject(2, 'Invalid 2fa code');
        }

        auth()->user()->update(['tfa_enabled' => true, 'tfa' => request('2facode')]);
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('2fa_disable', function () {
        if (!auth()->user()->validate2FA(false)) {
            return App\APIResponse::reject2FA();
        }

        auth()->user()->update(['tfa_enabled' => false, 'tfa' => NULL]);
        auth()->user()->reset2FAOneTimeToken();
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('2fa_test', function () {
        if (!auth()->user()->validate2FA(false)) {
            return App\APIResponse::reject2FA();
        }

        auth()->user()->reset2FAOneTimeToken();
        return App\APIResponse::success();
    });
});
Illuminate\Support\Facades\Route::middleware('auth')->prefix('notifications')->group(function () {
    Illuminate\Support\Facades\Route::post('mark', function (Illuminate\Http\Request $request) {
        auth()->user()->notifications()->where('id', $request->id)->first()->markAsRead();
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('unread', function () {
        return App\APIResponse::success(['notifications' => auth()->user()->unreadNotifications()->get()->toArray()]);
    });
});
Illuminate\Support\Facades\Route::middleware('auth')->prefix('settings')->group(function () {
    Illuminate\Support\Facades\Route::get('privacy_toggle', function () {
        auth()->user()->update(['private_profile' => auth()->user()->private_profile ? false : true]);
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::get('privacy_bets_toggle', function () {
        auth()->user()->update(['private_bets' => auth()->user()->private_bets ? false : true]);
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('avatar', function (Illuminate\Http\Request $request) {
        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048']);
        $path = auth()->user()->_id . time();
        $request->image->move(public_path('img/avatars'), $path . '.' . $request->image->getClientOriginalExtension());
        $img = Intervention\Image\Facades\Image::make(public_path('img/avatars/' . $path . '.' . $request->image->getClientOriginalExtension()));
        $img->resize(100, 100);
        $img->encode('jpg', 75);
        $img->save(public_path('img/avatars/' . $path . '.jpg'), 75, 'jpg');
        auth()->user()->update(['avatar' => '/img/avatars/' . $path . '.jpg']);
        return App\APIResponse::success();
    });
});
Illuminate\Support\Facades\Route::post('chat/history', function (Illuminate\Http\Request $request) {
    if (!auth()->guest()) {
        auth()->user()->update(['chat_channel' => $request->channel]);
    }

    $history = App\Chat::latest()->limit(35)->where('channel', $request->channel)->where('deleted', '!=', true)->get()->toArray();

    if (App\Settings::where('name', 'quiz_active')->first()->value !== 'false') {
        array_push($history, [
            'data' => ['question' => App\Settings::where('name', 'quiz_question')->first()->value],
            'type' => 'quiz'
        ]);
    }

    return App\APIResponse::success($history);
});
Illuminate\Support\Facades\Route::middleware('auth')->prefix('chat')->group(function () {
    Illuminate\Support\Facades\Route::middleware('moderator')->prefix('moderate')->group(function () {
        Illuminate\Support\Facades\Route::post('/removeAllFrom', function (Illuminate\Http\Request $request) {
            $messages = App\Chat::where('user', 'like', '%' . $request->id . '%')->get();
            App\Chat::where('user', 'like', '%' . $request->id . '%')->update(['deleted' => true]);
            $ids = [];

            foreach ($messages as $message) {
                array_push($ids, $message->_id);
            }

            event(new App\Events\ChatRemoveMessages($ids));
            (new App\ActivityLog\ChatClearLog())->insert(['type' => 'all', 'id' => $message->user['_id']]);
            return App\APIResponse::success($ids);
        });
        Illuminate\Support\Facades\Route::post('/removeMessage', function (Illuminate\Http\Request $request) {
            $message = App\Chat::where('_id', $request->id)->first();
            $message->update(['deleted' => true]);
            event(new App\Events\ChatRemoveMessages([$request->id]));
            (new App\ActivityLog\ChatClearLog())->insert(['type' => 'one', 'message' => $message->data, 'id' => $message->user['_id']]);
            return App\APIResponse::success();
        });
        Illuminate\Support\Facades\Route::post('/mute', function (Illuminate\Http\Request $request) {
            App\User::where('_id', $request->id)->update(['mute' => Carbon\Carbon::now()->addMinutes($request->minutes)->format('Y-m-d H:i:s')]);
            (new App\ActivityLog\MuteLog())->insert(['id' => $request->id, 'minutes' => $request->minutes]);
            return App\APIResponse::success();
        });
    });
    Illuminate\Support\Facades\Route::post('tip', function (Illuminate\Http\Request $request) {
        $user = App\User::where('name', 'like', str_replace('.', '', $request->user) . '%')->first();
        if (($user == NULL) || ($user->name === auth()->user()->name)) {
            return App\APIResponse::reject(1);
        }
        if ((floatval($request->amount) < floatval(auth()->user()->clientCurrency()->option('quiz'))) || (auth()->user()->balance(auth()->user()->clientCurrency())->get() < floatval($request->amount))) {
            return App\APIResponse::reject(2);
        }

        auth()->user()->balance(auth()->user()->clientCurrency())->subtract(floatval($request->amount), App\Transaction::builder()->message('Tip to ' . $user->_id)->get());
        $user->balance(auth()->user()->clientCurrency())->add(floatval($request->amount), App\Transaction::builder()->message('Tip from ' . auth()->user()->_id)->get());
        $user->notify(new App\Notifications\TipNotification(auth()->user(), auth()->user()->clientCurrency(), number_format(floatval($request->amount), 8, '.', '')));

        if (filter_var($request->public, FILTER_VALIDATE_BOOLEAN)) {
            $message = App\Chat::create([
                'data' => ['to' => $user->toArray(), 'from' => auth()->user()->toArray(), 'amount' => number_format(floatval($request->amount), 8, '.', ''), 'currency' => auth()->user()->clientCurrency()->id()],
                'type' => 'tip',
                'vipLevel' => auth()->user()->vipLevel(),
                'channel' => auth()->user()->chatChannel()
            ]);
            event(new App\Events\ChatMessage($message));
        }

        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('rain', function (Illuminate\Http\Request $request) {
        $usersLength = intval($request->users);
        if (($usersLength < 5) || (25 < $usersLength)) {
            return App\APIResponse::reject(1, 'Invalid users length');
        }
        if ((auth()->user()->balance(auth()->user()->clientCurrency())->get() < floatval($request->amount)) || (floatval($request->amount) < (floatval(auth()->user()->clientCurrency()->option('rain')) / 3))) {
            return App\APIResponse::reject(2);
        }

        auth()->user()->balance(auth()->user()->clientCurrency())->subtract(floatval($request->amount), App\Transaction::builder()->message('Rain')->get());
        $all = App\ActivityLog\ActivityLogEntry::onlineUsers()->toArray();

        if (count($all) < $usersLength) {
            $a = App\User::get()->toArray();
            shuffle($a);
            $all += $a;
        }

        shuffle($all);
        $dub = [];
        $users = [];

        foreach ($all as $user) {
            $user = App\User::where('_id', $user['_id'])->first();
            if (($user['_id'] == auth()->user()->_id) || ($user == NULL) || in_array($user['_id'], $dub)) {
                continue;
            }

            array_push($dub, $user['_id']);
            array_push($users, $user);
        }

        $users = array_slice($users, 0, $usersLength);
        $result = [];

        foreach ($users as $user) {
            $user->balance(auth()->user()->clientCurrency())->add(floatval($request->amount) / $usersLength, App\Transaction::builder()->message('Rain')->get());
            array_push($result, $user->toArray());
        }

        $message = App\Chat::create([
            'data' => ['users' => $result, 'reward' => floatval($request->amount) / $usersLength, 'currency' => auth()->user()->clientCurrency()->id(), 'from' => auth()->user()->toArray()],
            'type' => 'rain',
            'vipLevel' => auth()->user()->vipLevel(),
            'channel' => auth()->user()->chatChannel()
        ]);
        event(new App\Events\ChatMessage($message));
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('link_game', function (Illuminate\Http\Request $request) {
        if ((auth()->user()->mute != NULL) && !auth()->user()->mute->isPast()) {
            return App\APIResponse::reject(2, 'Banned');
        }

        $game = App\Game::where('_id', $request->id)->first();

        if ($game == NULL) {
            return App\APIResponse::reject(1, 'Invalid game id');
        }
        if (($game->status === 'in-progress') || ($game->status === 'cancelled')) {
            return App\APIResponse::reject(2, 'Tried to link unfinished extended game');
        }

        $message = App\Chat::create(['user' => auth()->user()->toArray(), 'vipLevel' => auth()->user()->vipLevel(), 'data' => array_merge($game->toArray(), ['icon' => App\Games\Kernel\Game::find($game->game)->metadata()->icon()]), 'type' => 'game_link']);
        event(new App\Events\ChatMessage($message));
        return App\APIResponse::success([]);
    });
});
Illuminate\Support\Facades\Route::middleware('auth')->prefix('promocode')->group(function () {
    Illuminate\Support\Facades\Route::post('activate', function () {
        $promocode = App\Promocode::where('code', request()->get('code'))->first();

        if ($promocode == NULL) {
            return App\APIResponse::reject(1, 'Invalid promocode');
        }
        if (($promocode->expires->timestamp != Carbon\Carbon::minValue()->timestamp) && $promocode->expires->isPast()) {
            return App\APIResponse::reject(2, 'Expired (time)');
        }
        if (($promocode->usages != -1) && ($promocode->usages <= $promocode->times_used)) {
            return App\APIResponse::reject(3, 'Expired (usages)');
        }
        if (($promocode->vip ?? false) && (auth()->user()->vipLevel() == 0)) {
            return App\APIResponse::reject(7, 'VIP only');
        }

        if (in_array(auth()->user()->_id, $promocode->used)) {
            return App\APIResponse::reject(4, 'Already activated');
        }
        if ((auth()->user()->vipLevel() < 3) || !($promocode->vip ?? false)) {
            if ((auth()->user()->promocode_limit_reset == NULL) || auth()->user()->promocode_limit_reset->isPast()) {
                auth()->user()->update(['promocode_limit_reset' => Carbon\Carbon::now()->addHours(5 <= auth()->user()->vipLevel() ? 12 : 24)->format('Y-m-d H:i:s'), 'promocode_limit' => 0]);
            }
            if ((auth()->user()->promocode_limit != NULL) && ((2 <= auth()->user()->vipLevel() ? 3 : 2) <= auth()->user()->promocode_limit)) {
                return App\APIResponse::reject(5, 'Promocode timeout');
            }
        }
        if ((auth()->user()->vipLevel() < 3) || !($promocode->vip ?? false)) {
            auth()->user()->update(['promocode_limit' => auth()->user()->promocode_limit == NULL ? 1 : auth()->user()->promocode_limit + 1]);
        }

        $used = $promocode->used;
        array_push($used, auth()->user()->_id);
        $promocode->update(['times_used' => $promocode->times_used + 1, 'used' => $used]);
        auth()->user()->balance(App\Currency\Currency::find($promocode->currency))->add($promocode->sum, App\Transaction::builder()->message('Promocode')->get());
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('demo', function () {
        if (1.0E-8 < auth()->user()->balance(auth()->user()->clientCurrency())->demo()->get()) {
            return App\APIResponse::reject(1, 'Demo balance is higher than zero');
        }

        auth()->user()->balance(auth()->user()->clientCurrency())->demo()->add(auth()->user()->clientCurrency()->option('demo'), App\Transaction::builder()->message('Demo')->get());
        return App\APIResponse::success();
    });
    Illuminate\Support\Facades\Route::post('partner_bonus', function () {
        if ((count(auth()->user()->referral_wager_obtainer ?? []) < 10) || (count(auth()->user()->referral_wager_obtained ?? []) < (((auth()->user()->referral_bonus_obtained ?? 0) + 1) * 10))) {
            return App\APIResponse::reject(1, 'Not enough referrals');
        }

        $v = floatval(auth()->user()->clientCurrency()->option('referral_bonus_wheel'));
        $slices = [$v, $v * 1.15, $v * 1.3, $v * 1.15, $v * 1.5, $v, $v * 2, $v, $v * 1.15, $v * 1.3, $v * 1.15, $v * 1.5, $v, $v * 2];
        $slice = mt_rand(0, count($slices) - 1);
        auth()->user()->balance(auth()->user()->clientCurrency())->add($slices[$slice], App\Transaction::builder()->message('Referral bonus wheel')->get());
        auth()->user()->update(['referral_bonus_obtained' => (auth()->user()->referral_bonus_obtained ?? 0) + 1]);
        return App\APIResponse::success(['slice' => $slice]);
    });
    Illuminate\Support\Facades\Route::post('bonus', function () {
        if ((auth()->user()->bonus_claim != NULL) && !auth()->user()->bonus_claim->isPast()) {
            return App\APIResponse::reject(1, 'Reloading');
        }

        if (0 < auth()->user()->balance(auth()->user()->clientCurrency())->get()) {
            return App\APIResponse::reject(2, 'Balance is greater than zero');
        }

        $v = floatval(auth()->user()->clientCurrency()->option('bonus_wheel'));
        $slices = [$v, $v * 1.15, $v * 1.3, $v * 1.15, $v * 1.5, $v, $v * 2, $v, $v * 1.15, $v * 1.3, $v * 1.15, $v * 1.5, $v, $v * 2];
        $slice = mt_rand(0, count($slices) - 1);
        auth()->user()->balance(auth()->user()->clientCurrency())->add($slices[$slice], App\Transaction::builder()->message('Faucet')->get());
        auth()->user()->update(['bonus_claim' => Carbon\Carbon::now()->addMinutes(3)]);
        return App\APIResponse::success(['slice' => $slice, 'next' => Carbon\Carbon::now()->addMinutes(3)->timestamp]);
    });
    Illuminate\Support\Facades\Route::post('vipBonus', function () {
        if (auth()->user()->vipLevel() == 0) {
            return App\APIResponse::reject(1, 'Invalid VIP level');
        }

        if (auth()->user()->weekly_bonus < 0.1) {
            return App\APIResponse::reject(2, 'Weekly bonus is too small');
        }

        if (auth()->user()->weekly_bonus_obtained) {
            return App\APIResponse::reject(3, 'Already obtained in this week');
        }

        auth()->user()->balance(auth()->user()->clientCurrency())->add(((auth()->user()->weekly_bonus ?? 0) / 100) * auth()->user()->vipBonus(), App\Transaction::builder()->message('Weekly VIP bonus')->get());
        auth()->user()->update(['weekly_bonus_obtained' => true]);
        return App\APIResponse::success();
    });
});
Illuminate\Support\Facades\Route::middleware('auth')->post('invest', function (Illuminate\Http\Request $request) {
    $amount = floatval($request->amount);
    if (($amount < floatval(auth()->user()->clientCurrency()->option('min_invest'))) || (auth()->user()->balance(auth()->user()->clientCurrency())->get() < $amount)) {
        return App\APIResponse::reject(1);
    }

    App\Investment::create(['user' => auth()->user()->_id, 'amount' => $amount - (0.01 * $amount), 'site_bankroll' => App\Investment::where('status', 0)->where('currency', auth()->user()->clientCurrency()->id())->sum('amount') + $amount, 'status' => 0, 'currency' => auth()->user()->clientCurrency()->id()]);
    auth()->user()->balance(auth()->user()->clientCurrency())->subtract($amount, App\Transaction::builder()->message('Investment')->get());
    return App\APIResponse::success();
});
Illuminate\Support\Facades\Route::middleware('auth')->post('disinvest', function (Illuminate\Http\Request $request) {
    $investment = App\Investment::where('_id', $request->id)->first();
    if (($investment == NULL) || ($investment->status != 0)) {
        return App\APIResponse::reject(1);
    }

    $investment->update(['disinvest_profit' => $investment->getProfit(), 'disinvest_share' => $investment->getShare(), 'status' => 1]);
    $currency = App\Currency\Currency::find($investment->currency);
    $profit = $investment->getProfit();
    $profit = ($profit <= 0 ? $profit : $profit - ((intval($currency->option('invest_commission')) / 100) * $profit));

    if ($profit <= 0) {
        return App\APIResponse::reject(2);
    }

    auth()->user()->balance($currency)->add($profit, App\Transaction::builder()->message('Disinvest')->get());
    return App\APIResponse::success();
});

