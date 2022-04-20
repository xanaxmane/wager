<?php

use App\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use MongoDB\BSON\Decimal128;

Route::prefix('wallet')->group(function() {
    Route::prefix('bank')->group(function() {
        Route::post('accept', function() {
            $invoice = \App\Invoice::where('_id', request('id'))->first();
            if($invoice == null || $invoice->status != 0) return \App\APIResponse::reject(1);
            $invoice->update([
                'status' => 3,
                'sum' => new Decimal128(floatval(request('sum')))
            ]);

            \App\User::where('_id', $invoice->user)->first()->balance(\App\Currency\Currency::find($invoice->currency))->add(request('sum'));

            return \App\APIResponse::success();
        });
        Route::post('decline', function() {
            $invoice = \App\Invoice::where('_id', request('id'))->first();
            if($invoice == null || $invoice->status != 0) return \App\APIResponse::reject(1);

            $invoice->update(['status' => 2]);
            return \App\APIResponse::success();
        });
    });
    Route::post('accept', function() {
        $withdraw = \App\Withdraw::where('_id', request('id'))->first();
        if($withdraw == null || $withdraw->status != 0) return \App\APIResponse::reject(1, 'Invalid state');

        \App\User::where('_id', $withdraw->user)->first()->notify(new \App\Notifications\WithdrawAccepted($withdraw));
        $withdraw->update([
            'status' => 1
        ]);
        return \App\APIResponse::success();
    });
    Route::post('decline', function() {
        $withdraw = \App\Withdraw::where('_id', request('id'))->first();
        if($withdraw == null || $withdraw->status != 0) return \App\APIResponse::reject(1, 'Invalid state');

        $withdraw->update([
            'decline_reason' => request('reason'),
            'status' => 2
        ]);
        \App\User::where('_id', $withdraw->user)->first()->notify(new \App\Notifications\WithdrawDeclined($withdraw));
        \App\User::where('_id', $withdraw->user)->first()->balance(\App\Currency\Currency::find($withdraw->currency))->add($withdraw->sum);
        return \App\APIResponse::success();
    });
    Route::post('ignore', function() {
        $withdraw = \App\Withdraw::where('_id', request('id'))->first();
        if($withdraw == null || $withdraw->status != 0) return \App\APIResponse::reject(1, 'Invalid state');
        $withdraw->update([
            'status' => 3
        ]);
        return \App\APIResponse::success();
    });
    Route::post('unignore', function() {
        $withdraw = \App\Withdraw::where('_id', request('id'))->first();
        if($withdraw == null || $withdraw->status != 3) return \App\APIResponse::reject(1, 'Invalid state');
        $withdraw->update([
            'status' => 0
        ]);
        return \App\APIResponse::success();
    });
    Route::get('autoSetup', function() {
        foreach (\App\Currency\Currency::all() as $currency) $currency->setupWallet();
        return \App\APIResponse::success();
    });
    Route::post('/transfer', function() {
        try {
            $currency = \App\Currency\Currency::find(request('currency'));
            $currency->send($currency->option('transfer_address'), request('address'), floatval(request('amount')));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::critical($e);
            return \App\APIResponse::reject(1);
        }
        return \App\APIResponse::success();
    });
});

Route::prefix('notifications')->group(function() {
    Route::post('/browser', function() {
        \Illuminate\Support\Facades\Notification::send(\App\User::where('notification_bonus', true)->get(),
            new \App\Notifications\BrowserOnlyNotification(request('title'), request('message')));
        return \App\APIResponse::success();
    });
    Route::post('/standalone', function() {
        \Illuminate\Support\Facades\Notification::send(\App\User::get(),
            new \App\Notifications\CustomNotification(request('title'), request('message')));
        return \App\APIResponse::success();
    });
    Route::post('/global', function() {
        \App\GlobalNotification::create([
            'icon' => request('icon'),
            'text' => request('text')
        ]);
        (new \App\ActivityLog\GlobalNotificationLog())->insert(['state' => true, 'text' => request('text'), 'icon' => request('icon')]);
        return \App\APIResponse::success();
    });
    Route::post('/global_remove', function() {
        $n = \App\GlobalNotification::where('_id', request('id'));
        (new \App\ActivityLog\GlobalNotificationLog())->insert(['state' => false, 'text' => $n->first()->text, 'icon' => $n->first()->icon]);
        $n->delete();
        return \App\APIResponse::success();
    });
});

Route::get('/sendMail/{mailable}', function($mailable) {
    $cl = 'App\\Mail\\'.$mailable;
    $users = \App\User::where('email', '!=', null)->get();
    foreach($users as $user)
        \Illuminate\Support\Facades\Mail::to($user->email)->send((new $cl));

    return \App\APIResponse::success([
        'mailable' => $cl,
        'users' => count($users)
    ]);
});

