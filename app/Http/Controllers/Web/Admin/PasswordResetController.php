<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordResetStoreRequest;
use App\Http\Requests\Admin\PasswordResetUpdateRequest;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('LupaPasswordPage');
    }

    public function store(PasswordResetStoreRequest $request): RedirectResponse
    {
        $user = User::query()->where('email', $request->validated()['email'])->firstOrFail();
        PasswordReset::query()->where('expires_at', '<', now())->delete();
        PasswordReset::query()->where('user_id', $user->id)->delete();
        $reset = PasswordReset::query()->create([
            'user_id' => $user->id,
            'token' => Str::random(48),
            'stage' => 'questions',
            'expires_at' => now()->addHour(),
        ]);

        return redirect()->route('admin.password-resets.edit', $reset);
    }

    public function edit(PasswordReset $passwordReset): View
    {
        abort_if($passwordReset->expires_at->isPast(), 410);

        if ($passwordReset->stage === 'new_password') {
            return view('GantiPasswordPage', ['reset' => $passwordReset]);
        }

        return view('PertanyaanLupaPassword', ['reset' => $passwordReset, 'user' => $passwordReset->user]);
    }

    public function update(PasswordResetUpdateRequest $request, PasswordReset $passwordReset): RedirectResponse
    {
        abort_if($passwordReset->expires_at->isPast(), 410);
        $validated = $request->validated();

        if ($passwordReset->stage === 'questions') {
            $user = $passwordReset->user;
            $first = strtolower((string) $user->password_recovery->first_answer);
            $second = strtolower((string) $user->password_recovery->second_answer);

            if ($first !== strtolower((string) $validated['first_answer']) || $second !== strtolower((string) $validated['second_answer'])) {
                return back()->with('error', 'Jawaban tidak sesuai!');
            }

            $passwordReset->update(['stage' => 'new_password']);

            return redirect()->route('admin.password-resets.edit', $passwordReset);
        }

        $passwordReset->user->update(['password' => $validated['new_password']]);
        $passwordReset->delete();

        return redirect()->route('admin.login')->with('success', 'Password berhasil diganti!');
    }
}
