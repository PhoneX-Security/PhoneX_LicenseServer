<?php namespace Phonex\Http\Controllers;

use Mail;
use Phonex\BusinessCode;
use Phonex\BusinessCodeExport;
use Phonex\BusinessCodesExport;
use Phonex\Jobs\CreateBusinessCodePair;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\GenerateCodePairsRequest;
use Phonex\Http\Requests\GenerateSingleCodesRequest;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\User;

class BusinessCodeController extends Controller {
	public function BusinessCodeController(){
	}

	public function getIndex(){
        $bcodes = BusinessCode::paginate(15);
        return view('bcode.index', compact('bcodes'));
	}

    public function getGenerateSingleCodes(){
        die('TODO');
    }

    public function getGenerateCodePairs(){
        $groups = Group::all();
        $licenseTypes = LicenseType::all();
        foreach ($licenseTypes as $lt){
            if ($lt->name == 'quarter'){
                $lt->default = true;
            }
        }
        return view('bcode.create_pair', compact('groups', 'licenseTypes'));
    }

    public function postGenerateCodePairs(GenerateCodePairsRequest $request){
        $group = Group::find($request->get('group_id'));
        $licenseType = LicenseType::find($request->get('license_type_id'));
        $licenseFuncType = LicenseFuncType::getFull();

        $numberOfPairs = $request->get('number_of_pairs');
        $email = $request->get('email');

        $creator = \Auth::user();
        $parent = $request->has('parent_username') ? User::findByUsername($request->get('parent_username')) : null;

        $export = new BusinessCodesExport();
        $export->email = $email;
        $export->creator_id = $creator->id;
        $export->save();

        $bcodes = array();

        for ($i=0; $i < $numberOfPairs; $i++){
            $command = new CreateBusinessCodePair($creator, $licenseType, $licenseFuncType, $export);
            if ($group){
                $command->addGroup($group);
            }

            if ($parent){
                $command->addParent($parent);
            }
            $newCodes = $this->dispatch($command);
            $bcodes = array_merge($bcodes, $newCodes);
        }

        foreach($bcodes as $c){
            // add dashes
            $c->code = substr($c->code, 0, 3) . "-" . substr($c->code, 3, 3) . "-" . substr($c->code, 6);
        }
//        dd($bcodes);

        Mail::send('emails.new_code_pairs', ['bcodes' => $bcodes, 'parent'=>$parent, 'group'=>$group], function($message) use ($email, $group)
        {
            $message->from('license-server@phone-x.net', 'License server');
            $message->to($email)->subject('New code pairs generated');
        });

        return redirect('bcodes')
            ->with('success', "New $numberOfPairs business code pairs generated and sent to $email.");
    }
}
