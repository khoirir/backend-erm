<?php

namespace App\Rules;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Validation\Validator;

class DateTimeRule implements ValidationRule, DataAwareRule, ValidatorAwareRule
{
    private array $data;
    private Validator $validator;

    public function setData(array $data): DateTimeRule
    {
        $this->data = $data;
        return $this;
    }

    public function setValidator(Validator $validator): DateTimeRule
    {
        $this->validator = $validator;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $jam = $value;
        $labelAtribut = str_replace("jam", "", $attribute);
        $tanggal = $this->data['tanggal' . $labelAtribut] . " " . $jam;
        try {
            $tanggalJam = Carbon::parse($tanggal);
            if ($tanggalJam->isFuture()) {
                $fail("TIDAK BOLEH LEBIH DARI JAM SEKARANG");
            }
        }catch (InvalidFormatException){
            $fail("HARUS SESUAI FORMAT (H:i:s)");
        }

    }

}
