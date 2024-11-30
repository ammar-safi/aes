<?php

namespace app\Traits;



trait GeneralTrait
{

    public function apiResponse($data = null, bool $status = true, $error = null, $statusCode = 200)
    {
        $response = [
            'data' => $data,
            'status' => $status, // True of false 
            'error' => $error, // user not found 
            'statusCode' => $statusCode // 404 500 403 200 303 (headers code)
        ];
        http_response_code($statusCode);
        return json_encode([$response]);
    }

    public function Error($error, $statusCode)
    {
        return $this->apiResponse(null, false, $error, $statusCode);
    }

    /**
     * Successful Response
     * 
     */
    public function SuccessResponse($data = null, $statusCode = 200)
    {
        return $this->apiResponse($data, true, null, $statusCode);
    }
    public function PartialContent($data, $error)
    {
        return $this->apiResponse($data, true, $error, 206);
    }

    /**
     * Client Error Responses
     */
    public function BadRequest($error)
    {
        return $this->apiResponse(null, false, $error, 400);
    }
    public function ValidationError($message)
    {
        return $this->apiResponse(null, false, $message, 400);
    }
    public function Unauthorized()
    {
        return $this->apiResponse(null, false, 'Unauthorized', 401);
    }
    public function Forbidden()
    {
        return $this->apiResponse(null, false, "Forbidden", 403);
    }
    public function NotFound($error)
    {
        return $this->apiResponse(null, false, $error, 404);
    }

    /**
     * Server Errors Response
     * @param $error
     * @return json_encode
     */
    public function ServerError($error)
    {
        return $this->apiResponse(null, false, $error, 500);
    }
}
