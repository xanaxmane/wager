<div class="auth modal">
    <div class="content">
        <i class="fas fa-close-symbol"></i>

        <div class="heading">
            {{ __('general.auth.login') }}
        </div>
        <div class="divider">
            <div class="line"></div>
            {{ __('general.auth.through_social') }}
            <div class="line"></div>
        </div>
        <div class="mt-2">
            <div class="auth-button-group">
                <button class="btn btn-vk" data-social="vk"><i class="fab fa-vk"></i></button>
                <button class="btn btn-facebook" data-social="fb"><i class="fab fa-facebook"></i></button>
                <button class="btn btn-google" data-social="google"><i class="fab fa-google"></i></button>
                <button class="btn btn-discord" data-social="discord"><i class="fab fa-discord"></i></button>
                <button class="btn btn-steam" data-social="steam"><i class="fab fa-steam"></i></button>
            </div>
        </div>
        <div class="divider">
            <div class="line"></div>
            {{ __('general.auth.through_login') }}
            <div class="line"></div>
        </div>
        <div class="mt-2 mb-2">
            <input id="login" type="text" placeholder="{{ __('general.auth.credentials.login') }}">
        </div>
        <div class="mt-2 mb-2">
            <input id="password" type="password" placeholder="{{ __('general.auth.credentials.password') }}">
        </div>
        <button class="btn btn-primary btn-block p-3">{{ __('general.auth.login') }}</button>
        <div class="auth-footer" id="auth-footer" style="display: none">
            <div>{!! __('general.auth.notice') !!}</div>
            <span onclick="$.register()">{{ __('general.auth.create_account') }}</span>
        </div>
        <div class="auth-footer" id="register-footer" style="display: none">
            <div>{!! __('general.auth.notice') !!}</div>
            <span onclick="$.auth()">{{ __('general.auth.login') }}</span>
        </div>
    </div>
</div>
