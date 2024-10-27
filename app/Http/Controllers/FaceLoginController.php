<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FaceLoginController extends Controller
{
    public function login(Request $request)
    {
        $descriptor = $request->input('descriptor');
        
        // Assuming each user has a stored face descriptor (array of floats)
        $users = User::all();
        
        foreach ($users as $user) {
            $storedDescriptor = json_decode($user->face_descriptor, true);
            
            if ($this->compareDescriptors($descriptor, $storedDescriptor)) {
                Auth::login($user);
                return response()->json(['success' => true]);
            }
        }
        
        return response()->json(['success' => false]);
    }

    private function compareDescriptors($descriptor1, $descriptor2, $threshold = 0.6)
    {
        $distance = 0.0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $distance += pow($descriptor1[$i] - $descriptor2[$i], 2);
        }
        return sqrt($distance) < $threshold;
    }
}
