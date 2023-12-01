<?php

namespace App\Http\Requests;

use App\Rules\DateTimeRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PemeriksaanRequest extends FormRequest
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
            'tanggalPerawatan' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:today'],
            'jamPerawatan' => ['required', 'date_format:H:i:s', new DateTimeRule()],
            'keluhan' => ['max:2000'],
            'pemeriksaan' => ['max:2000'],
            'penilaian' => ['required', 'max:2000'],
            'suhuTubuh' => ['numeric', 'max:99999'],
            'beratBadan' => ['numeric', 'max:99999'],
            'tinggiBadan' => ['numeric', 'max:99999'],
            'tensi' => ['required', 'max:8'],
            'nadi' => ['numeric', 'max:999'],
            'respirasi' => ['numeric', 'max:999'],
            'instruksi' => ['required', 'max:2000'],
            'evaluasi' => ['required', 'max:2000'],
            'kesadaran' => ['required', 'in:Compos Mentis,Somnolence,Sopor,Coma'],
            'alergi' => ['max:50'],
            'spo2' => ['required', 'numeric', 'max:999'],
            'gcs' => ['max: 10'],
            'tindakLanjut' => ['required', 'max:2000'],
            'lingkarPerut' => ['numeric', 'max: 99999']
        ];
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
