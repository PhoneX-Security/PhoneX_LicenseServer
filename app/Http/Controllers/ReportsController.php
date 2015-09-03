<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Phonex\Http\Requests\ResetTrialCounterByImeiRequest;
use Phonex\Jobs\CreateSubscriberWithLicense;
use Phonex\Jobs\CreateUser;
use Phonex\Jobs\IssueLicense;
use Phonex\ContactList;
use Phonex\Events\AuditEvent;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\AddUserToClRequest;
use Phonex\Http\Requests\CreateUserRequest;
use Phonex\Http\Requests\DeleteContactRequest;
use Phonex\Http\Requests\NewLicenseRequest;
use Phonex\Http\Requests\UpdateUserRequest;
use Phonex\LicenseFuncType;
use Phonex\LicenseType;
use Phonex\Model\ErrorReport;
use Phonex\Role;
use Phonex\Subscriber;
use Phonex\TrialRequest;
use Phonex\User;
use Phonex\Utils\InputGet;
use Phonex\Utils\InputPost;
use Phonex\Utils\Stats;
use Queue;
use Redirect;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $requests = TrialRequest::limit($limit)->orderBy('dateCreated','desc')->get();
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
