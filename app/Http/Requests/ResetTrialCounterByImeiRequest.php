<?php namespace Phonex\Http\Requests;

class ResetTrialCounterByImeiRequest extends Request {

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
			'imei' => 'required|min:20|max:255',
		];
	}

}
