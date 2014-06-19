$(function() {

    var anc = {};

    anc.doSaveScreen = function (items, cb) {
        app.post(serviceUrls[0], items, function (err) {
            err ? cb(err) : cb(null);
        });
    };

    $('#btnAncSaveScreen').on('click', function(e) {
        e.preventDefault();

        var items = {};

        items.ga = $('#txtAncGa').val();
        items.gravida = $('#slAncGravida').val();
        items.anc_result = $('#slAncResult').val();
        items.uterus_level_id = $('#slAncUterusLevel').val();
        items.baby_position_id = $('#slAncBabyPosition').val();
        items.baby_lead_id = $('#slAncBabyLeads').val();
        items.baby_heart_sound = $('#txtAncBabyHeartSound').val();
        items.is_headache = $('#chkAncHeadache').val();
        items.is_swollen = $('#chkAncSwollen').val();
        items.is_sick = $('#chkAncSick').val();
        items.is_bloodshed = $('#chkAncBloodshed').val();
        items.is_thyroid = $('#chkAncThyroid').val();
        items.is_cramp = $('#chkAncCramp').val();
        items.is_baby_flex = $('#chkAncBabyFlex').val();
        items.is_urine = $('#chkAncUrine').val();
        items.is_leucorrhoea = $('#chkAncLeucorrhoea').val();
        items.is_heart_disease = $('#chkAncHeartDisease').val();

        items.service_id = $('#txtServiceId').val();

        if (!items.ga) {
            app.alert('กรุณาระบุอายุของครรภ์');
        } else if (!items.gravida) {
            app.alert('กรุณาระบุลำดับที่ของครรภ์ที่ตรวจในครั้งนี้');
        } else if (!items.anc_result) {
            app.alert('กรุณาระบุผลของการตรวจครรภ์');
        } else {
            anc.doSaveScreen(items, function (err) {
                if (err) app.alert(err);
                else app.alert('บันทึกรายการเสร็จเรียบร้อยแล้ว');
            })
        }
    });
});
