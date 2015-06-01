<?php namespace Phonex\Http\Requests;

class GenerateSingleCodesRequest extends Request {

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
			'number' => 'required|integer|between:1,30',
            'license_type_id' => 'exists:phonex_license_types,id|required_with:issue_license',
            'email' => 'required|email'
		];
	}

}
