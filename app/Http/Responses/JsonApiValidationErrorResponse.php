<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class JsonApiValidationErrorResponse extends JsonResponse
{
    public function __construct($exception, $status = 422)
    {
        $title = $exception->getMessage();

        $data = $this->formatJsonApiErrors($exception);
        $headers = ['content-type' => 'application/vnd.api+json'];
        parent::__construct($data, $status, $headers);
    }

    private function formatJsonApiErrors($exception)
    {
        $title = $exception->getMessage();

        return
            [
                'errors' => collect($exception->errors())
                    ->map(function ($messages, $field) use ($title) {
                        return [
                            'title' => $title,
                            'detail' => $messages[0],
                            'source' => ['pointer' => '/'.str_replace('.', '/', $field)],
                        ];
                    })->values(),
            ];
    }
}
