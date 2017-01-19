///csrf_token
(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
})();

/*Destroy action*/
(function () {
    jQuery(document.body).on('click', '[data-action="destroy"],[data-action="confirm"]', function (evt) {

        var confirmation = $(this).data('confirm');
        confirmation = confirmation ? confirmation : 'Tem certeza?';
        if (!confirm(confirmation)) {
            evt.preventDefault();
        }
    });
})();
