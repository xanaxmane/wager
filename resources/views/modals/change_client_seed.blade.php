<div class="change_client_seed modal">
    <div class="content" style="min-height: unset !important;">
        <i class="fas fa-close-symbol"></i>

        <input class="mt-4 mb-4" type="text" id="new-client-seed" value="{{ auth()->user()->client_seed }}" placeholder="{{ __('general.profile.client_seed') }}">
        <button class="btn btn-primary mr-2" id="change-client-seed-btn">{{ __('general.change') }}</button>
        <button class="btn btn-secondary" onclick="$.modal('change_client_seed')">{{ __('general.cancel') }}</button>
    </div>
</div>
