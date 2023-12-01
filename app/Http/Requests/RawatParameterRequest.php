<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Routing\Route;

class RawatParameterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "noRawat" => ['required'],
            "tanggalRawat" => ['required', 'date_format:Y-m-d'],
            "jamRawat" => ['required', 'date_format:H:i:s'],
        ];
    }

    public function validationData(): array
    {
        return array_merge($this->request->all(), [
            'noRawat' => request()->route('noRawat'),
            'tanggalRawat' => request()->route('tanggalRawat'),
            'jamRawat' => request()->route('jamRawat'),
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "error" => [
                "pesan" => $validator->getMessageBag()
            ]
        ], 400));
    }
}
