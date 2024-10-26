<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeaderResource;
use App\Models\Header;

class HeaderController extends Controller
{
    public function index()
    {
        $headers = Header::all();

        //return with Api Resource
        return new HeaderResource(true, 'Data Header', $headers);
    }
}
