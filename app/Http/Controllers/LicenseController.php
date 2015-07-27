<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Phonex\Http\Requests;
use Phonex\Http\Requests\UpdateLicenseRequest;
use Phonex\License;
use Phonex\LicenseFuncType;
use Phonex\User;
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

		$sortable = ['username', 'license_func_type', 'license_type', 'active', 'starts_at', 'expires_at'];
		$s = InputGet::get('s','licenses.id');
		$o = InputGet::get('o', 'asc') == 'desc' ? 'desc' : 'asc';
		if (!in_array($s, $sortable)){
			$s = 'licenses.id';
		}

		$query = License
            ::join('license_types', 'licenses.license_type_id', '=', 'license_types.id')
            ->join('license_func_types', 'licenses.license_func_type_id', '=', 'license_func_types.id')
			->join('users', 'licenses.user_id', '=', 'users.id')
			->orderBy($s, $o)
			->select([
                'users.username',
                'license_types.name as license_type',
                'license_func_types.name as license_func_type',
                'licenses.*',
				\DB::raw('IF(expires_at IS NULL OR expires_at >= NOW(), 1, 0) as active')
            ]); // Warning: MySQL specific syntax

        $query = $query->where('users.qa_trial', false);

		if (InputGet::has('active_only')){
			$query = $query->whereRaw('( expires_at IS NULL OR expires_at >= NOW() )');

		}
		if (InputGet::has('trial_only')){
			$query = $query->where('is_trial', 1);
		}

        if(InputGet::has('username')){
            $query = $query->where('username', 'LIKE', "%" . InputGet::getAlphaNum('username') . "%");
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

		$licenseFuncTypes = LicenseFuncType::all();
		foreach($licenseFuncTypes as $ft){
			if($license->licenseFuncType->id === $ft->id){
				$ft->selected = true;
			}
		}

        return view('license.edit', compact('license', 'licenseFuncTypes'));
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

        if (InputPost::hasNonEmpty('issuer_username')){
            $issuer = User::where('username', InputPost::getAlphaNum('issuer_username'))->first();
            $license->issuer_id = $issuer->id;
        } else {
            $license->issuer_id = null;
        }

		$license->license_func_type_id = $request->get('license_func_type_id');
        $license->comment = InputPost::get('comment');
        $license->save();

        return \Redirect::route('licenses.edit', [$id])
            ->with('success', 'License has been updated. Propagation to users may take up to 24 hours.');
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
