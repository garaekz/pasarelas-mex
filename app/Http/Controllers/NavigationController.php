<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NavigationController extends Controller
{
    public function welcome(Request $request)
    {
        $response = [
            'products' => Product::all(),
        ];
        if ($request->has('success')) {
            $response['success'] = $request->success;
        }
        return Inertia::render('Welcome', $response);
    }
}
