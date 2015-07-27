<?php namespace Phonex\Http\Controllers;

use Bus;
use Illuminate\Http\Request;
use Mail;
use Phonex\BusinessCode;
use Phonex\BusinessCodeExport;
use Phonex\BusinessCodesExport;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\GenerateCodePairsRequest;
use Phonex\Jobs\NewCodePairsExport;
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

        // All codes in a single export have same properties
        $firstCode = $export->codes->count() > 0 ? $export->codes[0] : null;

        return view('bcode.export-details', compact('codePairs', 'export', 'firstCode'));
    }

    public function getGenerateSingleCodes()
    {
        die('TODO');
    }

    public function getGenerateCodePairs()
    {
        $groups = Group::all();
        $licenseTypes = LicenseType::all();
        foreach ($licenseTypes as $lt){
            if ($lt->name === LicenseType::EXPIRATION_QUARTER){
                $lt->default = true;
            }
        }
        $licenseFuncTypes = LicenseFuncType::all();
        foreach ($licenseFuncTypes as $lft){
            if ($lft->name === LicenseFuncType::TYPE_TRIAL){
                $lt->default = true;
            }
        }

        return view('bcode.create_pair', compact('groups', 'licenseTypes', 'licenseFuncTypes'));
    }

    public function postGenerateCodePairs(GenerateCodePairsRequest $request)
    {
        $group = Group::find($request->get('group_id'));
        $licenseType = LicenseType::find($request->get('license_type_id'));
        $licenseFuncType = LicenseFuncType::find($request->get('license_func_type_id'));

        $numberOfPairs = $request->get('number_of_pairs');
        $email = $request->get('email');
        $parent = $request->has('parent_username') ? User::findByUsername($request->get('parent_username')) : null;

        $command = new NewCodePairsExport($numberOfPairs, $licenseType, $licenseFuncType, 1);
        if ($request->has('comment')){
            $command->addComment($request->get('comment'));
        }
        if ($group){
            $command->addGroup($group);
        }
        if ($request->has('expires_at')){
            $command->addExpiration(carbonFromInput($request->get('expires_at')));
        }
        if ($parent){
            $command->addParent($parent);
        }

        list($export, $codePairs) = Bus::dispatch($command);
        $export->email = $email;
        $export->save();

        Mail::send('emails.new_code_pairs', ['codePairs' => $codePairs, 'parent'=>$parent, 'group'=>$group], function($message) use ($email, $group)
        {
            $message->from('license-server@phone-x.net', 'License server');
            $message->to($email)->subject('New code pairs generated');
        });

        return redirect('bcodes')
            ->with('success', "New $numberOfPairs business code pairs generated and sent to $email.");
    }
}