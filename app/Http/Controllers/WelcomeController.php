<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Credit;

class WelcomeController extends Controller
{
    public function start(Request $request)
    {
        // Validate the password
        $request->validate([
            'password' => 'required|string',
        ]);

        // Check if the password is correct
        if ($request->password !== '2025') {
            return response()->json([
                'message' => 'The password is incorrect.'
            ], 401); // Unauthorized status code
        }

        // Return success message
        return response()->json([
            'message' => 'Password verified successfully!'
        ], 200);

    }

    public function holding()
    {

        $credits =  Credit::find(1);


        return Inertia::render('Holding', [
            'credits'=> $credits,

        ]);
    }





}
