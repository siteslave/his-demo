<?php

/**
 * Class ApiController
 *
 * @author      Satit Rianpit <rianpit@gmail.com>
 * @copyright   2013-2015 by Maha Sarakahm Hospital <http://www.mkh.go.th>
 * @since       1.0
 */
class ApiController extends BaseController {

    /**
     * Constructor function
     *
     */
    public function __construct()
    {
        $this->beforeFilter('denied');
    }

    /**
     * Search Hospital
     *
     * URI  GET /api/search/hospital
     *
     * @internal int $page
     * @internal int $page_limit
     * @internal string $callback
     *
     * @return json
     *
     */
    public function searchHospital()
    {
        $page       = Input::get('page');
        $page_limit = Input::get('page_limit');
        $callback   = Input::get('callback');

        $start      = $page == 1 ? 0 : ($page * $page_limit) - 1;

        $query      = Input::get('query');

        $result = DB::table('hospitals')
            ->select('hmain', 'hname')
            ->where('hmain', 'like', '%' . $query . '%')
            ->orWhere('hname', 'like', '%' . $query . '%')
            ->skip($start)
            ->take($page_limit)
            ->get();

        $total = DB::table('hospitals')
            ->select('hmain', 'hname')
            ->where('hmain', 'like', '%' . $query . '%')
            ->orWhere('hname', 'like', '%' . $query . '%')
            ->count();

        $json = array(
            'ok'    => 1,
            'rows'  => $result,
            'total' => $total
        );

        return Response::json($json)->setCallback($callback);
    }

    public function searchDoctorRoom()
    {
        $clinic_id = Input::get('clinic_id');
        $callback  = Input::get('callback');

        $doctor_rooms = DB::table('doctor_rooms')
            ->where('clinic_id', $clinic_id)
            ->get();

        $json = array('ok' => 1, 'rows' => $doctor_rooms);

        return Response::json($json)->setCallback($callback);
    }

    public function searchDiagnosis()
    {
        $page = Input::get('page');
        $page_limit = Input::get('page_limit');
        $callback = Input::get('callback');

        $start = $page == 1 ? 0 : ($page * $page_limit) - 1;

        $condition = Input::get('query');

        $result = DB::table('diagnosis')
            ->select('code', 'desc_r')
            ->where('valid', 'T')

            ->Where(function ($query) use ($condition) {
                $query
                    ->where('code', 'like', $condition . '%')
                    ->orWhere('desc_r', 'like', '%' . $condition . '%');
            })
            ->skip($start)
            ->take($page_limit)
            ->get();

        $total = DB::table('diagnosis')
            ->where('valid', 'T')
            ->Where(function ($query) use ($condition) {
                $query
                    ->where('code', 'like', $condition . '%')
                    ->orWhere('desc_r', 'like', '%' . $condition . '%');
            })
            ->count();

        $json = array(
            'ok'    => 1,
            'rows'  => $result,
            'total' => $total
        );

        return Response::json($json)->setCallback($callback);
    }

    public function searchProcedure()
    {
        $page       = Input::get('page');
        $page_limit = Input::get('page_limit');
        $type       = Input::get('t');
        $callback   = Input::get('callback');

        $query      = Input::get('query');

        $start      = $page == 1 ? 0 : ($page * $page_limit) - 1;

        $result = DB::table('procedures')
            ->select('id', 'name', 'price')
            ->where('is_active', 'Y')
            ->where('procedure_type', $type)
            ->where('name', 'like', '%' . $query . '%')
            ->skip($start)
            ->take($page_limit)
            ->get();

        $total = DB::table('procedures')
            ->where('is_active', 'Y')
            ->where('procedure_type', $type)
            ->where('name', 'like', '%' . $query . '%')
            ->count();

        $json = [
            'ok'    => 1,
            'rows'  => $result,
            'total' => $total
        ];

        return Response::json($json)->setCallback($callback);
    }

    public function getProcedurePosition()
    {
        $callback     = Input::get('callback');
        $procedure_id = Input::get('id');

        $result = DB::table('procedure_positions')
            ->where('procedure_id', (int) $procedure_id)
            ->get();

        $json = ['ok' => 1, 'rows' => $result];

        return Response::json($json)->setCallback($callback);
    }

    public function searchIncome()
    {
        $page       = Input::get('page');
        $page_limit = Input::get('page_limit');
        $type       = Input::get('t');
        $callback   = Input::get('callback');

        $query      = Input::get('query');

        $start      = $page == 1 ? 0 : ($page * $page_limit) - 1;

        $result = DB::table('incomes')
            ->select('id', 'name', 'price')
            ->where('is_active', 'Y')
            ->where('name', 'like', '%' . $query . '%')
            ->skip($start)
            ->take($page_limit)
            ->get();

        $total = DB::table('incomes')
            ->where('is_active', 'Y')
            ->where('name', 'like', '%' . $query . '%')
            ->count();

        $json = [
            'ok'    => 1,
            'rows'  => $result,
            'total' => $total
        ];

        return Response::json($json)->setCallback($callback);
    }

    public function searchDrug()
    {
        $page       = Input::get('page');
        $page_limit = Input::get('page_limit');
        $callback   = Input::get('callback');

        $query      = Input::get('query');

        $start      = $page == 1 ? 0 : ($page * $page_limit) - 1;

        $result = DB::table('drugs')
            ->select('id', 'name', 'price')
            ->where('is_active', 'Y')
            ->where('name', 'like', '%' . $query . '%')
            ->skip($start)
            ->take($page_limit)
            ->get();

        $total = DB::table('drugs')
            ->where('is_active', 'Y')
            ->where('name', 'like', '%' . $query . '%')
            ->count();

        $json = [
            'ok'    => 1,
            'rows'  => $result,
            'total' => $total
        ];

        return Response::json($json)->setCallback($callback);
    }

    public function searchDrugUsage()
    {
        $page       = Input::get('page');
        $page_limit = Input::get('page_limit');
        $callback   = Input::get('callback');

        $query      = Input::get('query');

        $start      = $page == 1 ? 0 : ($page * $page_limit) - 1;

        $result = DB::table('drugusages')
            ->select('id', 'code')
            //->where('is_active', 'Y')
            ->where('code', 'like', $query . '%')
            ->skip($start)
            ->take($page_limit)
            ->get();

        $total = DB::table('drugusages')
            //->where('is_active', 'Y')
            ->where('code', 'like', $query . '%')
            ->count();

        $json = [
            'ok'    => 1,
            'rows'  => $result,
            'total' => $total
        ];

        return Response::json($json)->setCallback($callback);
    }
}
