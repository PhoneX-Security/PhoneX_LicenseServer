<?php namespace Phonex\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Phonex\BusinessCode;
use Phonex\ContactList;
use Phonex\Events\AuditEvent;
use Phonex\Group;
use Phonex\Http\Requests;
use Phonex\Http\Requests\CreateUserRequest;
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

    public function getShow($id){
        if (!is_numeric($id)){
            abort(404);
        }
    }



}
