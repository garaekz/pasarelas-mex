<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NavigationController extends Controller
{
    public function welcome()
    {
        return Inertia::render('Welcome', [
            'products' => Product::all(),
        ]);
    }
}
