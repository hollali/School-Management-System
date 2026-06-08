<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:Admin,Teacher,Parent,Student'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role if Spatie is available
        if (method_exists($user, 'assignRole')) {
            $user->assignRole($request->role);
        }

        // Create related profiles for Student or Parent roles
        if ($request->role === 'Student') {
            // create a basic student profile linked to the user
            \App\Models\Student::create([
                'user_id' => $user->id,
                'admission_number' => null,
                'date_of_birth' => null,
                'gender' => null,
                'phone' => null,
                'address' => null,
                'parent_id' => null,
            ]);
        }

        if ($request->role === 'Parent') {
            \App\Models\ParentProfile::create([
                'user_id' => $user->id,
                'relationship' => null,
                'phone' => null,
                'address' => null,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
