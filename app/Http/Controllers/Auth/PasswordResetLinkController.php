<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Http\Controllers\Controller;

class PasswordResetLinkController extends Controller
{
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Nenhum utilizador encontrado.'
            ]);
        }

        $code = rand(100000, 999999);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Mail::raw(
            "O seu código de recuperação é: {$code}",
            function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Código de Recuperação');
            }
        );
        session([
            'reset_email' => $request->email
        ]);
        return redirect()
            ->route('password.verify')
            ->with('status', 'Código enviado com sucesso.');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required']
        ]);

        $record = DB::table('password_resets')
            ->where('email', session('reset_email'))
            ->where('code', $request->code)
            ->first();

        if (!$record) {
            return back()->withErrors([
                'code' => 'Código inválido.'
            ]);
        }

        if (Carbon::parse($record->expires_at)->isPast()) {

            DB::table('password_resets')
                ->where('email', session('reset_email'))
                ->delete();

            return back()->withErrors([
                'code' => 'Código expirado.'
            ]);
        }

        session([
            'code_verified' => true
        ]);

        return redirect()->route('password.new');
    }

    public function updatePassword(Request $request)
    {
        if (!session('code_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:6'
            ]
        ]);

        $user = User::where(
            'email',
            session('reset_email')
        )->first();

        if (!$user) {
            return redirect()
                ->route('password.request');
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_resets')
            ->where('email', session('reset_email'))
            ->delete();

        session()->forget([
            'reset_email',
            'code_verified'
        ]);

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Senha alterada com sucesso.'
            );
    }



    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
