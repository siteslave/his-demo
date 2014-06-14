$(function () {
    var preg = {};

    preg.doRegister = function (params, cb) {
        app.post(actionUrl[0], params, function (err) {
            err ? cb(err) : cb(null);
        });
    };

    preg.doGetList = function (cb) {
        app.post(actionUrl[1], {}, function (err, data) {
            err ? cb(err) : cb(null, data);
        });
    };

    preg.modal = {
        showSearchPerson: function () {
            $('#modalSearchPerson').modal({
                backdrop: 'static',
                keyboard: false
            });
        },

        hideSearchPerson: function () {
            $('#modalSearchPerson').modal('hide');
        }
    };

    $('#modalSearchPerson').on('show.bs.modal', function (e) {
        $('#tblSearchPersonResult > tbody')
            .empty()
            .append('<tr><td colspan="8">กรุณาระบุคำค้นหา</td></tr>');
        $('#txtQueryPerson').val('');
    });

    preg.doSearchPerson = function (query, cb) {
        app.get(apiUrls[2], {query: query}, function (err, data) {
            err ? cb(err) : cb(null, data);
        });
    };

    preg.searchPerson = function () {

        var query = $('#txtQueryPerson').val(),
            $table = $('#tblSearchPersonResult > tbody');

        $table.empty();

        if (query) {
            preg.doSearchPerson(query, function (err, data) {
                if (err) {
                    $table.append('<tr><td colspan="8">ไม่พบรายการ</td></tr>');
                }
                else {
                    if (_.size(data.rows)) {
                        preg.setSearchResult(data.rows);
                    }
                    else {
                        $table.append('<tr><td colspan="8">ไม่พบรายการ</td></tr>');
                    }
                }
            });

        }
        else {
            app.alert('กรุณาระบุคำที่ต้องการค้นหา');
            $table.append('<tr><td colspan="8">กรุณาระบุคำค้นหา</td></tr>');
        }
    };

    preg.setSearchResult = function (data) {
        var $table = $('#tblSearchPersonResult > tbody');

        _.each(data, function (v) {
            var sex = v.sex == '1' ? 'ชาย' : 'หญิง',
                birthdate = app.toThaiDate(v.birthdate),
                age = app.countAge(v.birthdate),
                age = age.year + ' ปี ' + age.month + ' เดือน ' + age.date + ' วัน';
            $table.append(
                '<tr>' +
                '<td>' + v.cid + '</td>' +
                '<td>' + v.fullname + '</td>' +
                '<td>' + sex + '</td>' +
                '<td>' + birthdate + '</td>' +
                '<td>' + age + '</td>' +
                '<td>' + v.address + '</td>' +
                '<td><span class="text-danger">' + v.typearea + '</span></td>' +
                '<td>' +
                '<a href="#" class="btn btn-sm btn-primary" data-name="btnAcceptPerson" ' +
                'data-person_id="' + v.person_id + '" data-sex="' + v.sex + '">' +
                '<i class="fa fa-check"></i>' +
                '</a>' +
                '</td>' +
                '</tr>'
            );
        });
    };


    preg.setPregnanciesList = function(data) {
        var $table = $('#tblPregnaciesList > tbody');

        $table.empty();

        if (_.size(data.rows)) {
            var i = 1;
            _.each(data.rows, function(v) {
                //var age = _.toArray(v.age).join('-');
                var laborStatus = v.labor_status == 'Y' ? 'คลอดแล้ว' : 'ยังไม่คลอด';
                $table.append(
                    '<tr>' +
                    '<td>' + i + '</td>' +
                    '<td>' + v.cid + '</td>' +
                    '<td>' + v.fullname + '</td>' +
                    '<td class="visible-lg visible-md">' + v.age.year + '</td>' +
                    '<td>' + v.gravida + '</td>' +
                    '<td>' + laborStatus + '</td>' +
                    '<td><div class="progress">' +
                    '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="' + v.prenatal_percent + '" aria-valuemin="0" aria-valuemax="100" style="width: '+ v.prenatal_percent +'%">' + v.prenatal_percent + '%</div>' +
                    '</div></td>' +
                    '<td><div class="progress">' +
                    '<div class="progress-bar" role="progressbar" aria-valuenow="' + v.postnatal_percent + '" aria-valuemin="0" aria-valuemax="100" style="width: '+ v.postnatal_percent +'%">' + v.postnatal_percent + '%</div>' +
                    '</div></td>' +
                    '<td><div class="btn-group">' +
                    '<a href="' + base_url + '/pregnancies/detail/' + v.id + '" class="btn btn-sm btn-default"><i class="fa fa-edit"></i></a>' +
                    '<a href="#" class="btn btn-sm btn-danger"><i class="fa fa-times"></i></a>' +
                    '</div></td>' +
                    '</tr>'
                );

                i++;
            });
        } else {
            $table.append('<tr><td colspan="9">ไม่พบรายการ</td></tr>');
        }
    };

    preg.getList = function() {
        preg.doGetList(function(err, data) {
           if (err) {
               app.alert(err);

               var $table = $('#tblPregnaciesList > tbody');
               $table.empty();
               $table.append('<tr><td colspan="9">ไม่พบรายการ</td></tr>');
           } else {
                preg.setPregnanciesList(data);
           }
        });
    };

    //Turn off click event.
    $(document).off('click', 'a[data-name="btnAcceptPerson"]');

    //Turn on click event.
    $(document).on('click', 'a[data-name="btnAcceptPerson"]', function (e) {
        e.preventDefault();
        var sex = $(this).data('sex');

        if (sex == '1') {
            app.alert('เป็นเพศชายไม่สามารถลงทะเบียนได้');
        } else {
            //Confirm
            var person_id = $(this).data('person_id'),
                gravida = prompt('กรุณาระบุครรภ์ที่', '1'),
                res = confirm('คุณต้องการลงทะเบียน ใช่หรือไม่?');

            if (res) {
                var items = {};
                items.person_id = person_id;
                items.gravida = gravida;

                if (!gravida || parseInt(gravida) < 0 || parseInt(gravida) > 10) {
                    app.alert('กรุณาตรวจสอบลำดับที่ในการตั้งครรครั้งนี้ว่าถูกหรือไม่');
                } else if (!person_id) {
                    app.alert('ไม่พบรหัสบุคคล (Person ID)');
                } else {
                    preg.doRegister(items, function (err) {
                        if (err) {
                            app.alert(err);
                        } else {
                            app.alert('ลงทะเบียนเสร็จเรียบร้อยแล้ว');
                            preg.modal.hideSearchPerson();
                            preg.getList();
                        }
                    });
                }
            }
        }
    });

    $('#txtQueryPerson').on('keypress', function (e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);

        if (keycode == 13)
            preg.searchPerson();
    });

    $('#btnDoSearchPerson').on('click', function (e) {
        e.preventDefault();
        preg.searchPerson();
    });

    $('#btnShowSearchPerson').on('click', function (e) {
        e.preventDefault();
        preg.modal.showSearchPerson();
    });

    preg.getList();

});