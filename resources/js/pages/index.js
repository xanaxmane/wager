import Glide from '@glidejs/glide';

let destroy = null, init;

$.on('/', function(){
    init = function() {
        if(window.location.pathname !== '/') return;

        if(destroy != null) destroy();

        const glide = new Glide('#slider', {
            type: 'carousel',
            perView: 1,
            focusAt: 'center',
            gap: 0,
            autoplay: 4000,
            keyboard: false
        });
        glide.mount();
        destroy = function() {
            glide.destroy();
        }
    };

    if(destroy == null) {
        $(document).on('wager:chatToggle', function() {
            setTimeout(init, 301);
        });
    }

    init();
}, ['/css/pages/index.css']);
