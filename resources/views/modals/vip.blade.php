<div class="vip modal">
    <div class="content">
        <i class="fas fa-close-symbol"></i>
        <div class="modal-scrollable-content">
            <img class="vip-logo" src="/img/misc/vip-logo.svg" alt>

            @php
                $currency = auth()->user()->closestVipCurrency();
                $breakpoints = [
                    1 => floatval($currency->option('vip_bronze')),
                    2 => floatval($currency->option('vip_silver')),
                    3 => floatval($currency->option('vip_gold')),
                    4 => floatval($currency->option('vip_platinum')),
                    5 => floatval($currency->option('vip_diamond'))
                ];

                $percent = number_format(auth()->user()->vipLevel() == 5 ? 100 : (\Illuminate\Support\Facades\DB::table('games')->where('user', auth()->user()->_id)->where('currency', $currency->id())->where('demo', '!=', true)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->sum('wager') / $breakpoints[auth()->user()->vipLevel() + 1]) * 100, 2, '.', '');
            @endphp
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%;">{{ $percent < 8 ? '' : $percent.'%' }}</div>
            </div>
            <div class="vipProgress">
                <div>
                    {{ __('vip.rank.'.(auth()->user()->vipLevel() == 5 ? 4 : auth()->user()->vipLevel())) }}
                </div>
                <div>
                    @switch(auth()->user()->vipLevel() == 5 ? 5 : auth()->user()->vipLevel() + 1)
                        @case(1)
                            <svg><use href="#vip-bronze"></use></svg>
                            @break
                        @case(2)
                            <svg><use href="#vip-silver"></use></svg>
                            @break
                        @case(3)
                            <svg><use href="#vip-gold"></use></svg>
                            @break
                        @case(4)
                            <svg><use href="#vip-platinum"></use></svg>
                            @break
                        @case(5)
                            <svg><use href="#vip-diamond"></use></svg>
                            @break
                    @endswitch
                    {{ __('vip.rank.'.(auth()->user()->vipLevel() == 5 ? 5 : auth()->user()->vipLevel() + 1)) }}
                </div>
            </div>
            <div class="vipDesc mb-2">{{ __('vip.description', ['currency' => auth()->user()->closestVipCurrency()->name()]) }}</div>
            <div class="font-weight-bold" style="font-size: 1.05em">{{ __('vip.benefits') }}</div>
            <div class="vipDesc">{{ __('vip.benefits_description') }}</div>
            <div class="expandableBlock">
                <div class="expandableBlockHeader">
                    <svg><use href="#vip-bronze"></use></svg>
                    {{ __('vip.rank.1') }}
                    <i class="fas fa-angle-left"></i>
                </div>
                <div class="expandableBlockContent" style="display: none;">
                    <ul>
                        <li>{{ __('vip.benefit_list.bronze.1') }}</li>
                        <li>{{ __('vip.benefit_list.bronze.2') }}</li>
                        <li>{{ __('vip.benefit_list.bronze.3') }}</li>
                    </ul>
                </div>
            </div>
            <div class="expandableBlock">
                <div class="expandableBlockHeader">
                    <svg><use href="#vip-silver"></use></svg>
                    {{ __('vip.rank.2') }}
                    <i class="fas fa-angle-left"></i>
                </div>
                <div class="expandableBlockContent" style="display: none;">
                    <ul>
                        <li>{{ __('vip.benefit_list.silver.1') }}</li>
                        <li>{{ __('vip.benefit_list.silver.2') }}</li>
                    </ul>
                </div>
            </div>
            <div class="expandableBlock">
                <div class="expandableBlockHeader">
                    <svg><use href="#vip-gold"></use></svg>
                    {{ __('vip.rank.3') }}
                    <i class="fas fa-angle-left"></i>
                </div>
                <div class="expandableBlockContent" style="display: none;">
                    <ul>
                        <li>{{ __('vip.benefit_list.gold.1') }}</li>
                        <li>{{ __('vip.benefit_list.gold.2') }}</li>
                    </ul>
                </div>
            </div>
            <div class="expandableBlock">
                <div class="expandableBlockHeader">
                    <svg><use href="#vip-platinum"></use></svg>
                    {{ __('vip.rank.4') }}
                    <i class="fas fa-angle-left"></i>
                </div>
                <div class="expandableBlockContent" style="display: none;">
                    <ul>
                        <li>{{ __('vip.benefit_list.platinum.1') }}</li>
                        <li>{{ __('vip.benefit_list.platinum.2') }}</li>
                    </ul>
                </div>
            </div>
            <div class="expandableBlock">
                <div class="expandableBlockHeader">
                    <svg><use href="#vip-diamond"></use></svg>
                    {{ __('vip.rank.5') }}
                    <i class="fas fa-angle-left"></i>
                </div>
                <div class="expandableBlockContent" style="display: none;">
                    <ul>
                        <li>{{ __('vip.benefit_list.diamond.1') }}</li>
                        <li>{{ __('vip.benefit_list.diamond.2') }}</li>
                        <li>{{ __('vip.benefit_list.diamond.3') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
