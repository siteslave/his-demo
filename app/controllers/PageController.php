<?php

class PageController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('denied');
    }

    public function serviceScreening()
    {

        $service_id = Input::get('service_id');
        $services = Service::where('id', (int) $service_id)->first();
        $screen = $services->screening()->first();
        $status = ServiceStatus::all();

        return View::make('pages.services.screening')
            ->with('status', $status)
            ->with('services', $services)
            ->with('screen', $screen);

    }

    public function serviceDiagnosis()
    {
        $service_id = Input::get('service_id');

        $diag = ServiceDiagnosis::getList($service_id)->get();
        $diagtype = DiagnosisType::getActive()->get(['code', 'name']);

        return View::make('pages.services.diagnosis')
            ->with('diag', $diag)
            ->with('diagtype', $diagtype);
    }

    public function serviceProcedure()
    {
        $providers = Provider::getActive()->get(['id', 'fname', 'lname']);

        return View::make('pages.services.procedure')
            ->with('providers', $providers);
    }

    public function serviceIncome()
    {
        return View::make('pages.services.income');
    }

    public function serviceDrug()
    {
        return View::make('pages.services.drug');
    }

    public function serviceAppointment()
    {
        $clinics = Clinic::getActive()->get();
        $appoints = AppointType::getActive()->get();
        $providers = Provider::getActive()->get(['id', 'fname', 'lname']);

        return View::make('pages.services.appointment')
            ->with('clinics', $clinics)
            ->with('appoints', $appoints)
            ->with('providers', $providers);
    }

    public function serviceReferOut()
    {
        $service_id = Input::get('service_id');

        $refer = ServiceReferOut::getDetail($service_id)->first();
        $providers = Provider::getActive()->get(['id', 'fname', 'lname']);
        $cause = DB::table('refer_cause')->get();

        return View::make('pages.services.referout')
            ->with('providers', $providers)
            ->with('cause', $cause)
            ->with('refer', $refer);
    }

    public function serviceAccident()
    {
        $service_id = Input::get('service_id');

        $accident = ServiceAccident::getDetail($service_id)->first();

        $type = DB::table('accident_type')->get(['id', 'th_name', 'export_code']);
        $place = DB::table('accident_place')->get();
        $typein = DB::table('accident_type_in')->get();
        $vehicle = DB::table('accident_vehicle')->get();

        return View::make('pages.services.accident', [
            'type'     => $type,
            'accident' => $accident,
            'place'    => $place,
            'typein'   => $typein,
            'vehicle'  => $vehicle
        ]);
    }

    public function pregnancyDetail()
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
            return View::make('error.404');
        }
    }

    public function pregnancyList()
    {
        return View::make('pages.pregnancies.list');
    }
}
