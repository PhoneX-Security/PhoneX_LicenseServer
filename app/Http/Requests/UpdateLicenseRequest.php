<?php namespace Phonex\Http\Requests;


class UpdateLicenseRequest extends Request {

//    function __construct(Factory $v){
//        $v->extend('issuer_username_rule',
//            function($attribute, $value, $parameters){
//                if (empty($value)){
//                    return true; // empty is OK
//                }
//                return empty($value) ||
//                User::where('username', $value)
////                return $value == 'foo';
//            }
//        );
////        \Validator::extend()
//
////        $v->extend()
////        Validator::class
//    }


    /**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
            // sometimes = only if input is non-empty
            'issuer_username' => 'sometimes|exists:users,username'

		];
	}

}
