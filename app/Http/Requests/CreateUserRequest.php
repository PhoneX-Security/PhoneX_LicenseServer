<?php namespace Phonex\Http\Requests;

use Phonex\Http\Requests\Request;

class CreateUserRequest extends Request {

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
			'username' => 'required|max:255|unique:phonex_users',
//			'email' => 'required|email|max:255|unique:users',
			// giving access
			'password' => 'confirmed|min:8|max:255|required_with:has_access',
			// giving license
			'starts_at' => 'date_format:"d-m-Y"|required_with:issue_license',
			'sip_default_password' => 'min:8|max:255|required_with:issue_license',
			'license_type_id' => 'exists:phonex_license_types,id|required_with:issue_license',
            'issuer_username' => 'exists:phonex_users,username|required_with:issue_license',
		];
	}

}
