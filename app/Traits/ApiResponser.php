<?php


namespace App\Traits;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ApiResponser
{
    protected $status = JsonResponse::HTTP_OK;
    protected $message = null;
    protected $data = [];

    /**
     * @param $data
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $message = null): JsonResponse
    {
        return response()->json([
            'data' => $data, 
            'message' => $message, 
            'errors' => null
        ], JsonResponse::HTTP_OK );
    }

    /**
     * @param $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($errors, $code = JsonResponse::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return response()->json([
            'message' => trans('messages.operation_fail'), 
            'errors' => $errors, 
        ], $code );
    }
    
    protected function sendResponse(): JsonResponse
    {
        return $this->status == JsonResponse::HTTP_OK || $this->status == JsonResponse::HTTP_CREATED ?
            $this->successResponse($this->message, $this->data, $this->status) :
            $this->errorResponse($this->message , $this->data, $this->status);
    }

    protected function sendResourceResponse(array $response,$resource, $collection = true)
    {
            return $response['code'] == JsonResponse::HTTP_OK || $response['code'] == JsonResponse::HTTP_CREATED ?
                 $this->getResponseData($response,$resource,$collection):
                 $this->errorResponse($response['message'],$response['code']);
    }

    private function setResourceCollection($data,$resource,$collection){
        return $collection ? $resource::collection($data) : new $resource($data);
    }

    private function getResponseData($response,$resource,$collection)
    {
        return isset($response['data']) && !is_null($response['data']) ?
            $this->setResourceCollection($response['data'],$resource,$collection)->additional(['code' => $response['code'], 'message' => $response['message']]) :
            $this->successResponse(['data' => $response['data'],'message' => $response['message']],$response['code']);
    }
    protected function returnData(): array
    {
        return ['data' => $this->data,'message' => $this->message,'code' => $this->status];
    }
}
