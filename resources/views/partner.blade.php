<div class="container-fluid">
    <div class="row">
        <div class="col vertical-tabs-column">
            <div class="vertical-tabs">
                <div data-toggle-tab="overview" class="option active">
                    {{ __('partner.tabs.overview') }}
                </div>
                @if(!auth()->guest())
                    <div data-toggle-tab="list" class="option">
                        {{ __('partner.tabs.list') }}
                    </div>
                    <div data-toggle-tab="analytics" class="option">
                        {{ __('partner.tabs.analytics') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="col vertical-tabs-content-column">
            <div class="vertical-tabs-content">
                <div class="tab-content" data-tab="overview">
                    {!! auth()->guest() ? __('partner.overview.guest_content') : __('partner.overview.content', ['id' => auth()->user()->_id]) !!}
                </div>
                @if(!auth()->guest())
                    <div class="tab-content" data-tab="list" style="display: none">
                        <table id="refs" class="table dt-responsive nowrap">
                            <thead>
                            <tr>
                                <th>{{ __('partner.list.name') }}</th>
                                <th>{{ __('partner.list.activity') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\User::where('referral', auth()->user()->id)->get() as $user)
                                    <tr onclick="redirect('/user/{{ $user->_id }}')" style="cursor: pointer">
                                        <td><img alt src="{{ $user->avatar }}" style="width: 32px; height: 32px; margin-right: 5px;"> {{ $user->name }}</td>
                                        @php
                                            $percent = ($user->games() / floatval(\App\Settings::where('name', 'referrer_activity_requirement')->first()->value)) * 100;
                                            if($percent > 100) $percent = 100;
                                            $percent = number_format($percent, 2, '.', '');
                                        @endphp
                                        <td>{{ in_array($user->_id, auth()->user()->referral_wager_obtained ?? []) ? __('general.yes') : __('general.no').' ('.$percent.'%)' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-content" data-tab="analytics" style="display: none">
                        <div>{!! __('partner.analytics.referrals', ['count' => \App\User::where('referral', auth()->user()->_id)->count()])  !!}</div>
                        <div>{!! __('partner.analytics.referrals_bonus', ['count' => count(auth()->user()->referral_wager_obtained ?? [])]) !!}</div>
                        <div>{!! __('partner.analytics.referrals_wheel', ['count' => auth()->user()->referral_bonus_obtained ?? 0]) !!}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
