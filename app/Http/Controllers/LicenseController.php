<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Phonex\Http\Requests;
use Phonex\Http\Requests\UpdateLicenseRequest;
use Phonex\License;
use Phonex\Utils\InputGet;
use Phonex\Utils\InputPost;
use Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LicenseController extends Controller {



	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(){
		if (\Request::has('filters')){
//			dd($_GET);
//			dd(\Request::get('filters'));
		}

		$sortable = ['username', 'is_trial', 'license_type', 'active', 'starts_at', 'expires_at'];
		$s = InputGet::get('s','phonex_licenses.id');
		$o = InputGet::get('o', 'asc') == 'desc' ? 'desc' : 'asc';
		if (!in_array($s, $sortable)){
			$s = 'phonex_licenses.id';
		}

		$query = License::join('phonex_license_types', 'phonex_licenses.license_type_id', '=', 'phonex_license_types.id')
			->join('phonex_users', 'phonex_licenses.user_id', '=', 'phonex_users.id')
			->orderBy($s, $o)
			->select(['phonex_users.username', 'phonex_license_types.name as license_type', 'phonex_license_types.is_trial', 'phonex_licenses.*',
				\DB::raw('IF(expires_at IS NULL OR expires_at >= NOW(), 1, 0) as active')]); // Warning: MySQL specific syntax

		if (InputGet::has('active_only')){
			$query = $query->whereRaw('( expires_at IS NULL OR expires_at >= NOW() )');

		}
		if (InputGet::has('trial_only')){
			$query = $query->where('is_trial', 1);
		}

		$licenses = $query->paginate(15);

		foreach ($licenses as $v){
			if ($v->starts_at){
				$v->formatted_starts_at = Carbon::parse($v->starts_at)->format('Y-m-d');
			}
			if ($v->expires_at) {
				$v->formatted_expires_at = Carbon::parse($v->expires_at)->format('Y-m-d');
			}
		}
		return view('license.index', ['licenses' => $licenses]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	public function edit($id)
	{
		$license = License::find($id);
        if ($license == null){
            throw new NotFoundHttpException;
        }

        return view('license.edit', compact('license'));
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param UpdateLicenseRequest $request
     * @return Response
     */
	public function update($id, UpdateLicenseRequest $request)
	{
        $license = License::find($id);
        if ($license == null){
            throw new NotFoundHttpException;
        }
        $license->comment = InputPost::get('comment');
        $license->save();

        return \Redirect::route('licenses.edit', [$id])
            ->with('success', 'License has been updated.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
