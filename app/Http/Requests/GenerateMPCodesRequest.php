<?php namespace Phonex\Http\Requests;

class GenerateMPCodesRequest extends Request {

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
			'number_of_pairs' => 'required|integer|between:1,30',
            'email' => 'required|email'
		];
	}

}
