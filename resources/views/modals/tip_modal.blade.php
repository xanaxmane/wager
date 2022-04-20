<div class="tip_modal modal">
    <div class="content">
        <i class="fas fa-close-symbol"></i>

        <select class="currency-selector-withdraw">
            @foreach(\App\Currency\Currency::all() as $currency)
                <option value="{{ $currency->id() }}" data-icon="{{ $currency->icon() }}" data-style="{{ $currency->style() }}">
                    {{ number_format(auth()->user()->balance($currency)->get(), 8, '.', '') }}
                </option>
            @endforeach
        </select>

        <div class="cc_label">{{ __('general.chat_commands.modal.tip.user') }}</div>
        <input id="tipname" type="text">
        <div class="cc_label">{{ __('general.chat_commands.modal.tip.amount') }}</div>
        <input id="tipamount" type="text" value="0.00000000">

        <div class="custom-control custom-checkbox mt-2">
            <label>
                <input checked id="tippublic" type="checkbox" class="custom-control-input">
                <div class="custom-control-label">{{ __('general.chat_commands.modal.tip.make_public') }}</div>
            </label>
        </div>

        <button class="btn btn-primary">{{ __('general.chat_commands.modal.tip.send') }}</button>
    </div>
</div>
