<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;
use App\Models\User; // Import your User model or any other models you're using

class HomeController extends Controller
{
    public function __construct()
    {
        // Your constructor logic if needed
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            
            $user = Auth::user();

            // Store any data you want in the session
            Session::put('login_id', $user->id);

            // Redirect to a specific route upon successful login
            return redirect()->route('dashboard'); // Replace 'dashboard' with your route name
        } else {
            // Authentication failed
            return redirect()->back()->withErrors(['login_failed' => 'Invalid credentials']);
        }
    }

    public function signup(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        // Add other user properties as needed

        if ($user->save()) {
            // User registration successful
            Auth::login($user); // Automatically log in the newly registered user
            
            // Store any data you want in the session
            Session::put('login_id', $user->id);

            // Redirect to a specific route upon successful registration
            return redirect()->route('dashboard'); // Replace 'dashboard' with your route name
        } else {
            // Registration failed
            return redirect()->back()->withErrors(['registration_failed' => 'Registration failed']);
        }
    }

    

    public function save_user(Request $request)
    {
        // Your save_user logic using Laravel methods and your User model
    }

    // Define other methods following the same pattern

    // End of the class
}
?>

