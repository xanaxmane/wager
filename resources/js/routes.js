$.routes = function() {
    return {
        '/': ['/js/pages/index.js'],
        '/help': ['/js/pages/help.js'],
        '/job': ['/js/pages/job.js'],
        '/bonus': ['/js/pages/bonus.js'],
        '/user': ['/js/pages/user.js'],
        '/fairness': ['js/pages/fairness.js'],
        '/vip': ['js/pages/vip.js'],
        '/partner': ['js/pages/partner.js'],
        '/wallet': ['js/pages/wallet.js'],
        '/game': [`/js/pages/${window.location.pathname.substr(window.location.pathname.lastIndexOf('/') + 1)}.js`],

        '/admin': ['/js/admin/pages/dashboard.js'],
        '/admin/promo': ['/js/admin/pages/promo.js'],
        '/admin/settings': ['/js/admin/pages/settings.js'],
        '/admin/notifications': ['/js/admin/pages/notifications.js'],
        '/admin/users': ['/js/admin/pages/users.js'],
        '/admin/user': ['/js/admin/pages/user.js'],
        '/admin/wallet': ['/js/admin/pages/wallet.js'],
        '/admin/wallet_ignored': ['/js/admin/pages/wallet.js'],
        '/admin/modules': ['/js/admin/pages/modules.js'],
        '/admin/currency': ['/js/admin/pages/currency.js'],
        '/admin/aggregators': ['/js/admin/pages/aggregators.js'],
        '/admin/bankdeposits': ['/js/admin/pages/bankdeposits.js']
    }
};
