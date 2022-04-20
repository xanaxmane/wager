@php
    $user = \App\User::where('_id', $data)->first();
    if(is_null($user)) {
        header('Location: /404');
        die();
    }

    $isOwner = !auth()->guest() && $user->_id == auth()->user()->id;
@endphp

<div class="container-fluid" data-user-profile-id="{{ $user->_id }}">
    <div class="profile-container h-100">
        <div class="row profile_row">
            <div class="profile_column col">
                <div class="profile-sidebar">
                    <div class="avatar">
                        <img alt src="{{ $user->avatar }}">
                    </div>
                    @if(!auth()->guest() && auth()->user()->access !== 'user')
                        @php
                            $name_change_history = '';
                            foreach($user->name_history as $history) {
                                $name_change_history .= '<div>'.\Carbon\Carbon::parse($history['time'])->diffForHumans().' - '.$history['name'].'</div>';
                            }
                        @endphp
                    @endif
                    <div class="name" @if(!auth()->guest() && auth()->user()->access !== 'user') data-toggle="tooltip" data-placement="bottom" data-html="true" title="{!! $name_change_history !!}" style="cursor: help" @endif>
                        {{ $user->name }}
                    </div>
                    <ul class="profile-menu">
                        <li class="active" data-toggle-tab="profile">{{ __('general.head.profile') }}</li>
                        @if($isOwner)
                            <li onclick="$.vip()">
                                {{ __('general.profile.vip') }}
                                <span><i class="fad fa-gem"></i></span>
                            </li>
                            <li onclick="redirect('/partner')">
                                {{ __('general.profile.partner') }}
                            </li>
                            <li data-toggle-tab="security">{{ __('general.profile.security') }}</li>
                            <li data-toggle-tab="settings">{{ __('general.profile.settings') }}</li>
                            <li onclick="$.request('/auth/logout', [], 'get').then(function() { window.location.reload(); });">{{ __('general.head.logout') }}</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="content_column col">
                <div class="profile-content">
                    @if((!$isOwner && $user->private_profile == true) && (auth()->guest() || auth()->user()->access != 'admin'))
                        <div class="incognito">
                            <img src="/img/misc/incognito-dark.png" data-incognito-dark>
                            <img src="/img/misc/incognito-default.png" data-incognito-white>
                            <div class="incognito-desc">
                                {{ __('general.profile.incognito') }}
                            </div>
                        </div>
                    @else
                        <div data-tab="profile">
                            @if(\Illuminate\Support\Facades\DB::table('games')->where('user', $user->_id)->where('status', 'win')->where('demo', '!=', true)->first() == null)
                                <div class="incognito">
                                    <i class="fas fa-history"></i>
                                    <div class="incognito-desc">
                                        {{ __('general.profile.empty') }}
                                    </div>
                                </div>
                            @else
                                <div class="cat">
                                    {{ __('general.profile.stats') }}
                                </div>
                                <table class="live-table">
                                    <thead>
                                    <tr>
                                        <th>
                                            {{ __('general.profile.bets') }}
                                        </th>
                                        <th>
                                            {{ __('general.profile.wins') }}
                                        </th>
                                        <th>
                                            {{ __('general.profile.losses') }}
                                        </th>
                                        <th style="text-align: right">
                                            {{ __('general.profile.wagered') }}
                                        </th>
                                        <th>
                                            {{ __('general.profile.profit') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="live_games">
                                    @foreach(\App\Currency\Currency::all() as $currency)
                                        <tr>
                                            <th>
                                                <div>
                                                    <div class="icon d-none d-md-inline-block">
                                                        <i class="{{ $currency->icon() }}" style="color: {{ $currency->style() }}"></i>
                                                    </div>
                                                    <div class="name">
                                                        <div data-highlight>{{ $currency->name() }}</div>
                                                        {{ __('general.profile.games_c') }} {{ \Illuminate\Support\Facades\DB::table('games')->where('demo', '!=', true)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->where('user', $user->_id)->where('currency', $currency->id())->count() }}
                                                    </div>
                                                </div>
                                            </th>
                                            <th data-highlight>
                                                <div>
                                                    {{ \Illuminate\Support\Facades\DB::table('games')->where('demo', '!=', true)->where('status', 'win')->where('user', $user->_id)->where('currency', $currency->id())->count() }}
                                                </div>
                                            </th>
                                            <th data-highlight>
                                                <div>
                                                    {{ \Illuminate\Support\Facades\DB::table('games')->where('demo', '!=', true)->where('status', 'lose')->where('user', $user->_id)->where('currency', $currency->id())->count() }}
                                                </div>
                                            </th>
                                            <th data-highlight style="text-align: right">
                                                <div>
                                                    {{ number_format(\Illuminate\Support\Facades\DB::table('games')->where('demo', '!=', true)->where('user', $user->_id)->where('currency', $currency->id())->sum('wager'), 8, '.', '') }}
                                                    <i class="{{ $currency->icon() }}" style="color: {{ $currency->style() }}"></i>
                                                </div>
                                            </th>
                                            <th data-highlight>
                                                <div>
                                                    {{ number_format(\App\Investment::getUserProfit($currency, \Carbon\Carbon::minValue(), $user), 8, '.', '') }}
                                                    <i class="{{ $currency->icon() }}" style="color: {{ $currency->style() }}"></i>
                                                </div>
                                            </th>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="cat">
                                    {{ __('general.profile.latest_games') }}
                                </div>
                                <table class="live-table">
                                    <thead>
                                    <tr>
                                        <th>
                                            {{ __('general.bets.game') }}
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ __('general.bets.time') }}
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ __('general.bets.bet') }}
                                        </th>
                                        <th class="d-none d-md-table-cell">
                                            {{ __('general.bets.mul') }}
                                        </th>
                                        <th>
                                            {{ __('general.bets.win') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="live_games user_games_selector"></tbody>
                                </table>
                            @endif
                        </div>
                    @endif
                    @if($isOwner)
                        <div data-tab="security" style="display: none">
                            <div class="cat">{{ __('general.profile.general') }}</div>
                            <div class="input-profile-group">
                                <div>{{ __('general.profile.login') }}</div>
                                <input id="loginUpdate" placeholder="Login" value="{{ auth()->user()->name }}">
                                <button class="btn btn-primary" data-update-name>{{ __('general.profile.update') }}</button>
                            </div>

                            <div class="input-profile-group">
                                <div>{{ __('general.profile.email') }}</div>
                                <input id="emailUpdate" placeholder="Email" value="{{ auth()->user()->email }}" type="email">
                                <button class="btn btn-primary" data-update-email>{{ __('general.profile.update') }}</button>
                            </div>

                            <div class="cat">{{ __('general.profile.password') }}</div>
                            <div class="input-profile-group">
                                <div>{{ __('general.profile.password_current') }}</div>
                                <input type="password" data-password="current">
                            </div>
                            <div class="input-profile-group">
                                <div>{{ __('general.profile.password_new') }}</div>
                                <input type="password" data-password="new">
                            </div>
                            <div class="input-profile-group">
                                <div>{{ __('general.profile.password_repeat') }}</div>
                                <input type="password" data-password="confirm">
                                <button data-reset-password class="btn btn-primary">{{ __('general.profile.password_reset') }}</button>
                            </div>

                            <div class="cat">{{ __('general.profile.2fa') }}</div>
                            <div class="settingsNotify" style="text-align: left">
                                <div class="settingsNotifyLoading" style="display: none"></div>
                                @php
                                    $tfa = auth()->user()->tfa();
                                    $secret = $tfa->createSecret(160);
                                @endphp

                                @if(!(auth()->user()->tfa_enabled ?? false))
                                    {{ __('general.profile.copy_this_to_2fa') }}
                                    <input id="2facode" onclick="this.select()" type="text" style="cursor: pointer !important;" class="mt-1" value="{{ $secret }}" data-toggle="tooltip" data-placement="top" title="{{ __('wallet.copy') }}" readonly>
                                    <div class="mt-2">
                                        <div class="text-center mb-2 mt-2">{{ __('general.profile.keep_secure') }}</div>
                                        <canvas id="qrcanvas" data-text="{{ $tfa->getQRText('wager.co.nz', $secret) }}" class="d-flex ml-auto mr-auto">
                                    </div>
                                    <div>{{ __('general.profile.2fa_code') }}</div>
                                    <input type="text" id="2faucode" class="mt-1">
                                    <button id="enable2fa" class="btn btn-primary">{{ __('general.profile.2fa_enable') }}</button>
                                @else
                                    <div class="text-center">{!! __('general.profile.2fa_enabled') !!}</div>
                                    <button id="2fadisable" class="btn btn-primary mt-2 btn-block">{{ __('general.profile.disable_2fa') }}</button>
                                @endif
                            </div>
                        </div>
                        <div data-tab="settings" style="display: none">
                            <div class="avatar-settings">
                                <img alt src="{{ auth()->user()->avatar }}">
                                <input class="d-none" type="file" id="image-input">
                                <button class="btn btn-primary" data-change-avatar>{{ __('general.profile.change_avatar') }}</button>
                            </div>
                            <div class="cat">{{ __('general.profile.social') }}</div>
                            <div class="link-group">
                                {{ __('general.profile.vk') }}
                                <span>
                                    @if($user->vk != null)
                                        <i class="fal fa-check mr-1"></i> {{ __('general.profile.linked') }}
                                    @else
                                        <a href="/auth/vk">{{ __('general.profile.link') }}</a>
                                    @endif
                                </span>
                            </div>
                            <div class="link-group">
                                {{ __('general.profile.fb') }}
                                <span>
                                    @if($user->fb != null)
                                        <i class="fal fa-check mr-1"></i> {{ __('general.profile.linked') }}
                                    @else
                                        <a href="/auth/fb">{{ __('general.profile.link') }}</a>
                                    @endif
                                </span>
                            </div>
                            <div class="link-group">
                                {{ __('general.profile.google') }}
                                <span>
                                    @if($user->google != null)
                                        <i class="fal fa-check mr-1"></i> {{ __('general.profile.linked') }}
                                    @else
                                        <a href="/auth/google">{{ __('general.profile.link') }}</a>
                                    @endif
                                </span>
                            </div>
                            <div class="link-group">
                                {{ __('general.profile.discord') }}
                                <span>
                                    @if($user->discord != null)
                                        <i class="fal fa-check mr-1"></i> {{ __('general.profile.linked') }}
                                    @else
                                        <a href="/auth/discord">{{ __('general.profile.link') }}</a>
                                    @endif
                                </span>
                            </div>
                            <div class="link-group">
                                {{ __('general.profile.steam') }}
                                <span>
                                    @if($user->discord != null)
                                        <i class="fal fa-check mr-1"></i> {{ __('general.profile.linked') }}
                                    @else
                                        <a href="/auth/steam">{{ __('general.profile.link') }}</a>
                                    @endif
                                </span>
                            </div>

                            @if(auth()->user()->vipLevel() > 0)
                                <div class="settingsNotify mt-2">
                                    @if(auth()->user()->discord == null)
                                        {!! __('general.profile.link_discord') !!}
                                    @else
                                        {!! __('general.profile.discord_vip') !!}
                                        <button data-vip-discord-update class="btn btn-block btn-primary mt-2">{{ __('general.profile.discord_vip_ok') }}</button>
                                    @endif
                                </div>
                            @endif
                            <div class="cat">{{ __('general.profile.privacy') }}</div>
                            <div class="form-check pl-0">
                                <label for="stackedCheck1" class="form-check-label">{{ __('general.profile.set_private_profile') }}</label>
                                <input onchange="$.request('settings', ['privacy_toggle'], 'get')" {{ auth()->user()->private_profile ? 'checked' : '' }} data-on="<i class='fal fa-check'></i>" data-off="<i class='fal fa-times'></i>" id="stackedCheck1" class="form-check-input" type="checkbox" data-toggle="toggle">
                            </div>
                            <div class="form-check pl-0">
                                <label for="stackedCheck2" class="form-check-label">{{ __('general.profile.set_private_bets') }}</label>
                                <input onchange="$.request('settings', ['privacy_bets_toggle'], 'get')" {{ auth()->user()->private_bets ? 'checked' : '' }} data-on="<i class='fal fa-check'></i>" data-off="<i class='fal fa-times'></i>" id="stackedCheck2" class="form-check-input" type="checkbox" data-toggle="toggle">
                            </div>
                            <div class="cat">{{ __('general.profile.fairness') }}</div>
                            <div>{{ __('general.profile.client_seed') }}</div>
                            <a href="javascript:void(0)" onclick="$.modal('change_client_seed')" data-toggle="tooltip" data-placement="bottom" title="{{ __('general.profile.change') }}">{{ auth()->user()->client_seed }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
