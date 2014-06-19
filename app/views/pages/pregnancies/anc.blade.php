<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-edit"></i>
            ประวัติการรับบริการฝากครรภ์
            <i class="fa fa-question-circle pull-right"></i>
        </h4>
    </div>
    <div class="panel-body">
            <span class="text-muted">
                ประวัติการรับบริการ ในกรณีที่เป็นการบันทึกโดยหน่วยบริการ
            </span>
    </div>
    <table class="table table-striped" id="tblPregnanciesList">
        <thead>
        <tr>
            <th>วันที่</th>
            <th>ประเภท</th>
            <th>ครรภ์ที่</th>
            <th>อายุครรภ์ (สัปดาห์)</th>
            <th>หน่วยบริการ</th>
            <th>ผู้ให้บริการ</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="7">...</td>
        </tr>
        </tbody>
    </table>
    <div class="panel-footer">
        <button class="btn btn-primary" type="button" id="btnShowSearchPerson">
            <span class="fa fa-plus-circle"></span> บันทึกความครอบคลุม
        </button>
        <small class="text-muted">การเพิ่มให้บันทึกผ่านหน้าให้บริการหลัก (ยกเว้นความครอบคลุม)</small>
    </div>
</div>

<div class="panel-group" id="accordionAncOther">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a href="#collapseAncOtherService" data-toggle="collapse" data-parent="accordionAncOther">
                    <i class="fa fa-edit"></i>
                    ประวัติการรับบริการฝากครรภ์ <span class="badge">99</span>
                    <i class="fa fa-chevron-circle-down pull-right"></i>
                </a>
            </h4>
        </div>
        <div class="panel-collapse collapse" id="collapseAncOtherService">
            <div class="panel-body">
                <span class="text-muted">
                    ประวัติการรับบริการ ในกรณีที่ไปรับบริการนอกเขต
                </span>
            </div>
            <table class="table table-striped" id="tblPregnanciesOtherList">
                <thead>
                <tr>
                    <th>วันที่</th>
                    <th>ประเภท</th>
                    <th>ครรภ์ที่</th>
                    <th>อายุครรภ์ (สัปดาห์)</th>
                    <th>หน่วยบริการ</th>
                    <th>ผู้ให้บริการ</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="7">...</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer"><small class="text-muted">คลิกที่ title bar เพื่อดูรายการฝากครรภ์ที่อื่น</small></div>
    </div>
</div>