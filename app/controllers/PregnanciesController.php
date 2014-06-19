<?php
/**
 * User Controller
 *
 * @author 		Satit Rianpit <rianpit@gmail.com>
 * @copyright 	2014 - 2014 by Satit Rianpit
 * @since  		1.0.0
 */

class PregnanciesController extends BaseController
{

    protected $layout = 'layouts.default';

    /**
     * Constructor function
     *
     */
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
        $this->beforeFilter('jsondenied', ['except' => ['getIndex', 'getDetail']]);
        $this->beforeFilter('auth', ['only' => ['getIndex', 'getDetail']]);
    }

    /**
     * Display a listing of the resource.
     *
     * GET    /pregnancies
     *
     * @return Response
     */
    public function getIndex()
    {
        $this->layout->title = 'ทะเบียนหญิงตั้งครรภ์ คลอด และดูแลหลังคลอด';
        $this->layout->content = View::make('pregnancies.index');
    }

    /**
     * Do register pregnancy
     *
     * POST    /pregnancies/register
     *
     * @internal int $person_id  Person id
     * @return  Response::json
     */
    public function postRegister()
    {
        $person_id = Input::get('person_id');
        $gravida = Input::get('gravida');

        // Check person exist
        $isPersonExist = Person::find((int) $person_id)->count();

        if ($isPersonExist) {
            // Check pregnancy is exist
            $isPregnancyExist = Pregnancy::isDuplicate(
                                $person_id, $gravida,
                                Auth::user()->hospcode)
                                ->count();

            if ($isPregnancyExist) {
                $json = ['ok' => 0, 'error' => 'ข้อมูลซ้ำ เนื่องจากเคยลงทะเบียนเอาไว้แล้ว'];
            } else {
                $preg                = new Pregnancy;
                $preg->person_id     = (int) $person_id;
                $preg->gravida       = (int) $gravida;
                $preg->hospcode      = Auth::user()->hospcode;
                $preg->user_id       = Auth::id();
                $preg->register_date = Helpers::getCurrentMySQLDate();

                try {
                    DB::transaction(function () use ($preg) {
                        $preg->save();

                        $risk               = new PregnancyRisk();

                        $risk->hospcode     = Auth::user()->hospcode;
                        $risk->pregnancy_id = $preg->id;
                        $risk->user_id      = Auth::id();

                        $risk->save();
                    });
                    $json = ['ok' => 1];
                } catch (Exception $ex) {
                    $json = ['ok' => 0, 'error' => $ex->getMessage()];
                }
            }
        } else {
            $json = ['ok' => 0, 'error' => 'ไม่พบข้อมูลบุคคลที่ต้องการลงทะเบียนในฐานข้อมูล'];
        }

        // Return json
        return Response::json($json);
    }

    /**
     * Get pregnancy list
     *
     * GET    /pregnancies/list
     *
     * @return Response::json
     */
    public function getList()
    {
        $callback = Input::get('callback');
        try {
            $rs = Pregnancy::getList(Auth::user()->hospcode)->get();

            $rows = [];

            foreach ($rs as $r) {
                $obj = new stdClass();
                $obj->id                = $r->id;
                $obj->cid               = $r->cid;
                $obj->fullname          = $r->fname . ' ' . $r->lname;
                $obj->birthdate         = Helpers::fromMySQLToThaiDate($r->birthdate);
                $obj->first_date        = Helpers::fromMySQLToThaiDate($r->first_doctor_date);
                $obj->age               = Helpers::countAge($r->birthdate);
                $obj->address           = Utils::getShortAddress($r->home_id);
                $obj->prenatal_percent  = 40;
                $obj->postnatal_percent = 80;
                $obj->labor_status      = $r->labor_status;
                $obj->gravida           = $r->gravida;

                $rows[] = $obj;
            }

            $json = ['ok' => 1, 'rows' => $rows];

        } catch (Exception $ex) {
            $json = ['ok' => 0, 'error' => $ex->getMessage()];
        }

        return Response::json($json)->setCallback($callback);
    }

    /**
     * Get pregnancy detail
     *
     * GET    /pregnancies/detail
     *
     * @param  int    $id    Pregnancy id
     * @return View
     */
    public function getDetail($id = null)
    {
        $data = Pregnancy::where('id', '=', $id)
            ->where('hospcode', '=', Auth::user()->hospcode)
            ->first();

        if ($data) {
            $person   = Person::where('id', '=', $data->person_id)->first();
            $typearea = PersonTypearea::where('hospcode', '=', Auth::user()->hospcode)
                ->where('cid', '=', $person->cid)
                ->first();

            $address               = Utils::getShortAddress($person->home_id);
            $this->layout->title   = 'ข้อมูลการตั้งครรภ์';
            $this->layout->content = View::make('pregnancies.detail', [
                'id'       => $id,
                'person'   => $person,
                'data'     => $data,
                'typearea' => $typearea->typearea,
                'address'  => $address
            ]);
        }
        else {
            App::abort(404);
        }
    }

    /**
     * Save pregnancy detail
     *
     * POST    /pregnancies/detail
     *
     * @param  Input
     * @return Response::json
     */
    public function postDetail()
    {
        if (Request::ajax()) {
            $data = Input::all();
            $preg = Pregnancy::find((int) $data['id']);
            $riskOld = PregnancyRisk::where('pregnancy_id', '=', $data['id'])->first();

            if ($riskOld) {
                $risk = PregnancyRisk::find($riskOld->id);
            } else {
                $risk = new PregnancyRisk();
                $risk->pregnancy_id = $data['id'];
                $risk->hospcode = Auth::user()->hospcode;
            }

            if ($data['force_export'] == 'Y') {
                //check export date
                if (!$data['force_export_date']) {
                    $json = [
                        'ok'    => 0,
                        'error' => 'กรุณาระบุวันที่บังคับส่งออก โดยระบุเป็นวันที่ในช่วงที่ต้องการส่งออกข้อมูล'
                    ];
                } else {
                    $validator = Validator::make($data, Pregnancy::$roles_export);
                    if ($validator->passes()) {
                        // save
                        $preg->user_id           = Auth::id();
                        $preg->provider_id       = $data['provider_id'];
                        $preg->lmp               = Helpers::toMySQLDate($data['lmp']);
                        $preg->edc               = Helpers::toMySQLDate($data['edc']);
                        $preg->first_doctor_date = Helpers::toMySQLDate($data['first_doctor_date']);
                        $preg->labor_status      = $data['labor_status'];
                        $preg->force_export      = $data['force_export'];
                        $preg->force_export_date = Helpers::toMySQLDate($data['force_export_date']);
                        $preg->discharge_status  = $data['discharge_status'];

                        $risk->risk1         = $data['risk1'];
                        $risk->risk2         = $data['risk2'];
                        $risk->risk3         = $data['risk3'];
                        $risk->risk4         = $data['risk4'];
                        $risk->risk5         = $data['risk5'];
                        $risk->risk6         = $data['risk6'];
                        $risk->risk7         = $data['risk7'];
                        $risk->risk8         = $data['risk8'];
                        $risk->risk9         = $data['risk9'];
                        $risk->risk10        = $data['risk10'];
                        $risk->risk11        = $data['risk11'];
                        $risk->risk12        = $data['risk12'];
                        $risk->risk13        = $data['risk13'];
                        $risk->risk14        = $data['risk14'];
                        $risk->risk15        = $data['risk15'];
                        $risk->risk16        = $data['risk16'];
                        $risk->risk17        = $data['risk17'];
                        $risk->risk18        = $data['risk18'];
                        $risk->risk18_detail = $data['risk18_detail'];
                        $risk->user_id       = Auth::id();

                        try {
                            DB::transaction(function () use ($preg, $risk) {
                                $preg->save();
                                $risk->save();
                            });
                            $json = ['ok' => 1];
                        } catch (Exception $ex) {
                            $json = ['ok' => 0, 'error' => $ex->getMessage()];
                        }
                    } else {
                        $json = [
                            'ok' => 0,
                            'error' => 'เนื่องจากมีการบังคับส่งออกข้อมูล แต่ข้อมูลไม่สมบูรณ์ กรุณาตรวจสอบอีกครั้ง'
                        ];
                    }
                }
            } else {
                $preg->user_id           = Auth::id();
                $preg->provider_id       = $data['provider_id'];
                $preg->lmp               = Helpers::toMySQLDate($data['lmp']);
                $preg->edc               = Helpers::toMySQLDate($data['edc']);
                $preg->first_doctor_date = Helpers::toMySQLDate($data['first_doctor_date']);
                $preg->labor_status      = $data['labor_status'];
                $preg->force_export      = $data['force_export'];
                $preg->force_export_date = Helpers::toMySQLDate($data['force_export_date']);
                $preg->discharge_status  = $data['discharge_status'];

                $risk->risk1         = $data['risk1'];
                $risk->risk2         = $data['risk2'];
                $risk->risk3         = $data['risk3'];
                $risk->risk4         = $data['risk4'];
                $risk->risk5         = $data['risk5'];
                $risk->risk6         = $data['risk6'];
                $risk->risk7         = $data['risk7'];
                $risk->risk8         = $data['risk8'];
                $risk->risk9         = $data['risk9'];
                $risk->risk10        = $data['risk10'];
                $risk->risk11        = $data['risk11'];
                $risk->risk12        = $data['risk12'];
                $risk->risk13        = $data['risk13'];
                $risk->risk14        = $data['risk14'];
                $risk->risk15        = $data['risk15'];
                $risk->risk16        = $data['risk16'];
                $risk->risk17        = $data['risk17'];
                $risk->risk18        = $data['risk18'];
                $risk->risk18_detail = $data['risk18_detail'];
                $risk->user_id       = Auth::id();

                try {
                    DB::transaction(function () use ($preg, $risk) {
                        $preg->save();
                        $risk->save();
                    });

                    $json = ['ok' => 1];

                } catch (Exception $ex) {
                    $json = ['ok' => 0, 'error' => $ex->getMessage()];
                }
            }
        } else {
            $json = ['ok' => 0, 'error' => 'Not ajax request.'];
        }

        return Response::json($json);
    }
}
