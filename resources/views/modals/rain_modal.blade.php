<div class="rain_modal modal">
    <div class="content">
        <i class="fas fa-close-symbol"></i>

        <select class="currency-selector-withdraw">
            @foreach(\App\Currency\Currency::all() as $currency)
                <option value="{{ $currency->id() }}" data-icon="{{ $currency->icon() }}" data-style="{{ $currency->style() }}">
                    {{ number_format(auth()->user()->balance($currency)->get(), 8, '.', '') }}
                </option>
            @endforeach
        </select>

        <div class="cc_label">{{ __('general.chat_commands.modal.rain.amount') }}</div>
        <input id="rainamount" type="text" value="0.00000000">
        <div class="cc_label">{{ __('general.chat_commands.modal.rain.number_of_users') }}</div>
        <input id="rainusers" type="text" value="10">
        <button class="btn btn-primary">{{ __('general.chat_commands.modal.rain.send') }}</button>
    </div>
</div>
