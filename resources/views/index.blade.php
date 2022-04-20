<div class="container-fluid">
    <div class="slider">
        <div class="glide" id="slider">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    <li class="glide__slide" style="background: #3699ff">
                        <div class="slideContent">
                            <div class="slideContentWrapper">
                                <div class="header">
                                    Wager
                                </div>
                                <div class="description">
                                    Where simplicity meets an online casino!
                                </div>
                                <div class="button" onclick="window.open('https://wager.co.nz', '_blank')">
                                    Contact us
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left" data-glide-dir="<"></button>
                <button class="glide__arrow glide__arrow--right" data-glide-dir=">"></button>
            </div>
            <div class="glide__bullets" data-glide-el="controls[nav]">
                <button class="glide__bullet" data-glide-dir="=0"></button>
            </div>
        </div>
    </div>

    <div class="index_cat">
        <i class="fab fa-gripfire"></i> {{ __('general.our_games') }}
    </div>

    <div class="games">
        @foreach(\App\Games\Kernel\Game::list() as $game)
            <div class="game_poster game-{{ $game->metadata()->id() }}" @if(!$game->isDisabled()) onclick="redirect('/game/{{ $game->metadata()->id() }}')" @endif>
                @if($game->isDisabled())
                    <div class="unavailable">
                        <div class="slanting">
                            <div class="content">
                                {{ $game->metadata()->isPlaceholder() ? __('general.coming_soon') : __('general.not_available') }}
                            </div>
                        </div>
                    </div>
                @endif
                <div class="name">
                    {{ $game->metadata()->name() }}
                </div>
            </div>
        @endforeach
    </div>
</div>
