$(function () {

    var target = $('#content'),
        id = $('#txtPregnancyId').val();

    var setActive = function (obj) {
        $('#collapseTools a').each(function () {
            var $this = $(this);

            $this.removeClass('list-group-item-success');
        });

        obj.addClass('list-group-item-success');
    };

    $('a[href="#info"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[0],
            params: { id: id },
            scripts: [scriptUrl[0]]
        });
    });

    setActive($('a[href="#info"]'));
    app.loadPage({
        target: target,
        url: pageUrl[0],
        params: { id: id },
        scripts: [scriptUrl[0]]
    });

});
