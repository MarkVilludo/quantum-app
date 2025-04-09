<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Year;
use Illuminate\Http\Response;

class YearController extends Controller
{
    protected $year;

    public function __construct(Year $year) {
        $this->year = $year;
    }

    public function index()
    {
        $years = $this->year->all();

        return response()->json([
            'success' => true,
            'message' => 'Years retrieved successfully.',
            'data' => $years
        ], Response::HTTP_OK);
    }
}
