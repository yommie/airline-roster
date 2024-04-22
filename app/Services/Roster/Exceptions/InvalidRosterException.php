<?php

namespace App\Services\Roster\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InvalidRosterException extends \Exception
{
    public function render(Request $request): JsonResponse
    {
        return new JsonResponse([
            "message"   => $this->getMessage()
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
