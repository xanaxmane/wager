<div class="bonus-side-menu-container">
    <script type="text/javascript">
        window.next = {{ auth()->user()->bonus_claim->timestamp ?? 0 }};
        window.timeout();
    </script>

    <div class="wheelContainer">
        <div class="wheel"></div>
        <div class="wheelBlock">
            <div class="wheelDesc">
                <div id="reload"></div>
                <div>{{ __('general.reload') }}</div>
            </div>
            <button class="btn btn-primary">{{ __('general.spin') }}</button>
        </div>
    </div>
</div>
