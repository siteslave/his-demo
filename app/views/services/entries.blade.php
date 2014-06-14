@extends('layouts.default')

<?php $age = Helpers::countAge($person->birthdate) ?>

@section('content')
<ol class="breadcrumb">
    <li><a href="/">หน้าหลัก</a></li>
    <li><a href="{{ URL::route('services.index') }}">การให้บริการ</a></li>
    <li class="active">ข้อมูลการให้บริการ</li>
</ol>

<input type="hidden" id="txtServiceId" value="{{ $service->id }}"/>

<div class="row">
    <div class="col-md-3 col-lg-3" id="divMenu">
        <div class="panel-group" id="accordion">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseInfo">
                            <i class="fa fa-th-large"></i> ข้อมูลทั่วไป
                        </a>
                    </h4>
                </div>
                <div class="panel-collapse collapse in" id="collapseInfo">
                    <table class="table">
                        <tbody>
                        <tr>
                            <td>​CID</td>
                            <td><span id="txtInfoCID" class="text-muted">{{ $person->cid }}</span></td>
                        </tr>
                        <tr>
                            <td>ชื่อ-สกลุ</td>
                            <td><span id="txtInfoFullname" class="text-muted">
                            {{ $person->title_name }}{{ $person->fname }} {{ $person->lname }}
                        </span></td>
                        </tr>
                        <tr>
                            <td>อายุ</td>
                            <td><span id="txtInfoAge" class="text-muted">
                            {{ $age->year }} ปี {{ $age->month }} เดือน {{ $age->day }} วัน
                        </span></td>
                        </tr>
                        <tr>
                            <td>สิทธิ</td>
                            <td><span id="txtInfoAddress" class="text-muted">[{{ $service->insurance_export_code }}] {{ $service->insurance_name }}</span></td>
                        </tr>
                        <tr>
                            <td>แพทย์</td>
                            <td><span id="txtInfoAddress" class="text-muted">{{ $service->fname }} {{ $service->lname }}</span></td>
                        </tr>
                        <tr>
                            <td>Typearea</td>
                            <td><span id="txtInfoTypearea" class="text-danger">{{ $person->typearea }}</span></td>
                        </tr>
                        <tr>
                            <td>Created</td>
                            <td><span id="txtInfoCreated" class="text-muted">{{ $service->created_at }}</span></td>
                        </tr>
                        <tr>
                            <td>Updated</td>
                            <td><span id="txtInfoLastUpdated" class="text-muted">{{ $service->updated_at }}</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <a class="btn btn-primary" href="/services/edit/{{ $service->id }}">
                        <i class="fa fa-edit"></i> แก้ไข
                    </a>
                    <a class="btn btn-danger" href="javascript:void(0);" id="btnRemoveService">
                        <i class="fa fa-trash-o"></i> ลบรายการ
                    </a>
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseService">
                            <i class="fa fa-th-large"></i> การให้บริการ
                        </a>
                    </h4>
                </div>
                <div class="panel-collapse collapse" id="collapseService">
                    <div class="list-group">
                        <a class="list-group-item" href="#screening">
                            <i class="fa fa-fw fa-suitcase"></i> ข้อมูลคัดกรองทั่วไป
                        </a>
                        <a class="list-group-item" href="#diagnosis">
                            <i class="fa fa-fw fa-suitcase"></i> การวินิจฉัยโรค
                        </a>
                        <a class="list-group-item" href="#procedures">
                            <i class="fa fa-fw fa-medkit"></i> การให้หัตถการ
                        </a>
                        <a class="list-group-item" href="#drug">
                            <i class="fa fa-fw fa-medkit"></i> จ่ายยา
                        </a>
                        <a class="list-group-item" href="#income">
                            <i class="fa fa-fw fa-medkit"></i> ค่าใช้จ่ายอื่นๆ
                        </a>
                        <a class="list-group-item" href="#appoint">
                            <i class="fa fa-fw fa-medkit"></i> ลงทะเบียนนัด
                        </a>
                        <a class="list-group-item" href="#refer">
                            <i class="fa fa-fw fa-medkit"></i> บันทึกการส่งต่อ
                        </a>
                        <a class="list-group-item" href="#accident">
                            <i class="fa fa-fw fa-medkit"></i> บันทึกการเกิดอุบัติเหตุ
                        </a>
                    </div>
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="#collapseSupportService" data-toggle="collapse" data-parent="#accordion">
                            <i class="fa fa-th-large"></i> งานส่งเสริมสุขภาพ
                        </a>
                    </h4>

                </div>
                <div class="panel-collapse collapse" id="collapseSupportService">
                    <div class="list-group no-border">
                        <a class="list-group-item" href="#"><i class="fa fa-fw fa-medkit"></i> วางแผนครอบครัว (FP)</a>
                        <a class="list-group-item" href="#"><i class="fa fa-fw fa-medkit"></i> บันทึกโภชนาการ (Nutrition)</a>
                        <a class="list-group-item" href="#"><i class="fa fa-fw fa-medkit"></i> บันทึกการให้วัคซีน (EPI)</a>
                        <a class="list-group-item" href="#"><i class="fa fa-fw fa-medkit"></i> บันทึกการฝากครรภ์ (ANC)</a>
                        <a class="list-group-item" href="#"><i class="fa fa-fw fa-medkit"></i> เยี่ยมหลังคลอด (มารดา)</a>
                        <a class="list-group-item" href="#"><i class="fa fa-fw fa-medkit"></i> เยี่ยมหลังคลอด (เด็ก)</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-md-9 col-lg-9" id="content" class="fixed-header"></div>
