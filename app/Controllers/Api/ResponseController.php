<?php

namespace app\Controllers\Api;

use app\Traits\GeneralTrait;

class ResponseController
{
    use GeneralTrait;

    public function helloMessage()
    {
        $data['message'] =  "This_application_was_made_by_Eng_AmmarSafi_😇";
        echo $this->SuccessResponse($data);
    }

    public function notFoundUrl()
    {
        $errorMessage["message"] = "This URL not found";
        echo $this->NotFound($errorMessage);
    }
}
