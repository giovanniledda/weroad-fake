<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use Illuminate\Validation\ValidationException;

final class FiltersValidator
{
    public function validate(array $inputs)
    {
        $validator = Validator::make($inputs,
            [
                'priceFrom' => 'numeric|nullable|sometimes',
                'priceTo' => 'numeric|nullable|sometimes',
                'dateFrom' => 'date_format:Y-m-d|nullable|sometimes',
                'dateTo' => 'date_format:Y-m-d|nullable|sometimes|after:dateFrom',
                'sortByPrice' => 'sometimes|in:asc,desc',
            ])->sometimes('priceTo', 'gte:priceFrom', function (Fluent $input) {
                return ! empty($input->priceFrom);
            });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
