<div class="panel-group" id="ancAccordion">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a href="#collapseAncService" data-toggle="collapse" data-parent="ancAccordion">
                    <i class="fa fa-edit"></i>
                    ข้อมูลการให้บริการฝากครรภ์
                    <i class="fa fa-chevron-circle-down pull-right"></i>
                </a>
            </h4>
        </div>
        <div class="panel-collapse collapse in" id="collapseAncService">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="">อายุครรภ์ (สัปดาห์)</label>
                            {{
                            Form::text('txtAncGa',
                                isset($anc->ga) ? $anc->ga : null,
                                ['id'=> 'txtAncGa', 'class' => 'form-control', 'data-type' => 'number'])
                            }}
                        </div>
                    </div>
                    <div class="col-md-2 col-lg-2">
                        <label for="">ครรภ์ที่</label>
                        {{ Form::select('slAncGravida', $gravidas, isset($anc->gravida) ? $anc->gravida : null, ['id' => 'slAncGravida', 'class' => 'form-control']) }}
                    </div>
                    <div class="col-md-3 col-lg-3">
                        <label for="">ผลตรวจ</label>
                        {{
                            Form::select('slAncResult', ['1' => 'ปกติ', '2' => 'ผิดปกติ'], isset($anc->anc_result) ? $anc->anc_result : null, ['id' => 'slAncResult', 'class' => 'form-control'])
                        }}
                    </div>
                </div> <!-- /row -->
                <!-- screening -->
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <legend>การคัดกรอง</legend>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <label for="">ระดับมดลูก</label>
                                {{ Form::select('slAncUterusLevel', $uterus, null, ['id' => 'slAncUterusLevel', 'class' => 'form-control']) }}
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">ท่าเด็ก</label>
                                    {{ Form::select('slAncBabyPosition', $positions, isset($anc->baby_position_id) ? $anc->baby_position_id : null, ['id' => 'slAncBabyPosition', 'class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">ส่วนนำ/การลง</label>
                                    {{ Form::select('slAncBabyLeads', $leads, isset($anc->baby_lead_id) ? $anc->baby_lead_id : null, ['id' => 'slAncBabyLeads', 'class' => 'form-control']) }}
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">เสียงหัวใจเด็ก</label>
                                    {{ Form::text('txtAncBabyHeartSound', isset($anc->baby_heart_sound) ? $anc->baby_heart_sound : null, ['id' => 'txtAncBabyHeartSound', 'class' => 'form-control']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <legend>อาการสำคัญ</legend>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncHeadache', null, isset($anc->is_headache) ? $anc->is_headache : null, ['id' => 'chkAncHeadache'] ) }} ปวดศีรษะ
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncSwollen', null, isset($anc->is_swollen) ? $anc->is_swollen : null, ['id' => 'chkAncSwollen'] ) }} บวม
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncSick', null, isset($anc->is_sick) ? $anc->is_sick : null, ['id' => 'chkAncSick'] ) }} คลื่นไส้
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncBloodshed', null, isset($anc->is_bloodshed) ? $anc->is_bloodshed : null, ['id' => 'chkAncBloodshed'] ) }} เลือดออกทางช่องคลอด
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncThyroid', null, isset($anc->is_thyroid) ? $anc->is_thyroid : null, ['id' => 'chkAncThyroid'] ) }} ต่อมไทรอยด์โต
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncCramp', null, isset($anc->is_cramp) ? $anc->is_cramp : null, ['id' => 'chkAncCramp'] ) }} ตะคริว
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncBabyFlex', null, isset($anc->is_baby_flex) ? $anc->is_baby_flex : null, ['id' => 'chkAncBabyFlex'] ) }} เด็กดิ้น
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncUrine', null, isset($anc->is_urine) ? $anc->is_urine : null, ['id' => 'chkAncUrine'] ) }} ระบบทางเดินปัสสาวะ
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncLeucorrhoea', null, isset($anc->is_leucorrhoea) ? $anc->is_leucorrhoea : null, ['id' => 'chkAncLeucorrhoea']) }} ตกขาว
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="checkbox-inline">
                                    <label>
                                        {{ Form::checkbox('chkAncHeartDisease', null, isset($anc->is_heart_disease) ? $anc->is_heart_disease : null, ['id' => 'chkAncHeartDisease']) }} โรคหัวใจ
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /screening -->
            </div>
        </div>
        <div class="panel-footer">
            <button class="btn btn-primary" id="btnAncSaveScreen">
                <i class="fa fa-save"></i> บันทึก
            </button>
            <small class="text-muted">คลิกที่ title bar เพื่อย่อ-ขยาย</small>
        </div>
    </div> <!-- panel anc -->

    <!-- Vaccine -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a href="#collapseAncVaccine" data-toggle="collapse" data-parent="ancAccordion">
                    <i class="fa fa-edit"></i>
                    การให้วัคซีน
                    <i class="fa fa-chevron-circle-down pull-right"></i>
                </a>
            </h4>
        </div>
        <div class="panel-collapse collapse in" id="collapseAncVaccine">
            <table class="table table-striped" id="tblPregnanciesOtherList">
                <thead>
                <tr>
                    <th>ชื่อวัคซีน</th>
                    <th>LOT.</th>
                    <th>วันหมดอายุ</th>
                    <th>เจ้าหน้าที่</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5">...</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <button class="btn btn-primary">
                <i class="fa fa-plus-circle"></i> เพิ่มรายการ
            </button>
            <small class="text-muted">คลิกที่ title bar เพื่อย่อ-ขยาย</small>
        </div>
    </div> <!-- panel vaccine -->

    <!-- anc history -->
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a href="#collapseAncVaccineOther" data-toggle="collapse" data-parent="ancAccordion">
                    <i class="fa fa-edit"></i>
                    ประวัติการรับบริการฝากครรภ์
                    <i class="fa fa-chevron-circle-down pull-right"></i>
                </a>
            </h4>
        </div>
        <div class=" panel-body panel-collapse collapse" id="collapseAncVaccineOther">
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <legend>ฝากครรภ์ที่นี่</legend>
                    <table class="table table-striped" id="tblPregnanciesOtherList">
                        <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>GA</th>
                            <th>ผลตรวจ</th>
                            <th>เจ้าหน้าที่</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4">...</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 col-lg-6">
                    <legend>ฝากครรภ์ที่อื่น</legend>
                    <table class="table table-striped" id="tblPregnanciesOtherList">
                        <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>GA</th>
                            <th>ผลตรวจ</th>
                            <th>เจ้าหน้าที่</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="4">...</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <small class="text-muted">คลิกที่ title bar เพื่อย่อ-ขยาย</small>
        </div>
    </div> <!-- panel anc history -->
</div>