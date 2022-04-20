<div class="overview modal">
    <div class="content">
        <i class="fas fa-close-symbol"></i>

        <div class="modal-scrollable-content">
            <div class="overview-share-options">
                <a data-share="link" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="{{ __('general.share_link') }}">
                    <i class="fas fa-link"></i>
                </a>
                @if(!auth()->guest())
                    <a data-share="chat" href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="{{ __('general.share_chat') }}">
                        <i class="fas fa-comments"></i>
                    </a>
                @endif
            </div>

            <div class="heading text-left"></div>

            <div class="overview-player">
                <div>{{ __('general.bets.player') }}: <a onclick="$.modal('overview')"></a></div>
            </div>
            <div class="overview-bet">
                <div class="option">Bet<span></span></div>
                <div class="option">Multiplier<span></span></div>
                <div class="option">Result<span></span></div>
            </div>

            <div class="overview-render-target"></div>
            <div class="client_seed mt-2">
                <div>{{ __('general.fairness.client_seed') }}</div>
                <a onclick="$.modal('overview')" class="client_seed_target"></a>
            </div>
            <div class="server_seed mt-2">
                <div>{{ __('general.fairness.server_seed') }}</div>
                <a onclick="$.modal('overview')" class="server_seed_target"></a>
            </div>
            <div class="nonce mt-2">
                <div>{{ __('general.fairness.nonce') }}</div>
                <a onclick="$.modal('overview')" class="nonce_target"></a>
            </div>
        </div>
    </div>
</div>