Route::post('/ban', function() {
    $user = \App\User::where('_id', request('id'))->first();
    (new \App\ActivityLog\BanUnbanLog())->insert(['type' => $user->ban ? 'unban' : 'ban', 'id' => $user->_id]);
    $user->update([
        'ban' => $user->ban ? false : true
    ]);
    return \App\APIResponse::success();
});

Route::post('/toggle_module', function() {
    $game = \App\Games\Kernel\Game::find(request('api_id'));
    $module = \App\Games\Kernel\Module\Module::find(request('module_id'));
    \App\Modules::get($game, filter_var(request('demo'), FILTER_VALIDATE_BOOLEAN))->toggleModule($module)->save();
    return \App\APIResponse::success();
});

Route::post('/option_value', function() {
    $game = \App\Games\Kernel\Game::find(request('api_id'));
    $module = \App\Games\Kernel\Module\Module::find(request('module_id'));
    \App\Modules::get($game, filter_var(request('demo'), FILTER_VALIDATE_BOOLEAN))->set($module, request('option_id'), request('value') ?? '')->save();
    return \App\APIResponse::success();
});

Route::post('/toggle', function() {
    if(\App\DisabledGame::where('name', request('name'))->first() == null) {
        \App\DisabledGame::create(['name' => request('name')]);
        (new \App\ActivityLog\DisableGameActivity())->insert(['state' => true, 'api_id' => request('name')]);
    } else {
        \App\DisabledGame::where('name', request('name'))->delete();
        (new \App\ActivityLog\DisableGameActivity())->insert(['state' => false, 'api_id' => request('name')]);
    }
    return \App\APIResponse::success();
});

Route::post('/role', function() {
    \App\User::where('_id', request('id'))->update([
        'access' => request('role')
    ]);
    return \App\APIResponse::success();
});

Route::post('/balance', function() {
    \App\User::where('_id', request('id'))->update([
        request('currency') => new Decimal128(strval(request('balance')))
    ]);
    (new \App\ActivityLog\BalanceChangeActivity())->insert(['currency' => request('currency'), 'balance' => request('balance'), 'id' => request('id')]);
    return \App\APIResponse::success();
});

Route::post('/currencyOption', function() {
    \App\Currency\Currency::find(request('currency'))->option(request('option'), request('value'));
    return \App\APIResponse::success();
});

Route::prefix('settings')->group(function() {
    Route::post('create', function() {
        \App\Settings::create(['name' => request('key'), 'description' => request('description'), 'value' => null]);
        return \App\APIResponse::success();
    });
    Route::post('edit', function() {
        \App\Settings::where('name', request('key'))->first()->update([
            'value' => request('value') === 'null' ? null : request('value')
        ]);
        return \App\APIResponse::success();
    });
    Route::post('remove', function() {
        \App\Settings::where('name', request('key'))->delete();
        return \App\APIResponse::success();
    });
});

Route::prefix('promocode')->group(function() {
    Route::post('remove', function() {
        \App\Promocode::where('_id', request()->get('id'))->delete();
        return \App\APIResponse::success();
    });
    Route::post('remove_inactive', function() {
        foreach(\App\Promocode::get() as $promocode) {
            if(($promocode->expires->timestamp != \Carbon\Carbon::minValue()->timestamp && $promocode->expires->isPast())
                    || ($promocode->usages != -1 && $promocode->times_used >= $promocode->usages)) $promocode->delete();
        }
        return \App\APIResponse::success();
    });
    Route::post('create', function() {
        request()->validate([
            'code' => 'required',
            'usages' => 'required',
            'expires' => 'required',
            'sum' => 'required',
            'currency' => 'required'
        ]);

        \App\Promocode::create([
            'code' => request('code') === '%random%' ? \App\Promocode::generate() : request('code'),
            'currency' => request('currency'),
            'used' => [],
            'sum' => floatval(request('sum')),
            'usages' => request('usages') === '%infinite%' ? -1 : intval(request('usages')),
            'times_used' => 0,
            'expires' => request('expires') === '%unlimited%' ? \Carbon\Carbon::minValue() : \Carbon\Carbon::createFromFormat('d-m-Y H:i', request()->get('expires'))
        ]);
        return \App\APIResponse::success();
    });
});

Route::get('/{page?}/{data?}', function($page = 'index', $data = null) {
    if(!view()->exists('admin.'.$page)) return response()->view('errors.404', [], 404);
    $page = str_replace('/', '.', $page);

    $view = view('admin.'.$page)->with('data', $data);
    if(!request()->pjax() && !request()->ajax()) $view = view('layouts.admin')->with('page', $view);
    return $view;
});
