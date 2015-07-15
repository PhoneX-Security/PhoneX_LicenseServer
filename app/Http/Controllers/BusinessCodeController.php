<?php namespace Phonex\Http\Controllers;

use Illuminate\Http\Request;
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

	public function getIndex(Request $request)
    {
        $query = BusinessCode::with(['clMappings', 'users', 'group', 'creator', 'parent']);

        if ($request->has('code')){
            $query = $query->where('code', 'LIKE', '%'. $request->get('code') .'%');
        }

        $bcodes = $query->paginate(15);
        return view('bcode.index', compact('bcodes'));
	}

    public function getCodeExports()
    {
        $items = BusinessCodesExport::with(['creator', 'codes'])->get();
        return view('bcode.exports', compact('items'));
    }

    public function getExport($id)
    {
        $export = BusinessCodesExport::with(['creator','codes'])->findOrFail($id);

        // Only code pairs are supported at the moment
        $codePairs = [];

        // Match pairs
        foreach($export->codes as $code){
            $firstCode = $code->code;
            $secondCode = $code->clMappings->first()->code;

            if (in_array($firstCode, $codePairs)){
                // we found all pairs, breaking
                continue;
            }
            $codePairs[$firstCode] = $secondCode;
        }
        return view('bcode.export-details', compact('codePairs', 'export'));
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

            if ($request->has('expires_at')){
                $command->addExpiration(carbonFromInput($request->get('expires_at')));
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
