<?php

class ServiceController extends BaseController
{
    // Default layout
    protected $layout = 'layouts.default';

    /**
     * Constructor function
     *
     */
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
        $this->beforeFilter('jsondenied', [
            'except' => ['index', 'register', 'entries']
        ]);
    }

    public function index()
    {
        $clinics = DB::table('clinics')->where('is_active', 'Y')->get();

        $this->layout->title = 'การให้บริการ';
        $this->layout->content = View::make('services.index', [
            'clinics' => $clinics
        ]);
    }

    public function register()
    {
        $clinics = DB::table('clinics')->where('is_active', 'Y')->get();
        $ins = DB::table('insurances')->get();

        $this->layout->content = View::make('services.register', [
            'clinics' => $clinics,
            'ins'     => $ins
        ]);

        $this->layout->title = 'ลงทะเบียนส่งตรวจ';
    }

    public function save()
    {
        $data = Input::all();

        $validator = Validator::make($data, Visit::$roles);

        if ($validator->passes()) {
            try {
                //do save
                $s = new Service;

                $s->hospcode = Auth::user()->hospcode;
                $s->person_id = $data['pid'];
                $s->service_date = Helpers::toMySQLDate($data['service_date']);
                $s->service_time = $data['service_time'];
                $s->location = $data['service_location'];
                $s->intime = $data['service_intime'];
                $s->type_in = $data['service_type_in'];
                $s->service_place = $data['service_place'];
                $s->priority = $data['service_priority'];
                $s->clinic_id = $data['service_clinic'];
                $s->doctor_room_id = $data['service_doctor_room'];
                $s->provider_id = Auth::user()->provider_id;
                $s->user_id = Auth::id();

                //save opd screen
                $sc = new Screening();
                $sc->hospcode = Auth::user()->hospcode;
                $sc->cc = $data['service_cc'];
                $sc->pid = $data['pid'];

                //save insurance
                $ins = new ServiceInsurance();

                $ins->pid = $data['pid'];
                $ins->insurance_id = $data['service_ins'];
                $ins->insurance_code = $data['service_ins_code'];
                $ins->hospmain = $data['service_ins_main'];
                $ins->hospsub = $data['service_ins_sub'];
                $ins->start_date = Helpers::toMySQLDate($data['service_ins_start']);
                $ins->expire_date = Helpers::toMySQLDate($data['service_ins_expire']);
                $ins->hospcode = Auth::user()->hospcode;

                DB::transaction(function () use ($s, $sc, $ins) {
                    $s->save();

                    $ins->service_id = $s->id;
                    $sc->service_id = $s->id;

                    $sc->save();
                    $ins->save();
                });

                $json = ['ok' => 1];
            } catch (Exception $ex) {
                $json = ['ok' => 0, 'error' => $ex->getMessage()];
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ข้อมูลไม่ถูกต้องตามรูปแบบ กรุณาตรวจสอบ'];
        }

        return Response::json($json);
    }

    public function getList()
    {
        $start_date = Input::get('startDate');
        $end_date = Input::get('endDate');
        $callback = Input::get('callback');
        $clinic = Input::get('clinic');

        $start_date = Helpers::toMySQLDate($start_date);
        $end_date = Helpers::toMySQLDate($end_date);

        $services = DB::table('services as v')
            ->select(
                'v.id', 'v.service_date', 'v.service_time', 'p.cid',
                'p.fname', 'p.lname', 'p.birthdate', 'i.insurance_name',
                'vi.insurance_code', 'i.export_code as ins_export_code',
                'sc.cc', 'pr.fname as provider_fname', 'pr.lname as provider_lname',
                't.name as title_name'
            )
            ->leftJoin('person as p', 'p.id', '=', 'v.person_id')
            ->leftJoin('service_insurances as vi', 'vi.service_id', '=', 'v.id')
            ->leftJoin('service_screenings as sc', 'sc.service_id', '=', 'v.id')
            ->leftJoin('providers as pr', 'pr.id', '=', 'v.provider_id')
            ->leftJoin('insurances as i', 'i.id', '=', 'vi.insurance_id')
            ->leftJoin('person_title as t', 't.id', '=', 'p.title_id')
            ->whereBetween('v.service_date', [$start_date, $end_date])
            ->where('v.hospcode', Auth::user()->hospcode);

        try {
            if ($clinic == '0') {
                $rs = $services->get();
            } else {
                $rs = $services->where('v.clinic_id', $clinic)->get();
            }

            $arr = [];

            foreach ($rs as $v) {
                $obj = new stdClass();
                $obj->visit_id = $v->id;
                $obj->service_date = $v->service_date;
                $obj->service_time = $v->service_time;
                $obj->cid = $v->cid;
                $obj->fullname = $v->title_name . $v->fname . ' ' . $v->lname;
                $obj->birthdate = $v->birthdate;
                $obj->ins_code = $v->insurance_code;
                $obj->ins_name = '[' . $v->ins_export_code . '] ' . $v->insurance_name;
                $obj->cc = $v->cc;
                $obj->diag = '';
                $obj->provider_name = $v->provider_fname . ' ' . $v->provider_lname;

                $arr[] = $obj;
            }

            $json = ['ok' => 1, 'rows' => $arr];

        } catch (Exception $ex) {
            $json = ['ok' => 0, 'error' => $ex->getMessage()];
        }

        return Response::json($json)->setCallback($callback);
    }

    public function searchService()
    {
        $callback = Input::get('callback');
        $pid = Input::get('pid');

        if (isset($pid)) {

            $visits = DB::table('services as v')
                ->select(
                    'v.id', 'v.service_date', 'v.service_time', 'p.cid', 'p.fname', 'p.lname',
                    'p.birthdate', 'i.name as insurance_name', 'vi.insurance_code', 'i.export_code as ins_export_code',
                    'sc.cc', 'pr.fname as provider_fname', 'pr.lname as provider_lname', 't.name as title_name'
                )
                ->leftJoin('person as p', 'p.id', '=', 'v.pid')
                ->leftJoin('visit_insurance as vi', 'vi.visit_id', '=', 'v.id')
                ->leftJoin('screening as sc', 'sc.visit_id', '=', 'v.id')
                ->leftJoin('provider as pr', 'pr.id', '=', 'v.provider_id')
                ->leftJoin('ref_insurance as i', 'i.id', '=', 'vi.insurance_id')
                ->leftJoin('ref_person_title as t', 't.id', '=', 'p.title_id')
                ->where('v.pid', $pid)
                ->where('v.hospcode', Auth::user()->hospcode)
                ->get();

            $arr = [];

            foreach ($visits as $v) {
                $obj = new stdClass();
                $obj->visit_id = $v->id;
                $obj->service_date = $v->service_date;
                $obj->service_time = $v->service_time;
                $obj->cid = $v->cid;
                $obj->fullname = $v->title_name . $v->fname . ' ' . $v->lname;
                $obj->birthdate = $v->birthdate;
                $obj->ins_code = $v->insurance_code;
                $obj->ins_name = '[' . $v->ins_export_code . '] ' . $v->insurance_name;
                $obj->cc = $v->cc;
                $obj->diag = '';
                $obj->provider_name = $v->provider_fname . ' ' . $v->provider_lname;

                $arr[] = $obj;
            }

            $json = ['ok' => 1, 'rows' => $arr];
        } else {
            $json = ['ok' => 0, 'msg' => 'กรุณาระบุคำค้นหา'];
        }

        return Response::json($json)->setCallback($callback);
    }

    public function entries($id)
    {

        //$service = Visit::find((int) $service_id)->count();

        $service = DB::table('services as v')
            ->select(
                'v.*', 'vi.insurance_code', 'vi.hospmain', 'vi.hospsub',
                'i.export_code as insurance_export_code', 'i.insurance_name',
                'p.fname', 'p.lname'
            )
            ->leftJoin('service_insurances as vi', 'vi.service_id', '=', 'v.id')
            ->leftJoin('insurances as i', 'i.id', '=', 'vi.insurance_id')
            ->leftJoin('providers as p', 'p.id', '=', 'v.provider_id')
            ->where('v.id', $id)
            ->first();

        if ($service) {

            $person = DB::table('person as p')
                ->select(
                    'p.fname', 'p.lname', 'p.id', 'p.cid', 'p.birthdate', 'p.sex',
                    't.typearea', 'p.home_id', 'pt.name as title_name', 't.typearea'
                )
                ->leftJoin('typearea as t', 't.cid', '=', 'p.cid')
                ->leftJoin('person_title as pt', 'pt.id', '=', 'p.title_id')
                ->where('t.hospcode', Auth::user()->hospcode)
                ->where('p.id', $service->person_id)
                ->first();

            $this->layout->title = 'ข้อมูลการให้บริการ';
            $this->layout->content = View::make('services.entries', [
                'person'  => $person,
                'service' => $service
            ]);

        } else {
            return View::make('errors.404');
        }
    }

    public function saveScreening()
    {
        $data = Input::all();
        $validator = Validator::make($data, Screening::$roles);

        if ($validator->passes()) {
            $sc = Screening::find((int) $data['screen_id']);
            $service = Service::find((int) $data['service_id']);

            if ($service) {
                $service->locked = $data['locked'];
                $service->service_status_id = $data['service_status'];

                $sc->cc = $data['cc'];
                $sc->body_temp = $data['body_temp'];
                $sc->sbp = $data['sbp'];
                $sc->dbp = $data['dbp'];
                $sc->pr = $data['pr'];
                $sc->rr = $data['rr'];
                $sc->smoking = $data['smoking'];
                $sc->drinking = $data['drinking'];
                $sc->weight = $data['weight'];
                $sc->height = $data['height'];
                $sc->waist = $data['waist'];
                $sc->pe = $data['pe'];
                $sc->ill_history = $data['ill_history'];
                $sc->ill_history_detail = $data['ill_history_detail'];
                $sc->operate_history = $data['operate_history'];
                $sc->operate_history_detail = $data['operate_history_detail'];
                $sc->mind_strain = $data['mind_strain'];
                $sc->mind_work = $data['mind_work'];
                $sc->mind_family = $data['mind_family'];
                $sc->mind_other = $data['mind_other'];
                $sc->mind_other_detail = $data['mind_other_detail'];
                $sc->risk_ht = $data['risk_ht'];
                $sc->risk_dm = $data['risk_dm'];
                $sc->risk_stoke = $data['risk_stoke'];
                $sc->risk_other = $data['risk_other'];
                $sc->risk_other_detail = $data['risk_other_detail'];
                $sc->lmp = $data['lmp'];
                $sc->lmp_start = !empty($data['lmp_start']) ? Helpers::toMySQLDate($data['lmp_start']) : '';
                $sc->lmp_finished = !empty($data['lmp_finished']) ? Helpers::toMySQLDate($data['lmp_finished']) : '';
                $sc->consult_drug = $data['consult_drug'];
                $sc->consult_activity = $data['consult_activity'];
                $sc->consult_food = $data['consult_food'];
                $sc->consult_appoint = $data['consult_appoint'];
                $sc->consult_exercise = $data['consult_exercise'];
                $sc->consult_complication = $data['consult_complication'];
                $sc->consult_other = $data['consult_other'];
                $sc->consult_other_detail = $data['consult_other_detail'];

                try {
                    DB::transaction(function () use ($sc, $service) {
                        $service->save();
                        $service->touch();

                        $sc->save();
                    });

                    $json = array('ok' => 1);
                } catch (Exception $ex) {
                    $json = ['ok' => 0, 'error' => $ex->getMessage()];
                }
            } else {
                $json = ['ok' => 0, 'error' => 'ไม่พบข้อมูลการรับบริการ (อาจถูกลบก่อนทำการบันทึก)'];
            }
        } else $json = ['ok' => 0, 'error' => 'ข้อมูลไม่สมบูรณ์ กรุณาตรวจสอบ'];

        return Response::json($json);
    }

    public function saveDiagnosis()
    {
        $data = Input::all();
        $validator = Validator::make($data, ServiceDiagnosis::$roles);

        if ($validator->passes()) {
            // check has principle diagnosis
            $hasPrinciple = ServiceDiagnosis::hasPrincipleDiagnosis($data['service_id'])->count();

            if ($hasPrinciple || $data['diagnosis_type_code'] == '1') {
                # is duplicate
                $count = ServiceDiagnosis::duplicateDiagnosis($data['service_id'], $data['diagnosis_code'])->count();

                if ($count) {
                    $json = ['ok' => '0', 'error' => 'รายการซ้ำ'];
                } else {
                    # principle diag exist

                    $diag = new ServiceDiagnosis();
                    $diag->diagnosis_code = $data['diagnosis_code'];
                    $diag->diagnosis_type_code = $data['diagnosis_type_code'];
                    $diag->service_id = $data['service_id'];
                    $diag->user_id = Auth::id();
                    $diag->hospcode = Auth::user()->hospcode;

                    if ($data['diagnosis_type_code'] == '1') {
                        $principle = ServiceDiagnosis::hasPrincipleDiagnosis($data['service_id'])->count();

                        if ($principle) {
                            $json = ['ok' => 0, 'error' => 'มีการระบุ Principle diagnosis ก่อนหน้านี้แล้ว กรุณาเลือกประเภทใหม่'];
                        } else {
                            try {
                                $diag->save();
                                $json = ['ok' => 1, 'id' => $diag->id];
                            } catch (Exception $e) {
                                $json = ['ok' => 0, 'error' => $e->getMessage()];
                            }
                        }
                    } else {
                        try {
                            $diag->save();
                            $json = ['ok' => 1, 'id' => $diag->id];
                        } catch (Exception $ex) {
                            $json = ['ok' => 0, 'error' => $ex->getMessage()];
                        }
                    }

                }
            } else {
                $json = ['ok' => 0, 'error' => 'กรุณาระบุ Priniciple diagnosis ก่อนทำการบันทึกรหัสอื่นๆ'];
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ข้อมูลไม่ครบ กรุณาตรวจสอบ'];
        }

        return Response::json($json);
    }

    public function removeDiagnosis()
    {
        $diag = ServiceDiagnosis::where('id', (int) Input::get('id'));

        if ($diag) {
            try {
                $diag->delete();
                $json = ['ok' => 1];
            } catch (Exception $ex) {
                $json = ['ok' => 0, 'error' => $ex->getMessage()];
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ไม่พบรหัสที่ต้องการลบ กรุณาตรวจสอบ'];
        }

        return Response::json($json);
    }

    public function saveProcedure()
    {
        $data = Input::all();
        $validator = Validator::make($data, ServiceProcedure::$roles);

        if ($validator->passes()) {

            $procedure = new ServiceProcedure();

            $procedure->service_id = $data['service_id'];
            $procedure->hospcode = Auth::user()->hospcode;
            $procedure->user_id = Auth::id();
            $procedure->procedure_id = $data['procedure_id'];
            $procedure->procedure_position_id = $data['position_id'];
            $procedure->start_time = $data['start_time'];
            $procedure->finished_time = $data['finished_time'];
            $procedure->provider_id = $data['provider_id'];
            $procedure->price = $data['price'];

            $oldData = ServiceProcedure::hasOldData($data['service_id'], $data['procedure_id'])->first();

            if ($oldData) {
                if ($data['position_id'] == $oldData->procedure_position_id) {
                    $json = ['ok' => 0, 'error' => 'รายการซ้ำ'];
                } else {
                    try {
                        $procedure->save();
                        $json = ['ok' => 1, 'id' => $procedure->id];
                    } catch (Exception $ex) {
                        $json = ['ok' => 0, 'error' => $ex->getMessage()];
                    }
                }
            } else {
                try {
                    $procedure->save();
                    $json = ['ok' => 1, 'id' => $procedure->id];
                } catch (Exception $e) {
                    $json = ['ok' => 0, 'error' => $e->getMessage()];
                }
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ข้อมูลไม่ครบถ้วนกรุณาตรวจสอบอีกครั้ง'];
        }

        return Response::json($json);
    }

    public function getProcedureList()
    {
        $service_id = Input::get('service_id');

        $rs = ServiceProcedure::getList($service_id)->get();
        $json = ['ok' => 1, 'rows' => $rs];

        return Response::json($json);
    }

    public function removeProcedure()
    {
        $id = Input::get('id');

        $procedure = ServiceProcedure::where('id', (int) $id);

        if ($procedure) {
            try {
                $procedure->delete();
                $json = ['ok' => 1];
            } catch (Exception $e) {
                $json = ['ok' => 0, 'error' => $e->getMessage()];
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ไม่พบรหัสที่ต้องการลบ กรุณาตรวจสอบ'];
        }

        return Response::json($json);
    }

    public function saveProcedureDental()
    {
        $data = Input::all();

        $validator = Validator::make($data, ServiceProcedureDental::$roles);

        if ($validator->passes()) {
            // Check duplicate

            $procedure = new ServiceProcedureDental();

            $procedure->service_id = $data['service_id'];
            $procedure->hospcode = Auth::user()->hospcode;
            $procedure->user_id = Auth::id();
            $procedure->procedure_id = $data['procedure_id'];
            $procedure->tcount = $data['tcount'];
            $procedure->rcount = $data['rcount'];
            $procedure->dcount = $data['dcount'];
            $procedure->tcode = $data['tcode'];
            $procedure->provider_id = $data['provider_id'];
            $procedure->price = $data['price'];

            $isDuplicate = ServiceProcedureDental::isDuplicate((int) $data['service_id'], (int) $data['procedure_id'])->count();

            if ($isDuplicate) {
                $json = ['ok' => 0, 'error' => 'รายการซ้ำ'];
            } else {
                try {
                    $procedure->save();
                    $json = ['ok' => 1, 'id' => $procedure->id];
                } catch (Exception $e) {
                    $json = ['ok' => 0, 'error' => $e->getMessage()];
                }
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ข้อมูลไม่ครบถ้วนกรุณาตรวจสอบอีกครั้ง'];
        }

        return Response::json($json);
    }

    public function getProcedureDentalList()
    {
        $service_id = Input::get('service_id');
        $rs = ServiceProcedureDental::getList($service_id)->get();

        $json = ['ok' => 1, 'rows' => $rs];

        return Response::json($json);
    }

    public function removeProcedureDental()
    {
        $id = Input::get('id');

        $procedure = ServiceProcedureDental::find($id);

        if ($procedure) {
            try {
                $procedure->delete();
                $json = ['ok' => 1];
            } catch (Exception $e) {
                $json = ['ok' => 0, 'error' => $e->getMessage()];
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ไม่พบรหัสที่ต้องการลบ กรุณาตรวจสอบ'];
        }

        return Response::json($json);
    }

    public function saveIncome()
    {
        $data = Input::all();
        $validator = Validator::make($data, ServiceIncome::$roles);

        if ($validator->passes()) {
            if (empty($data['id'])) {
                // Check duplicate

                $income = new ServiceIncome();

                $income->service_id = $data['service_id'];
                $income->hospcode = Auth::user()->hospcode;
                $income->provider_id = Auth::user()->provider_id;
                $income->user_id = Auth::id();
                $income->income_id = $data['income_id'];
                $income->price = $data['price'];
                $income->qty = $data['qty'];

                $isDuplicate = ServiceIncome::isDuplicate($data['service_id'], $data['income_id'])->count();

                if ($isDuplicate) {
                    $json = ['ok' => 0, 'error' => 'รายการซ้ำ'];
                } else {
                    try {
                        $income->save();
                        $json = ['ok' => 1];
                    } catch (Exception $e) {
                        $json = ['ok' => 0, 'error' => $e->getMessage()];
                    }
                }
            } else {
                $income = ServiceIncome::find($data['id']);

                $income->price = $data['price'];
                $income->qty = $data['qty'];

                try {
                    $income->save();
                    $json = ['ok' => 1];
                } catch (Exception $e) {
                    $json = ['ok' => 0, 'error' => $e->getMessage()];
                }
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ข้อมูลไม่ครบถ้วนกรุณาตรวจสอบอีกครั้ง'];
        }

        return Response::json($json);
    }

    public function getIncomeList()
    {
        $service_id = Input::get('service_id');

        $rs = ServiceIncome::getList($service_id)->get();

        $json = ['ok' => 1, 'rows' => $rs];

        return Response::json($json);
    }

    public function removeIncome()
    {
        $id = Input::get('id');

        $income = ServiceIncome::find((int) $id);

        if ($income) {
            try {
                $income->delete();
                $json = ['ok' => 1];
            } catch (Exception $e) {
                $json = ['ok' => 0, 'error' => $e->getMessage()];
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ไม่พบรายการที่ต้องการลบ'];
        }

        return Response::json($json);
    }

    public function saveDrug()
    {
        $data = Input::all();
        $validator = Validator::make($data, ServiceDrug::$roles);

        if ($validator->passes()) {

            if (empty($data['id'])) {
                $isExist = ServiceDrug::isExist($data['service_id'], $data['drug_id'])->count();

                if ($isExist) {
                    $json = ['ok' => 0, 'error' => 'รายการนี้ซ้ำ'];
                } else {
                    $drug = new ServiceDrug();

                    $drug->service_id = $data['service_id'];
                    $drug->hospcode = Auth::user()->hospcode;
                    $drug->provider_id = Auth::user()->provider_id;
                    $drug->user_id = Auth::id();
                    $drug->drug_id = $data['drug_id'];
                    $drug->usage_id = $data['usage_id'];
                    $drug->price = $data['price'];
                    $drug->qty = $data['qty'];

                    try {
                        $drug->save();
                        $json = ['ok' => 1, 'id' => $drug->id];
                    } catch (Exception $ex) {
                        $json = ['ok' => 0, 'error' => $ex->getMessage()];
                    }
                }
            } else {
                $drug = ServiceDrug::find((int) $data['id']);

                $drug->user_id = Auth::id();
                $drug->drug_id = $data['drug_id'];
                $drug->usage_id = $data['usage_id'];
                $drug->price = $data['price'];
                $drug->qty = $data['qty'];

                try {
                    $drug->save();
                    $json = ['ok' => 1, 'id' => $drug->id];
                } catch (Exception $ex) {
                    $json = ['ok' => 0, 'error' => $ex->getMessage()];
                }
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ข้อมูลไม่สมบูรณ์ กรุณาตรวจสอบ'];
        }

        return Response::json($json);
    }

    public function getDrugList()
    {
        $service_id = Input::get('service_id');
        $rs = ServiceDrug::getList($service_id)->get();
        $json = ['ok' => 1, 'rows' => $rs];

        return Response::json($json);
    }

    public function removeDrug()
    {
        $id = Input::get('id');

        $drug = ServiceDrug::find((int) $id);

        if ($drug) {
            try {
                $drug->delete();
                $json = ['ok' => 1];
            } catch (Exception $ex) {
                $json = ['ok' => 0, 'error' => $ex->getMessage()];
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ไม่พบรายการที่ต้องการลบ'];
        }

        return Response::json($json);
    }

    public function clearDrug()
    {
        $service_id = Input::get('service_id');

        $drug = ServiceDrug::where('service_id', '=', $service_id);

        try {
            $drug->delete();
            $json = ['ok' => 1];
        } catch (Exception $ex) {
            $json = ['ok' => 0, 'error' => $ex->getMessage()];
        }

        return Response::json($json);

    }

    public function saveAppoint()
    {
        $data = Input::all();
        $validator = Validator::make($data, ServiceAppointment::$roles);

        if ($validator->passes()) {
            //is duplicate
            $isDuplicate = ServiceAppointment::isDuplicate((int) $data['service_id'], (int) $data['appoint_id'], $data['appoint_date'])
                ->count();

            if ($isDuplicate) {
                $json = ['ok' => 0, 'msg' => 'ข้อมูลซำ้ซ้อน กรุณาตรวจสอบ'];
            } else {
                $ap = new ServiceAppointment();

                $ap->service_id = $data['service_id'];
                $ap->hospcode = Auth::user()->hospcode;
                $ap->user_id = Auth::id();
                $ap->provider_id = $data['provider_id'];
                $ap->appoint_type_id = $data['appoint_id'];
                $ap->appoint_date = Helpers::toMySQLDate($data['appoint_date']);
                $ap->appoint_time = $data['appoint_time'];
                $ap->clinic_id = $data['clinic_id'];

                try {
                    $ap->save();
                    $json = ['ok' => 1];
                } catch (Exception $ex) {
                    $json = ['ok' => 0, 'error' => $ex->getMessage()];
                }
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ข้อมูลไม่สมบูรณ์ กรุณาตรวจสอบ'];
        }

        return Response::json($json);

    }

    public function getAppointList()
    {
        $service_id = Input::get('service_id');

        $rs = ServiceAppointment::getList($service_id)->get();

        $json = ['ok' => 1, 'rows' => $rs];

        return Response::json($json);
    }

    public function removeAppoint()
    {
        $id = Input::get('id');

        $appoint = ServiceAppointment::find($id);

        try {
            $appoint->delete();
            $json = ['ok' => 1];
        } catch (Exception $ex) {
            $json = ['ok' => 0, 'error' => $ex->getMessage()];
        }

        return Response::json($json);
    }

    public function saveReferOut()
    {
        $data = Input::all();

        $validator = Validator::make($data, ServiceReferOut::$roles);

        if ($validator->passes()) {
            if (empty($data['refer_id'])) {
                try {
                    $refer = new ServiceReferOut();

                    $refer->service_id = $data['service_id'];
                    $refer->hospcode = Auth::user()->hospcode;
                    $refer->user_id = Auth::id();
                    $refer->provider_id = $data['provider_id'];
                    $refer->refer_date = Helpers::toMySQLDate($data['refer_date']);
                    $refer->cause_id = $data['cause_id'];
                    $refer->diagnosis_code = $data['diag_code'];
                    $refer->to_hospital = $data['to_hospital'];
                    $refer->expire_date = Helpers::toMySQLDate($data['expire_date']);
                    $refer->description = $data['description'];

                    $refer->save();
                    $json = ['ok' => 1, 'id' => $refer->id];
                } catch (Exception $ex) {
                    $json = ['ok' => 0, 'error' => $ex->getMessage()];
                }
            } else {
                try {
                    $refer = ServiceReferOut::find((int) $data['refer_id']);

                    $refer->service_id = $data['service_id'];
                    $refer->hospcode = Auth::user()->hospcode;
                    $refer->user_id = Auth::id();
                    $refer->provider_id = $data['provider_id'];
                    $refer->refer_date = Helpers::toMySQLDate($data['refer_date']);
                    $refer->cause_id = $data['cause_id'];
                    $refer->diagnosis_code = $data['diag_code'];
                    $refer->to_hospital = $data['to_hospital'];
                    $refer->expire_date = Helpers::toMySQLDate($data['expire_date']);
                    $refer->description = $data['description'];

                    $refer->save();

                    $json = ['ok' => 1, 'id' => $refer->id];
                } catch (Exception $ex) {
                    $json = ['ok' => 0, 'error' => $ex->getMessage()];
                }
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ข้อมูลไม่ถูกต้อง หรือ ไม่สมบูรณ์ กรุณาตรวจสอบ'];
        }

        return Response::json($json);
    }

    public function removeReferOut()
    {
        $id = Input::get('id');
        $refer = ServiceReferOut::find($id);

        if ($refer) {
            try {
                $refer->delete();
                $json = ['ok' => 1];
            } catch (Exception $ex) {
                $json = ['ok' => 0, 'error' => $ex->getMessage()];
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ไม่พบรายการที่ต้องการลบ'];
        }

        return Response::json($json);
    }

    public function saveAccident()
    {
        $data = Input::all();
        $validator = Validator::make($data, ServiceAccident::$roles);

        if ($validator->passes()) {
            if (empty($data['id'])) {
                $acc = new ServiceAccident();

                $acc->hospcode = Auth::user()->hospcode;
                $acc->user_id = Auth::id();
                $acc->service_id = $data['service_id'];
                $acc->accident_date = Helpers::toMySQLDate($data['accident_date']);
                $acc->accident_time = $data['accident_time'];
                $acc->accident_type_id = $data['accident_type_id'];
                $acc->accident_place_id = $data['accident_place_id'];
                $acc->accident_type_in_id = $data['accident_type_in_id'];
                $acc->traffic = $data['traffic'];
                $acc->accident_vehicle_id = $data['accident_vehicle_id'];
                $acc->alcohol = $data['alcohol'];
                $acc->nacrotic_drug = $data['nacrotic_drug'];
                $acc->belt = $data['blet'];
                $acc->helmet = $data['helmet'];
                $acc->airway = $data['airway'];
                $acc->stop_bleed = $data['stop_bleed'];
                $acc->splint = $data['splint'];
                $acc->fluid = $data['fluid'];
                $acc->urgency = $data['urgency'];
                $acc->coma_eye = $data['coma_eye'];
                $acc->coma_speak = $data['coma_speak'];
                $acc->coma_movement = $data['coma_movement'];

                try {
                    $acc->save();
                    $json = ['ok' => 1, 'id' => $acc->id];
                } catch (Exception $ex) {
                    $json = ['ok' => 0, 'error' => $ex->getMessage()];
                }
            } else {
                $acc = ServiceAccident::find($data['id']);

                //$acc->hospcode = Session::get('hospcode');
                $acc->user_id = Auth::id();
                //$acc->visit_id          = $data['visit_id'];
                $acc->accident_date = Helpers::toMySQLDate($data['accident_date']);
                $acc->accident_time = $data['accident_time'];
                $acc->accident_type_id = $data['accident_type_id'];
                $acc->accident_place_id = $data['accident_place_id'];
                $acc->accident_type_in_id = $data['accident_type_in_id'];
                $acc->traffic = $data['traffic'];
                $acc->accident_vehicle_id = $data['accident_vehicle_id'];
                $acc->alcohol = $data['alcohol'];
                $acc->nacrotic_drug = $data['nacrotic_drug'];
                $acc->belt = $data['blet'];
                $acc->helmet = $data['helmet'];
                $acc->airway = $data['airway'];
                $acc->stop_bleed = $data['stop_bleed'];
                $acc->splint = $data['splint'];
                $acc->fluid = $data['fluid'];
                $acc->urgency = $data['urgency'];
                $acc->coma_eye = $data['coma_eye'];
                $acc->coma_speak = $data['coma_speak'];
                $acc->coma_movement = $data['coma_movement'];

                try {
                    $acc->save();
                    $json = ['ok' => 1, 'id' => $acc->id];
                } catch (Exception $ex) {
                    $json = ['ok' => 0, 'error' => $ex->getMessage()];
                }
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ข้อมูลไม่ถูกรูปแบบ หรือ ไม่สมบูรณ์ กรุณาตรวจสอบ'];
        }

        return Response::json($json);
    }

    public function removeAccident()
    {
        $id = Input::get('id');
        $acc = ServiceAccident::find($id);

        if ($acc) {
            try {
                $acc->delete();
                $json = ['ok' => 1];
            } catch (Exception $ex) {
                $json = ['ok' => 0, 'error' => $ex->getMessage()];
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ไม่พบรายการที่ต้องการลบ'];
        }

        return Response::json($json);
    }

}
