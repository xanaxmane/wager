const defaultNotificationHandler = function(notification) {
    return {
        'title': notification.title,
        'message': notification.message
    }
}
const notifications = {
    'App\\Notifications\\CustomNotification': defaultNotificationHandler,
    'App\\Notifications\\WithdrawAccepted': defaultNotificationHandler,
    'App\\Notifications\\WithdrawDeclined': defaultNotificationHandler,
    'App\\Notifications\\VipDiscordNotification': defaultNotificationHandler,
    'App\\Notifications\\TipNotification': defaultNotificationHandler,
    'App\\Notifications\\EmailNotification': defaultNotificationHandler,
    default_icon: 'fad fa-galaxy'
};

const addNotification = function(id, title, message, icon) {
    $('.notifications-content .os-content').prepend(`
        <div role="alert" class="toast" data-autohide="false" data-toast-id="${id}">
            <div class="toast-header">
                <i class="${icon}" style="margin-right: 5px"></i>
                <span class="mr-auto">${title}</span>
                <i data-notification-dismiss="${id}" class="fal fa-times" data-dismiss="toast"></i>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `);

    $(document).on('click', `[data-notification-dismiss="${id}"]`, function() {
        $.request('notifications/mark', { id: id });
        $(`[data-toast-id="${id}"]`).remove();

        if($('.notifications-content .toast.show').length <= 1) $('[data-notification-attention]').fadeOut('fast', function() {
            $(this).remove();
        });
    });

    $('.toast').toast('show');

    if($('[data-notification-attention]').length === 0) {
        $('[data-notification-view]').prepend(`<span class="notification pulsating-circle" data-notification-attention></span>`);
    }
};

$.displayNotifications = function() {
    $('.notifications-overlay').fadeToggle('fast');
    $('.notifications').toggleClass('active');
};

$(document).ready(function() {
    $(document).on('click', '.notifications-overlay, .notifications [data-close-notifications]', $.displayNotifications);

    $('.notifications-content').overlayScrollbars({
        scrollbars: {
            autoHide: 'leave'
        },
        className: "os-theme-thin-light"
    });

    if(!$.isGuest()) {
        window.Echo.channel(`laravel_database_private-App.User.${$.userId()}`).notification((notification) => {
            const meta = notifications[notification.type](notification);
            addNotification(notification.id, meta.title, meta.message, meta.icon === undefined ? notifications.default_icon : meta.icon);
        });

        $.request('notifications/unread').then(response => {
            _.forEach(response.notifications, function(notification) {
                const meta = notifications[notification.type](notification.data);
                addNotification(notification.id, meta.title, meta.message, meta.icon === undefined ? notifications.default_icon : meta.icon);
            });
        });
    }
});
