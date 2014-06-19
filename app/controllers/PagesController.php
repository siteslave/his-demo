<?php

class PagesController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('denied');
    }

    /**
     * Service screening
     *
     * POST    /pages/service-screenings
     *
     * @internal int    $service_id    Service id
     * @return   View
     */
    public function postServiceScreenings()
    {

        $service_id = Input::get('service_id');
        $services = Service::where('id', (int)$service_id)->first();
        $screen = $services->screening()->first();
        $status = ServiceStatus::all();

        return View::make('pages.services.screening')
            ->with('status', $status)
            ->with('services', $services)
            ->with('screen', $screen);

    }

    /**
     * Service diagnosis
     *
     * POST    /pages/service-diagnosis
     *
     * @internal int    $service_id    Service id
     * @return   View
     */
    public function postServiceDiagnosis()
    {
        $service_id = Input::get('service_id');

        $diag       = ServiceDiagnosis::getList($service_id)->get();
        $diagtype   = DiagnosisType::getActive()->get(['code', 'name']);

        return View::make('pages.services.diagnosis')
            ->with('diag', $diag)
            ->with('diagtype', $diagtype);
    }

    /**
     * Service procedures
     *
     * POST    /pages/service-procedures
     *
     * @return   view
     */
    public function postServiceProcedures()
    {
        $providers = Provider::getActive()->get(['id', 'fname', 'lname']);

        return View::make('pages.services.procedure')
            ->with('providers', $providers);
    }

    /**
     * Service income
     *
     * POST    /pages/service-incomes
     *
     * @return   view
     */
    public function postServiceIncomes()
    {
        return View::make('pages.services.income');
    }

    /**
     * Service drug
     *
     * POST    /pages/service-drugs
     *
     * @return   view
     */
    public function postServiceDrugs()
    {
        return View::make('pages.services.drug');
    }

    /**
     * Service appointments
     *
     * POST    /pages/service-appoint
     *
     * @return   View
     */
    public function postServiceAppoint()
    {
        $clinics   = Clinic::getActive()->get();
        $appoints  = AppointType::getActive()->get();
        $providers = Provider::getActive()->get(['id', 'fname', 'lname']);

        return View::make('pages.services.appointment')
            ->with('clinics', $clinics)
            ->with('appoints', $appoints)
            ->with('providers', $providers);
    }

    /**
     * Service refer out
     *
     * POST    /pages/service-refer-out
     *
     * @internal int    $service_id    Service id
     * @return   View
     */
    public function postServiceReferOut()
    {
        $service_id = Input::get('service_id');

        $refer      = ServiceReferOut::getDetail($service_id)->first();
        $providers  = Provider::getActive()->get(['id', 'fname', 'lname']);
        $cause      = DB::table('refer_cause')->get();

        return View::make('pages.services.referout')
            ->with('providers', $providers)
            ->with('cause', $cause)
            ->with('refer', $refer);
    }

    /**
     * Service screening
     *
     * POST    /pages/service-accidents
     *
     * @internal int    $service_id    Service id
     * @return   View
     */
    public function postServiceAccidents()
    {
        $service_id = Input::get('service_id');

        $accident   = ServiceAccident::getDetail($service_id)->first();

        $type       = DB::table('accident_type')->get(['id', 'th_name', 'export_code']);
        $place      = DB::table('accident_place')->get();
        $typein     = DB::table('accident_type_in')->get();
        $vehicle    = DB::table('accident_vehicle')->get();

        return View::make('pages.services.accident', [
            'type'     => $type,
            'accident' => $accident,
            'place'    => $place,
            'typein'   => $typein,
            'vehicle'  => $vehicle
        ]);
    }

    public function postServiceAnc()
    {
        $service_id = Input::get('service_id');
        $person = Service::where('id', '=', $service_id)->select('person_id as id')->first();

        $isExist = Pregnancy::where('person_id', '=', $person->id)
            ->where('hospcode', '=', Auth::user()->hospcode)
            ->count();

        if ($isExist) {
            $gravida = Pregnancy::getGA($person->id)->get(['gravida']);
            $gravidas = [];
            foreach ($gravida as $g) $gravidas[$g->gravida] = $g->gravida;

            $baby_leads = DB::table('anc_baby_leads')->get();
            $baby_positions = DB::table('anc_baby_positions')->get();
            $uterus_levels = DB::table('anc_uterus_levels')->get();

            $uterus = [];
            foreach ($uterus_levels as $u) $uterus[$u->id] = $u->name;

            $gravidas = [];
            foreach ($gravida as $g) $gravidas[$g->gravida] = $g->gravida;

            $positions = [];
            foreach ($baby_positions as $p) $positions[$p->id] = $p->name;
            $leads = [];
            foreach ($baby_leads as $l) $leads[$l->id] = $l->name;

            $providers = Provider::getActive()->get(['id', 'fname', 'lname']);

            $anc = DB::table('service_anc')->where('service_id', '=', $service_id)->first();

            return View::make('pages.services.anc')
                ->with('uterus', $uterus)
                ->with('leads', $leads)
                ->with('positions', $positions)
                ->with('providers', $providers)
                ->with('gravidas', $gravidas)
                ->with('anc', $anc);
        } else {
            return View::make('pages.pregnancies.not-register');
        }
    }
    /**
     * Pregnancy detail
     *
     * POST    /pages/pregnancy-detail
     *
     * @internal int $id Pregnancy id
     * @return   View
     */
    public function postPregnancyDetail()
    {
        $id = Input::get('id');
        $preg = Pregnancy::where('id', $id)->first();
        $risk = PregnancyRisk::where('pregnancy_id', '=', $id)->first();

        if ($preg) {
            $providers = Provider::getActive()->get();
            return View::make('pages.pregnancies.detail')
                ->with('providers', $providers)
                ->with('preg', $preg)
                ->with('risk', $risk);
        } else {
            return View::make('errors.404');
        }
    }

    /**
     * Pregnancy list
     *
     * GET    /pages/pregnancy-list
     *
     * @internal int    $service_id    Service id
     * @return   View
     */
    public function getPregnanciesList()
    {
        return View::make('pages.pregnancies.list');
    }

    public function postPregnanciesAnc()
    {
        return View::make('pages.pregnancies.anc');
    }
}
