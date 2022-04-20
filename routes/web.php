<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function(Request $request) {
    return \App\APIResponse::success(['Pong!']);
});
Illuminate\Support\Facades\Route::get('/avatar/{hash}', function($hash) {
    $size = 100;
    $icon = new Jdenticon\Identicon();
    $icon->setValue($hash);
    $icon->setSize($size);
    $style = new Jdenticon\IdenticonStyle();
    $style->setBackgroundColor('#21232a');
    $icon->setStyle($style);
    $icon->displayImage('png');
    return response('')->header('Content-Type', 'image/png');
});
Illuminate\Support\Facades\Route::get('/lang/{locale}', function($locale) {
    $languages = ['en', 'ru'];

    if (!auth()->guest()) {
        auth()->user()->update(['chat_channel' => NULL]);
    }

    if (in_array($locale, $languages)) {
        Illuminate\Support\Facades\Cookie::queue(Illuminate\Support\Facades\Cookie::make('lang', $locale, '20160'));
        return back();
    }
    else {
        Illuminate\Support\Facades\App::setLocale(Illuminate\Support\Facades\App::getLocale());
        return back();
    }
});
Illuminate\Support\Facades\Route::get('/{page?}/{data?}', function($page = 'index', $data = NULL) {


    if (!view()->exists($page)) {
        return response()->view('errors.404', [], 404);
    }

    if (!auth()->guest()) {
        if (!(auth()->user()->email_notified ?? false)) {
            auth()->user()->notify(new App\Notifications\EmailNotification());
            auth()->user()->update(['email_notified' => true]);
        }
    }

    $page = str_replace('/', '.', $page);
    $view = view($page)->with('data', $data);
    if (!request()->pjax() && !request()->ajax() && !(($page == 'no_js') || ($page == 'invalid_browser'))) {
        $view = view('layouts.app')->with('page', $view);
    }

    return $view;
});
