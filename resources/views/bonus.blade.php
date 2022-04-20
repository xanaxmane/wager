<div class="container-fluid">
    <div class="bonusContainer">
        <div class="bonusHeader">{{ __('general.head.bonus') }}</div>
        <div class="row h">
            <div class="col-12 col-lg-6 h">
                <div data-bonus-modal-contents></div>
            </div>
            <div class="col-12 col-lg-6 h">
                <div class="bonusSidebar">
                    <div class="sidebarEntry" data-bonus-toggle="wheel">
                        <div class="icon">
                            <i class="fas fa-wheel"></i>
                        </div>
                        <div class="entry">
                            <div class="title">BONUS WHEEL</div>
                            <div class="desc">Spin the wheel every 3 minutes and win free bonus balance!</div>
                        </div>
                    </div>
                    <div class="sidebarEntry" data-bonus-toggle="partner">
                        <div class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="entry">
                            <div class="title">REFERRAL WHEEL</div>
                            <div class="desc">Invite your friends to Wager and get a bonus spin for every 10 active referrals!</div>
                        </div>
                    </div>
                    <div class="sidebarEntry" data-bonus-toggle="promo">
                        <div class="icon">
                            <i class="fas fa-barcode-alt"></i>
                        </div>
                        <div class="entry">
                            <div class="title">{{ __('bonus.promo.title') }}</div>
                            <div class="desc">Have a promocode? Redeem it and reap the rewards!</div>
                        </div>
                    </div>
                    <div class="sidebarEntry" data-bonus-toggle="discord">
                        <div class="icon">
                            <i class="fab fa-discord"></i>
                        </div>
                        <div class="entry">
                            <div class="title">{{ __('bonus.discord.title') }}</div>
                            <div class="desc">Join our Discord server and receive free bonus balance!</div>
                        </div>
                    </div>
                    <div class="sidebarEntry" data-bonus-toggle="rain">
                        <div class="icon">
                            <i class="fas fa-cloud-sun-rain"></i>
                        </div>
                        <div class="entry">
                            <div class="title">{{ __('bonus.rain.title') }}</div>
                            <div class="desc">Every hour, an event occurs in the chat that distributes free bonus balance to lucky random players!</div>
                        </div>
                    </div>
                    <div class="sidebarEntry" onclick="{{ (auth()->guest() || auth()->user()->vipLevel() == 0) ? "$.vip()" : "$.vipBonus()" }}">
                        <div class="icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="entry">
                            <div class="title">{{ __('bonus.vip.title') }}</div>
                            <div class="desc">{!! auth()->guest() || auth()->user()->vipLevel() == 0 ? __('bonus.vip.unavailable_description') : __('bonus.vip.description') !!}</div>
                        </div>
                    </div>
                    <div class="sidebarEntry" data-notification-bonus style="display: none">
                        <div class="icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="entry">
                            <div class="title">{{ __('bonus.notifications.title') }}</div>
                            <div class="desc">Claim your free weekly VIP bonus balance! Wager more to accumulate a bigger bonus!</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
