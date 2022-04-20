<div class="tfa modal">
    <div class="content">
        <div class="lockContainer">
            <i class="fas fa-lock"></i>

            <div class="lock"></div>

            <div class="bubble"></div>
            <div class="bubble"></div>
        </div>

        <div class="tfah">2FA</div>
        <div class="tfad">{{ __('general.profile.2fa_description') }}</div>

        <div class="inputs">
            <input maxlength="2">
            <input maxlength="2">
            <input maxlength="2">
            <input maxlength="2">
            <input maxlength="2">
            <input maxlength="1">
        </div>

        <div class="tfaStatus">{{ __('general.profile.2fa_digits', ['digits' => 6]) }}</div>
    </div>
</div>
