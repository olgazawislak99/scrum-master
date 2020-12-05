<?php


namespace App\Service;


class ResponseHelper
{
    public static function defineSuccess(array &$responseResult)
    {
        if (!empty($responseResult)) {
            $responseResult['success'] = true;
        } else {
            $responseResult['success'] = false;
        }

    }

}