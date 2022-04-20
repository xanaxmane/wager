import iziToast from 'izitoast';

$.success = function(message) {
    iziToast.success({
        'message': message,
        'position': 'topRight'
    });
};

$.error = function(message) {
    iziToast.error({
        'message': message,
        'position': 'topRight'
    });
};

$.warning = function(message) {
    iziToast.warning({
        'message': message,
        'position': 'topRight'
    });
};

$.info = function(message) {
    iziToast.info({
        'message': message,
        'position': 'topRight'
    });
};
