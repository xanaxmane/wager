@if(isset($data))
    @php
        $currency = \App\Currency\Currency::find($data);
        $cold = $currency->coldWalletBalance();
        $hot = $currency->hotWalletBalance();
    @endphp
    @if($currency->isRunning())
        <div><strong>Deposit wallet balance:</strong> {{ number_format($cold, 8, '.', '') }} {{ $currency->name() }}</div>
        <div><strong>Auto-withdraw wallet balance:</strong> {{ number_format($hot, 8, '.', '') }} {{ $currency->name() }}</div>
    @else
        <div class="text-danger"><strong>Node is offline</strong></div>
    @endif
@else
    @php
        $foundEmpty = false;
        $foundCount = 0;
        foreach (\App\Currency\Currency::all() as $currency) {
            if($currency->option('withdraw_address') === '' || $currency->option('transfer_address') === '' || $currency->option('withdraw_address') === '1' || $currency->option('transfer_address') === '1') {
                $foundEmpty = true;
                $foundCount++;
            }
        }
    @endphp

    <div class="row page-title">
        <div class="col-md-12">
            <h4 class="mb-1 mt-1">Currencies</h4>
        </div>
    </div>
    <div class="container-fluid">
        @if($foundEmpty)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Auto-setup</h5>
                            <div>Click this button to setup missing wallet addresses.</div>
                            <div class="mb-2"><strong>When generation finishes save provided backups to safe location, otherwise you could lose access to your wallet!</strong></div>
                            <button class="btn btn-danger" id="autogen">Generate</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($foundCount == 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Send deposits to address</h5>
                            <div class="row">
                                <div class="col-12 col-lg-2">
                                    <select class="form-control" id="cs_currency">
                                        @foreach(\App\Currency\Currency::all() as $currency)
                                            <option value="{{ $currency->id() }}">{{ $currency->name() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-8 mt-2 mt-lg-0">
                                    <input class="form-control" id="cs_address" placeholder="Address">
                                    <input class="form-control mt-2" id="cs_amount" placeholder="Amount" value="0.00000000">
                                </div>
                                <div class="col-3 col-lg-2 mt-2 mt-lg-0">
                                    <button class="btn btn-danger btn-block" id="cs_send">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            @foreach(\App\Currency\Currency::all() as $currency)
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-1">{{ $currency->name() }}</h5>
                            <h6 class="text-muted font-weight-normal mt-0 mb-3">{{ $currency->id() }}</h6>
                            <div data-currency-wallet="{{ $currency->id() }}">
                                <div><strong>Deposit wallet balance:</strong> <div class="spinner-grow spinner-grow-sm"></div></div>
                                <div><strong>Auto-withdraw wallet balance:</strong> <div class="spinner-grow spinner-grow-sm"></div></div>
                            </div>
                            <div class="mt-2">
                                @foreach($currency->getOptions() as $option)
                                    <div class="form-group mt-2">
                                        <label data-toggle="tooltip" data-placement="top" title="{{ $option->id() }}">{{ $option->name() }}</label>
                                        <input {{ $option->readOnly() ? 'disabled' : '' }} data-currency="{{ $currency->id() }}" data-option="{{ $option->id() }}" type="text" value="{{ $currency->option($option->id()) }}" class="form-control">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script>$('.pageContent').css({opacity: 1})</script>
@endif
