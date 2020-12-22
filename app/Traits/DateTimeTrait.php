<?php

namespace App\Traits;

trait DateTimeTrait
{

    //* Make the date fillable to sql table
    public function makeDateFillable(string $date, string $separator = '.'): string
    {
        $dates = explode($separator, $date);
        return "$dates[2]-$dates[1]-$dates[0]";
    }
}
