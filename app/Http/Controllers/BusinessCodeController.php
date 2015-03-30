<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mail;
use Phonex\BusinessCode;
use Phonex\BusinessCodeClMapping;
use Phonex\ContactList;
use Phonex\Events\AuditEvent;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\CreateUserRequest;
use Phonex\Http\Requests\GenerateMPCodesRequest;
use Phonex\Http\Requests\UpdateUserRequest;
use Phonex\License;
use Phonex\LicenseType;
use Phonex\Subscriber;
use Phonex\User;
use Phonex\Utils\InputGet;
use Phonex\Utils\InputPost;
use Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BusinessCodeController extends Controller {
	public function BusinessCodeController(){
	}

	public function getIndex(){
        $bcodes = BusinessCode::paginate(15);
        return view('bcode.index', compact('bcodes'));
	}

    public function getGenerateMpCodes(){
        return view('bcode.create');
    }

    /**
     * @param GenerateMPCodesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGenerateMpCodes(GenerateMPCodesRequest $request){

        $mpGroup = Group::where('name', 'Mobil Pohotovost')->first();
        $mpLicenseType = LicenseType::where('name', 'mp_half_year')->first();

        $numberOfPairs = $request->get('number_of_pairs');
        $email = $request->get('email');

        $bcodes = array();

        for ($i=0; $i < $numberOfPairs; $i++){
            // first code
            $bc1 = new BusinessCode();
            $bc1->code = BusinessCode::generateUniqueCode();
            $bc1->group_id = $mpGroup->id;
            $bc1->creator_id = \Auth::user()->id;

            $bc1->license_type_id = $mpLicenseType->id;
            $bc1->licenses_limit = 1; // only one license per this code
            $bc1->is_active = 1;
            $bc1->exported = 1;


            // second code
            $bc2 = clone $bc1;
            $bc2->code = BusinessCode::generateUniqueCode();

            $bc1->save();
            event(AuditEvent::create('business_code', $bc1->id));
            $bc2->save();
            event(AuditEvent::create('business_code', $bc2->id));
            $bcodes[] = $bc1;
            $bcodes[] = $bc2;

            $mapping1 = new BusinessCodeClMapping();
            $mapping1->cl_owner_bcode_id = $bc1->id;
            $mapping1->contact_bcode_id = $bc2->id;
            $mapping1->save();

            $mapping2 = new BusinessCodeClMapping();
            $mapping2->cl_owner_bcode_id = $bc2->id;
            $mapping2->contact_bcode_id = $bc1->id;
            $mapping2->save();
        }

        Mail::send('emails.mp_bcodes', [ 'bcodes' => $bcodes], function($message) use ($email)
        {
            $message->from('license-server@phone-x.net', 'License server');
            $message->to($email)->subject('Mobil Pohotovost: new code pairs');
        });

        return redirect('bcodes')
            ->with('success', "New $numberOfPairs MP business code pairs generated and sent to $email.");
    }

    public function getShow($id){
        if (!is_numeric($id)){
            abort(404);
        }
    }



}