</div>
@stop

@section('modal')

@stop

@section('urls')
<script>
    var serviceUrl = [
            "{{ action('ServiceController@save') }}", //0
            "{{ action('ServiceController@getList') }} ", //1
            "{{ action('ServiceController@searchVisit') }} ", //2
            "{{ action('ServiceController@saveScreening') }}", //3
            "{{ action('ServiceController@saveDiagnosis') }}", //4
            "{{ action('ServiceController@removeDiagnosis') }}", //5
            "{{ action('ServiceController@saveProcedure') }}", //6
            "{{ action('ServiceController@getProcedureList') }}", //7
            "{{ action('ServiceController@removeProcedure') }}", //8
            "{{ action('ServiceController@saveProcedureDental') }}", //9
            "{{ action('ServiceController@getProcedureDentalList') }}", //10
            "{{ action('ServiceController@removeProcedureDental') }}", //11
            "{{ action('ServiceController@saveIncome') }}", //12
            "{{ action('ServiceController@getIncomeList') }}", //13
            "{{ action('ServiceController@removeIncome') }}", //14
            "{{ action('ServiceController@saveDrug') }}", //15
            "{{ action('ServiceController@getDrugList') }}", //16
            "{{ action('ServiceController@removeDrug') }}", //17
            "{{ action('ServiceController@clearDrug') }}", //18
            "{{ action('ServiceController@saveAppoint') }}", //19
            "{{ action('ServiceController@getAppointList') }}", //20
            "{{ action('ServiceController@removeAppoint') }}", //21
            "{{ action('ServiceController@saveReferOut') }}", //22
            "{{ action('ServiceController@removeReferOut') }}", //23
            "{{ action('ServiceController@saveAccident') }}", //24
            "{{ action('ServiceController@removeAccident') }}" //25
        ],
        pageUrl = [
            "{{ action('PageController@serviceScreening') }}", //0
            "{{ action('PageController@serviceDiagnosis') }}", //1
            "{{ action('PageController@serviceProcedure') }}", //2
            "{{ action('PageController@serviceIncome') }}", //3
            "{{ action('PageController@serviceDrug') }}", //4
            "{{ action('PageController@serviceAppointment') }}", //5
            "{{ action('PageController@serviceReferOut') }}", //6
            "{{ action('PageController@serviceAccident') }}" //7
        ],
        scriptUrl = [
            "{{ asset('assets/app/js/services.screening.js') }}", //0
            "{{ asset('assets/app/js/services.diagnosis.js') }}", //1
            "{{ asset('assets/app/js/services.procedure.js') }}", //2
            "{{ asset('assets/app/js/services.income.js') }}", //3
            "{{ asset('assets/app/js/services.drug.js') }}", //4
            "{{ asset('assets/app/js/services.appointment.js') }}", //5
            "{{ asset('assets/app/js/services.referout.js') }}", //6
            "{{ asset('assets/app/js/services.accident.js') }}" //7
        ];
</script>
@stop

@section('scripts')
{{ HTML::script(asset('assets/app/js/services.entries.js')); }}
@stop
