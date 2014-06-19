/**
 * Main Service script.
 *
 */

$(function() {
    var target = $('#content'),
        service_id = $('#txtServiceId').val();

    $('a[href="#screening"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[0],
            params: { service_id: service_id },
            scripts: [scriptUrl[0]]
        });
    });

    $('a[href="#diagnosis"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[1],
            params: { service_id: service_id },
            scripts: [scriptUrl[1]]
        });
    });

    $('a[href="#procedures"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[2],
            params: { service_id: service_id },
            scripts: [scriptUrl[2]]
        });
    });

    $('a[href="#income"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[3],
            params: { service_id: service_id },
            scripts: [scriptUrl[3]]
        });
    });

    $('a[href="#drug"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[4],
            params: { service_id: service_id },
            scripts: [scriptUrl[4]]
        });
    });

    $('a[href="#appoint"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[5],
            params: { service_id: service_id },
            scripts: [scriptUrl[5]]
        });
    });

    $('a[href="#refer"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[6],
            params: { service_id: service_id },
            scripts: [scriptUrl[6]]
        });
    });

    $('a[href="#accident"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[7],
            params: { service_id: service_id },
            scripts: [scriptUrl[7]]
        });
    });

    $('a[href="#anc"]').on('click', function() {
        setActive($(this));
        app.loadPage({
            target: target,
            url: pageUrl[8],
            params: { service_id: service_id },
            scripts: [scriptUrl[8]]
        });
    });

    var setActive = function (obj) {
        $('#collapseService a').each(function () {
            var $this = $(this);

            $this.removeClass('list-group-item-success');
        });

        $('#collapseSupportService a').each(function () {
            var $this = $(this);

            $this.removeClass('list-group-item-success');
        });

        obj.addClass('list-group-item-success');
    };

    //initial load screening page
    app.loadPage({
        target: target,
        url: pageUrl[0],
        params: { service_id: service_id },
        scripts: [scriptUrl[0]]
    });


});
