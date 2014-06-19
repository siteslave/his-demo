$(function() {
    var preg = {};

    preg.target = $('#content');

    preg.setActive = function (obj) {
        $('#collapseMain a').each(function () {
            var $this = $(this);

            $this.removeClass('list-group-item-success');
        });

        $('#collapseTools a').each(function () {
            var $this = $(this);

            $this.removeClass('list-group-item-success');
        });

        obj.addClass('list-group-item-success');
    };

    $('a[href="#list"]').on('click', function() {
        preg.setActive($(this));
        app.loadPage({
            target: preg.target,
            url: pageUrl[0],
            //params: {},
            scripts: [scriptUrl[0]]
        });
    });
    $('a[href="#register"]').on('click', function() {
        preg.setActive($(this));
        app.loadPage({
            target: preg.target,
            url: pageUrl[1],
            //params: {},
            scripts: [scriptUrl[1]]
        });
    });

    app.loadPage({
        target: preg.target,
        url: pageUrl[0],
        //params: {},
        scripts: [scriptUrl[0]]
    });
});
