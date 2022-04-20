require('./wallet');
require('./auth');
require('./vip');
require('./change-client-seed');
require('./tfa');
require('./invest');

$.modal = function(id, option = null) {
    return new Promise(function(resolve) {
        const load = function(e, firstTime) {
            e.find('.loaderAnimation').fadeOut('fast', () => $(this).remove());

            if(firstTime) {
                e.find('.modal-scrollable-content').overlayScrollbars({
                    scrollbars: {
                        autoHide: 'leave'
                    },
                    className: "os-theme-thin-light"
                });

                $.each(e.find('i'), (i, e) => $.transformIcon($(e)));
            }

            if(option != null) {
                switch (option) {
                    case 'show':
                        e.toggleClass('show', true);
                        break;
                    case 'hide':
                        e.toggleClass('show', false);
                        break;
                    default:
                        throw new Error(`Unknown modal option ${option}`);
                }
            } else e.toggleClass('show');

            resolve(e, firstTime);
        };

        if($(`.${id}`).length === 0) {
            // TODO: Show preloader
            $.get(`/modals.${id}`, function(response) {
                $('.modal-wrapper').prepend(response);
                load($(`.${id}`), true);
            });
        }
        else load($(`.${id}`), false);
    });
}

$(document).ready(function() {
    $(document).on('click', '.modal .fa-close-symbol', function() {
        const modal = $(this).parent().parent();
        modal.toggleClass('show');

        setTimeout(() => $.blockPlayButton(false), 2000);
    });

    $.fn.uiBlocker = function(show = true) {
        if(show) {
            if(this.find('.loader').length > 0) this.find('.loader').remove();
            this.prepend(`<div class="loader">${window.loader()}</div>`);
        } else this.find('.loader').fadeOut('fast', () => this.find('.loader').remove());
    };
});
