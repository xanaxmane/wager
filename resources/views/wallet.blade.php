@php
    if(auth()->guest()) {
        header('Location: /');
        die();
    }
@endphp

@if(isset($data))
    @if($data === 'payments')
        @if(\App\Invoice::where('user', auth()->user()->_id)->count() == 0)
            <div class="walletHistoryEmpty">
                <i class="fas fa-waiting"></i>
                <div>{{ __('wallet.history.empty') }}</div>
            </div>
        @else
            <table class="live-table">
                <thead>
                    <tr>
                        <th>
                            {{ __('wallet.history.name') }}
                        </th>
                        <th class="d-none d-md-table-cell">
                            {{ __('wallet.history.sum') }}
                        </th>
                        <th>
                            {{ __('wallet.history.date') }}
                        </th>
                        <th>
                            {{ __('wallet.history.status') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="live_games">
                @foreach(\App\Invoice::where('user', auth()->user()->_id)->latest()->get() as $invoice)
                    <tr>
                        <th>
                            <div>
                                <div><i class="{{ \App\Currency\Currency::find($invoice->currency)->icon() }}" style="color: {{ \App\Currency\Currency::find($invoice->currency)->style() }}"></i> {{ \App\Currency\Currency::find($invoice->currency)->name() }}</div>
                            </div>
                        </th>
                        <th class="d-none d-md-table-cell">
                            <div>
                                {{ number_format(floatval(is_float($invoice->sum) ? $invoice->sum : $invoice->sum->jsonSerialize()['$numberDecimal']), 8, '.', '') }}
                                <i class="{{ \App\Currency\Currency::find($invoice->currency)->icon() }}" style="color: {{ \App\Currency\Currency::find($invoice->currency)->style() }}"></i>
                            </div>
                        </th>
                        <th>
                            <div>
                                {{ $invoice->created_at->diffForHumans() }}
                            </div>
                        </th>
                        <th>
                            <div>
                                @if($invoice->type === 'currency')
                                    {{ $invoice->confirmations }}/{{ \App\Currency\Currency::find($invoice->currency)->option('confirmations') }} {{ __('wallet.history.confirmations') }}
                                @else
                                    @switch($invoice->status)
                                        @case(0)
                                            <span data-highlight>{{ __('wallet.pending') }}</span>
                                            @break
                                        @case(1)
                                            {{ __('wallet.history.withdraw_status.cancelled') }}
                                            @break
                                        @case(2)
                                            {{ __('wallet.history.withdraw_status.declined') }}
                                            @break
                                        @case(3)
                                            {{ __('wallet.history.withdraw_status.accepted') }}
                                            @break
                                    @endswitch
                                @endif
                            </div>
                        </th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    @elseif($data === 'withdraws')
        @if(\App\Withdraw::where('user', auth()->user()->_id)->count() == 0)
            <div class="walletHistoryEmpty">
                <i class="fas fa-waiting"></i>
                <div>{{ __('wallet.history.empty') }}</div>
            </div>
        @else
            <table class="live-table">
                <thead>
                <tr>
                    <th>
                        {{ __('wallet.history.name') }}
                    </th>
                    <th class="d-none d-md-table-cell">
                        {{ __('wallet.history.sum') }}
                    </th>
                    <th>
                        {{ __('wallet.history.date') }}
                    </th>
                    <th>
                        {{ __('wallet.history.status') }}
                    </th>
                </tr>
                </thead>
                <tbody class="live_games">
                @foreach(\App\Withdraw::where('user', auth()->user()->_id)->latest()->get() as $withdraw)
                    <tr>
                        <th>
                            <div>
                                <div><i class="{{ \App\Currency\Currency::find($withdraw->currency)->icon() }}" style="color: {{ \App\Currency\Currency::find($withdraw->currency)->style() }}"></i> {{ \App\Currency\Currency::find($withdraw->currency)->name() }}</div>
                                <div data-highlight>{{ $withdraw->address }}</div>
                            </div>
                        </th>
                        <th class="d-none d-md-table-cell">
                            <div>
                                {{ number_format($withdraw->sum, 8, '.', '') }} <i class="{{ \App\Currency\Currency::find($withdraw->currency)->icon() }}"></i>
                            </div>
                        </th>
                        <th>
                            <div>
                                {{ $withdraw->created_at->diffForHumans() }}
                            </div>
                        </th>
                        <th>
                            @switch($withdraw->status)
                                @case(0)
                                @case(3)
                                {{ __('wallet.history.withdraw_status.moderation') }}
                                @if($withdraw->status == 0)
                                    @if(!$withdraw->auto ?? false)
                                        <div data-highlight style="cursor: pointer;" onclick="$.cancelWithdraw('{{ $withdraw->_id }}')">
                                            {{ __('wallet.history.cancel') }}
                                        </div>
                                    @endif
                                @endif
                                @break
                                @case(1)
                                <div class="text-success">{{ __('wallet.history.withdraw_status.accepted') }}</div>
                                @break
                                @case(2)
                                <div class="text-danger">{{ __('wallet.history.withdraw_status.declined') }}</div>
                                <div data-highlight>{{ __('wallet.history.withdraw_status.reason') }} {{ $withdraw->decline_reason }}</div>
                                @break
                                @case(4)
                                {{ __('wallet.history.withdraw_status.cancelled') }}
                                @break
                            @endswitch
                        </th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    @endif
@else
<div class="container-fluid">
    <div class="walletPage">
        <div class="walletUiBlocker" style="display: none">
            <div class="successfulWalletAction" style="display: none">
                <div class="heading"></div>
                <div class="content"></div>
                <div class="d-flex ml-auto btn btn-primary close-action-notify">{{ __('general.close') }}</div>
            </div>
        </div>
        <div class="walletTabs">
            <div class="walletTab active" data-toggle-wallet-tab="deposit">{{ __('wallet.tabs.deposit') }}</div>
            <div class="walletTab" data-toggle-wallet-tab="withdraw">{{ __('wallet.tabs.withdraw') }}</div>
            <div class="walletTab" data-toggle-wallet-tab="history">{{ __('wallet.tabs.history') }}</div>
        </div>
        <div class="walletTabContent" data-wallet-tab="deposit">
            <div class="row">
                <div class="col-12 col-md-5 col-lg-4 col-xl-3">
                    <div class="walletColumnContent">
                        <div class="mb-3">{{ __('wallet.method') }}</div>
                        <div class="paymentMethods">
                            @foreach(\App\Currency\Currency::all() as $currency)
                                <div class="paymentMethod" data-deposit-type="{{ $currency->id() }}" data-deposit-action="qr">
                                    <div class="icon">
                                        <i class="{{ $currency->icon() }}" style="color: {{ $currency->style() }}"></i>
                                    </div>
                                    <div class="name">
                                        {{ $currency->displayName() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7 col-lg-8 col-xl-9">
                    <div class="walletColumnContent">
                        <div class="paymentMethodDesc">
                            {{ __('wallet.method') }}
                            <div class="mt-3 paymentDesc"></div>
                        </div>
                        <div class="divider">
                            <div class="line"></div>
                            <i class="fal fa-angle-down"></i>
                            <div class="line"></div>
                        </div>
                        <div class="walletOut"></div>
                        <div class="walletInfo mt-2">
                            <div class="walletInfoBlock">
                                <i class="fas fa-stopwatch"></i>
                                <div class="mt-3">
                                    {!! __('wallet.fast') !!}
                                </div>
                            </div>
                            <div class="walletInfoBlock">
                                <i class="fas fa-headset"></i>
                                <div class="mt-3">
                                    {!! __('wallet.troubles') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="walletTabContent" data-wallet-tab="withdraw" style="display: none">
            <div class="row">
                <div class="col-12 col-md-5 col-lg-4 col-xl-3">
                    <div class="walletColumnContent">
                        <div class="mb-3">{{ __('wallet.withdraw.method') }}</div>
                        <div class="paymentMethods">
                            @foreach(\App\Currency\Currency::all() as $currency)
                                <div class="paymentMethod" data-withdraw-type="{{ $currency->id() }}">
                                    <div class="icon">
                                        <i class="{{ $currency->icon() }}" style="color: {{ $currency->style() }}"></i>
                                    </div>
                                    <div class="name">
                                        {{ $currency->displayName() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-7 col-lg-8 col-xl-9">
                    <div class="walletColumnContent">
                        <div class="paymentMethodDesc">
                            {{ __('wallet.withdraw.method') }}
                            <div class="mt-3 paymentDesc"></div>
                        </div>
                        <div class="divider">
                            <div class="line"></div>
                            <i class="fal fa-angle-down"></i>
                            <div class="line"></div>
                        </div>
                        <div class="mt-2">
                            <div class="mb-2">{{ __('wallet.withdraw.enter_wallet') }}</div>
                            <input id="wallet" placeholder="{{ __('wallet.withdraw.wallet') }}">
                        </div>
                        <div class="divider">
                            <div class="line"></div>
                            <i class="fal fa-angle-down"></i>
                            <div class="line"></div>
                        </div>
                        <div class="walletOut">
                            <div id="withdrawSum"></div>
                            <input id="walletWithValue" type="number" step="0.00000001">
                            <div id="withdrawFee" class="mb-3"></div>
                            <button class="btn btn-primary" id="withdraw">{{ __('wallet.withdraw.go') }}</button>
                        </div>
                        <div class="walletInfo mt-2">
                            <div class="walletInfoBlock">
                                <i class="fas fa-stopwatch"></i>
                                <div class="mt-3">
                                    {!! __('wallet.fast') !!}
                                </div>
                            </div>
                            <div class="walletInfoBlock">
                                <i class="fas fa-headset"></i>
                                <div class="mt-3">
                                    {!! __('wallet.troubles') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="walletTabContent" data-wallet-tab="history" style="display: none">
            <div class="walletHistory">
                <div class="walletTabs">
                    <div class="walletTab" data-toggle-history-tab="payments">{{ __('wallet.tabs.deposits') }}</div>
                    <div class="walletTab" data-toggle-history-tab="withdraws">{{ __('wallet.tabs.withdraws') }}</div>
                </div>
                <div class="history-tab-content" data-history-tab="payments"></div>
                <div class="history-tab-content" data-history-tab="withdraws" style="display: none"></div>
            </div>
        </div>
    </div>
</div>
@endif
