<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordRecoveryUpdateRequest;
use App\Models\PasswordRecovery;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasswordRecoveryController extends Controller
{
    public function edit(): View
    {
        $recovery = $this->currentRecovery();

        return view('AdminPertanyaan.AdminPageEditPertanyaan', [
            'first_question' => $recovery->first_question,
            'second_question' => $recovery->second_question,
            'first_answer' => $recovery->first_answer,
            'second_answer' => $recovery->second_answer,
        ]);
    }

    public function update(PasswordRecoveryUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->currentRecovery()->update([
            'first_question' => $validated['first_question'],
            'second_question' => $validated['second_question'],
            'first_answer' => strtolower($validated['first_answer']),
            'second_answer' => strtolower($validated['second_answer']),
        ]);

        return redirect()->route('admin.password-recovery.edit')->with('success', 'Pertanyaan berhasil diupdate!');
    }

    private function currentRecovery(): PasswordRecovery
    {
        return PasswordRecovery::query()->where('user_id', request()->user()->id)->firstOrFail();
    }
}
