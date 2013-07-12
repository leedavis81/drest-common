<?php

function ad_1103_getFeedFields($golden_record_data)
{
    // receives the entire golden_record as an array
    // returns an array of fields to be added to the live feed
    // element name will be the field name

    $day = substr($golden_record_data["birth_date"], 8, 2);
    $day = (strlen($day) == 1) ?  str_pad($day, 2, 0, STR_PAD_LEFT) : $day;
    $month = substr($golden_record_data["birth_date"], 5, 2);
    $month = (strlen($month) == 1) ?  str_pad($month, 2, 0, STR_PAD_LEFT) : $month;
    $year = substr($golden_record_data["birth_date"], 0, 4);

    return array(
        'dob_day' => $day,
        'dob_month' => $month,
        'dob_year' => $year
    );
}



function ad_1103_getFeedFields2($golden_record_data)
{
    // receives the entire golden_record as an array
    // returns an array of fields to be added to the live feed
    // element name will be the field name

    $day = substr($golden_record_data["birth_day"], 8, 2);
    if (strlen($day) == 1)
    {
        $day = str_pad($day, 2, 0, STR_PAD_LEFT);

        $month = substr($golden_record_data["birth_month"], 5, 2);
        if (strlen($month) == 1)
        {
            $month = str_pad($month, 2, 0, STR_PAD_LEFT);

            $year = substr($golden_record_data["birth_year"], 0, 4);
            return array(
                'dob_day' => $day,
                'dob_month' => $month,
                'dob_year' => $year
            );
        }
    }
}
