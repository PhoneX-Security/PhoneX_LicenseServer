<?php namespace Phonex\Http\Controllers;

use Mail;
use Phonex\BusinessCode;
use Phonex\Commands\CreateBusinessCodePair;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\GenerateMPCodesRequest;
use Phonex\LicenseFuncType;
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
        $mpLicenseType = LicenseType::where('name', 'half_year')->first();
        $mpLicenseFuncType = LicenseFuncType::getFull();

        $numberOfPairs = $request->get('number_of_pairs');
        $email = $request->get('email');

        $creator = \Auth::user();
        $bcodes = array();

        for ($i=0; $i < $numberOfPairs; $i++){
            $command = new CreateBusinessCodePair($creator, $mpLicenseType, $mpLicenseFuncType, $mpGroup, 1, 'mp');
            $newCodes = $this->dispatch($command);
            $bcodes = array_merge($bcodes, $newCodes);
        }


        foreach($bcodes as $c){
            // add dashes
            $c->code = substr($c->code, 0, 3) . "-" . substr($c->code, 3, 3) . "-" . substr($c->code, 6);
        }
//        dd($bcodes);

        Mail::send('emails.mp_bcodes', ['bcodes' => $bcodes], function($message) use ($email)
        {
            $message->from('license-server@phone-x.net', 'License server');
            $message->to($email)->subject('Mobil Pohotovost: new code pairs');
        });

        return redirect('bcodes')
            ->with('success', "New $numberOfPairs MP business code pairs generated and sent to $email.");
    }
}
