<?php

namespace App\Traits;

trait getPaymentAndCurrenciesTrait
{
    //* Get the currencies and payment methods
    private function getPaymentAndCurrencies($includeShapes = true)
    {
        $payment_types = config('params.payment_types');
        $payment_types_res = [];
        foreach ($payment_types as $key => $val) {
            $payment_types_res[] = [
                'id' => $key,
                'name' => $val
            ];
        }
        $currencies = config('params.currencies');
        $currencies_res = [];
        foreach ($currencies as $key => $val) {
            $currencies_res[] = [
                'id' => $key,
                'name' => $val
            ];
        }
        $res = [
            'payment_types' => $payment_types_res,
            'currencies' => $currencies_res,
        ];
        if ($includeShapes) {
            $res['project'] = [
                'title' => '',
                'description' => '',
                'deadline' => date('Y-m-d'),
                'photo' => null
            ];
            $res['steps'] = [
                [
                    'title' => '',
                    'price' => '',
                    'currency_id' => [
                        'id' => 1,
                        'name' => config("params.currencies.1")
                    ],
                    'payment_type' => [
                        'id' => 1,
                        'name' => config("params.payment_types.1")
                    ]
                ]
            ];
        }
        return $res;
    }
}
