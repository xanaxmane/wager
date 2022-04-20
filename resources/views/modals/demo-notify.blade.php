<div class="demo-notify modal">
    <div class="content">
        <i class="fas fa-close-symbol"></i>

        <div class="heading">Woah there stranger!</div>
        <div class="text-center mt-4 mb-4">Please sign up to continue playing in demo mode, or with real balance! You can claim free balance every 3 minutes with an account. </div>
        <button class="btn btn-primary btn-block" onclick="$.modal('demo-notify', 'hide'); $.register();">{{ __('general.demo.register') }}</button>
    </div>
</div>
