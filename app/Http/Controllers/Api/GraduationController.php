<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GraduationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GraduationController extends Controller
{
    public function __construct(private GraduationService $graduation)
    {
    }

    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => $this->graduation->statusPayloadForUser($user),
        ], Response::HTTP_OK);
    }

    public function certificate(Request $request)
    {
        $user = $request->user();
        $html = $this->graduation->certificateHtml($user);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => $html,
                'data' => $this->graduation->statusPayloadForUser($user),
            ], Response::HTTP_OK);
        }

        return response($html, Response::HTTP_OK)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
