<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FaceRegisterController extends Controller
{
    public function register(Request $request)
    {
        $descriptor = $request->input('descriptor');

        // Assuming user is authenticated, save the descriptor to the user's profile
        $user = User::find(1);

        if ($user) {
            $user->face_descriptor = json_encode($descriptor);
            $user->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'User not authenticated']);
    }
}
