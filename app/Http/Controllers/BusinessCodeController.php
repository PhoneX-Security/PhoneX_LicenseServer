<?php namespace Phonex\Http\Controllers;

use Mail;
use Phonex\BusinessCode;
use Phonex\Commands\CreateBusinessCodePair;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\GenerateMPCodesRequest;
use Phonex\LicenseType;

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

        $creator = \Auth::user();
        $bcodes = array();

        for ($i=0; $i < $numberOfPairs; $i++){
            $command = new CreateBusinessCodePair($creator, $mpLicenseType, $mpGroup, 1, 'mp');
            $newCodes = $this->dispatch($command);
            array_merge($bcodes, $newCodes);
        }

        Mail::send('emails.mp_bcodes', [ 'bcodes' => $bcodes], function($message) use ($email)
        {
            $message->from('license-server@phone-x.net', 'License server');
            $message->to($email)->subject('Mobil Pohotovost: new code pairs');
        });

        return redirect('bcodes')
            ->with('success', "New $numberOfPairs MP business code pairs generated and sent to $email.");
    }
}
