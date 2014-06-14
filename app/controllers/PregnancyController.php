<?php
/**
 * User Controller
 *
 * @author 		Satit Rianpit <rianpit@gmail.com>
 * @copyright 	2014 - 2014 by Satit Rianpit
 * @since  		1.0.0
 */

class PregnancyController extends BaseController
{

    protected $layout = 'layouts.default';

    /**
     * Constructor function
     *
     */
    public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
        $this->beforeFilter('jsondenied', ['except' => ['index', 'getDetail']]);
        $this->beforeFilter('auth', ['only' => ['index', 'getDetail']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->layout->title = 'ทะเบียนหญิงตั้งครรภ์ คลอด และดูแลหลังคลอด';
        $this->layout->content = View::make('pregnancies.index');
    }

    /**
     * Do register pregnancy
     *
     * @method              doRegister()
     * @internal {integer}  $person_id  Person id.
     *
     * @return  Response
     */
    public function doRegister()
    {
        $person_id = Input::get('person_id');
        $gravida = Input::get('gravida');

        // Check person exist
        $isPersonExist = Person::find((int) $person_id)->count();

        if ($isPersonExist) {
            // Check pregnancy is exist
            $isPregnancyExist = Pregnancy::isDuplicate($person_id, $gravida, Auth::user()->hospcode)->count();
            if ($isPregnancyExist) {
                $json = ['ok' => 0, 'error' => 'ข้อมูลซ้ำ เนื่องจากเคยลงทะเบียนเอาไว้แล้ว'];
            } else {
                $preg = new Pregnancy;
                $preg->person_id = (int) $person_id;
                $preg->gravida = (int) $gravida;
                $preg->hospcode = Auth::user()->hospcode;
                $preg->user_id = Auth::id();
                $preg->register_date = Helpers::getCurrentMySQLDate();

                try {
                    DB::transaction(function () use ($preg) {
                        $preg->save();

                        $risk = new PregnancyRisk();

                        $risk->hospcode = Auth::user()->hospcode;
                        $risk->pregnancy_id = $preg->id;
                        $risk->user_id = Auth::id();

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

    public function getList()
    {
        try {
            $rs = Pregnancy::getList(Auth::user()->hospcode)->get();

            $rows = [];

            foreach ($rs as $r) {
                $obj = new stdClass();
                $obj->id = $r->id;
                $obj->cid = $r->cid;
                $obj->fullname = $r->fname . ' ' . $r->lname;
                $obj->birthdate = Helpers::fromMySQLToThaiDate($r->birthdate);
                $obj->first_date = Helpers::fromMySQLToThaiDate($r->first_doctor_date);
                $obj->age = Helpers::countAge($r->birthdate);
                $obj->address = Utils::getShortAddress($r->home_id);
                $obj->prenatal_percent = 40;
                $obj->postnatal_percent = 80;
                $obj->labor_status = $r->labor_status;
                $obj->gravida = $r->gravida;

                $rows[] = $obj;
            }

            $json = ['ok' => 1, 'rows' => $rows];

        } catch (Exception $ex) {
            $json = ['ok' => 0, 'error' => $ex->getMessage()];
        }

        return Response::json($json);
    }

    public function getDetail($id)
    {
        // Check exist
        //$data = Pregnancy::isExist($id, Auth::user()->hospcode)->first();
        $data = Pregnancy::where('id', '=', $id)->first();
        if ($data) {
            $person = Person::where('id', '=', $data->person_id)->first();
            $typearea = PersonTypearea::where('hospcode', '=', Auth::user()->hospcode)
                ->where('cid', '=', $person->cid)
                ->first();

            $address = Utils::getShortAddress($person->home_id);
            $this->layout->title = 'ข้อมูลการตั้งครรภ์';
            $this->layout->content = View::make('pregnancies.detail', [
                'id' => $id,
                'person' => $person,
                'data' => $data,
                'typearea' => $typearea->typearea,
                'address' => $address
            ]);
        }
        else {
            App::abort(404);
        }
    }

    public function saveDetail()
    {
        $data = Input::all();
        $preg = Pregnancy::find((int) $data['id']);


        if ($data['force_export'] == 'Y') {
            //check export date
            if (!$data['force_export_date']) {
                $json = ['ok' => 0, 'error' => 'กรุณาระบุวันที่บังคับส่งออก โดยระบุเป็นวันที่ในช่วงที่ต้องการส่งออกข้อมูล'];
            } else {
                $validator = Validator::make($data, Pregnancy::$roles_export);
                if ($validator->passes()) {
                    // save
                    $preg->user_id = Auth::id();
                    $preg->provider_id = $data['provider_id'];
                    $preg->lmp = Helpers::toMySQLDate($data['lmp']);
                    $preg->edc = Helpers::toMySQLDate($data['edc']);
                    $preg->first_doctor_date = Helpers::toMySQLDate($data['first_doctor_date']);
                    $preg->labor_status = $data['labor_status'];
                    $preg->force_export = $data['force_export'];
                    $preg->force_export_date = Helpers::toMySQLDate($data['force_export_date']);
                    $preg->discharge_status = $data['discharge_status'];

                    try {
                        $preg->save();
                        $json = ['ok' => 1];
                    } catch (Exception $ex) {
                        $json = ['ok' => 0, 'error' => $ex->getMessage()];
                    }
                } else {
                    $json = ['ok' => 0, 'error' => 'เนื่องจากมีการบังคับส่งออกข้อมูล แต่ข้อมูลไม่สมบูรณ์ กรุณาตรวจสอบอีกครั้ง'];
                }
            }
        } else {
            $preg->user_id = Auth::id();
            $preg->provider_id = $data['provider_id'];
            $preg->lmp = Helpers::toMySQLDate($data['lmp']);
            $preg->edc = Helpers::toMySQLDate($data['edc']);
            $preg->first_doctor_date = Helpers::toMySQLDate($data['first_doctor_date']);
            $preg->labor_status = $data['labor_status'];
            $preg->force_export = $data['force_export'];
            $preg->force_export_date = Helpers::toMySQLDate($data['force_export_date']);
            $preg->discharge_status = $data['discharge_status'];

            try {
                $preg->save();
                $json = ['ok' => 1];
            } catch (Exception $ex) {
                $json = ['ok' => 0, 'error' => $ex->getMessage()];
            }
        }

        return Response::json($json);
    }
}