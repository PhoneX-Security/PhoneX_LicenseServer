<?php namespace Phonex\Http\Controllers;

use Illuminate\Http\Request;
use Phonex\Http\Requests;
use Phonex\Http\Requests\ResetTrialCounterByImeiRequest;
use Phonex\Model\ErrorReport;
use Phonex\TrialRequest;
use Phonex\Utils\InputGet;

class ReportsController extends Controller {

	public function __construct()
	{
	}

	public function lastErrors(Request $request){
        $limit = InputGet::getInteger('limit', 100);
        $reports = ErrorReport::limit($limit)->orderBy('date_created','desc')->get();
		return view('reports.last-errors', compact('reports'));
	}

    public function lastTrialRequests(Request $request){
        $limit = InputGet::getInteger('limit', 100);
        $imeiPrefix = $request->get('imei');

        $requests = null;
        if ($imeiPrefix){
            $requests = TrialRequest::where('imei', 'like', $imeiPrefix . '%')->limit($limit)->orderBy('dateCreated','desc')->get();
        } else {
            $requests = TrialRequest::limit($limit)->orderBy('dateCreated','desc')->get();
        }

        return view('reports.last-trial-requests', compact('requests'));
    }

    public function resetTrialCounter(ResetTrialCounterByImeiRequest $request){
        $imei = $request->get('imei');
        $imeiPrefix = substr($imei,0, 20);
        $counter = TrialRequest::where('imei', 'like', $imeiPrefix . '%')->delete();
        return redirect()
            ->back()
            ->with("success", 'Trial counter has been reset (' . $counter . ' row(s) were deleted).');
    }

}
