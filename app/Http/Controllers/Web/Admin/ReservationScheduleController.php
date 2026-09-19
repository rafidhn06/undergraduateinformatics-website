<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReservationSchedule;
use App\Services\Reservation\BeritaAcaraPdfGenerator;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ReservationScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $reservationTableReady = Schema::hasTable('reservation_schedules');
        $reservationDetailsReady = $reservationTableReady
            && Schema::hasColumn('reservation_schedules', 'meeting_room');
        $search = trim((string) $request->get('search', ''));

        $reservations = collect();
        if ($reservationTableReady) {
            $query = ReservationSchedule::latest();
            if ($search !== '') {
                $query->where(function ($inner) use ($search) {
                    $inner->where('requested_by', 'like', '%' . $search . '%')
                        ->orWhere('meeting_room', 'like', '%' . $search . '%')
                        ->orWhere('study_program', 'like', '%' . $search . '%')
                        ->orWhere('date', 'like', '%' . $search . '%')
                        ->orWhere('shift', 'like', '%' . $search . '%')
                        ->orWhere('agenda', 'like', '%' . $search . '%');
                });
            }
            $reservations = $query->paginate(10)->withQueryString();
        }

        return view('AdminDashboard.reservation', [
            'reservationTableReady' => $reservationTableReady,
            'reservationDetailsReady' => $reservationDetailsReady,
            'reservations' => $reservations,
        ]);
    }

    public function create(): View
    {
        return view('AdminDashboard.reservation-create');
    }

    public function edit(ReservationSchedule $reservationSchedule): View
    {
        return view('AdminDashboard.reservation-edit', ['reservation' => $reservationSchedule]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $this->normalize($validator->validated());

        $isConflict = ReservationSchedule::where('date', $data['date'])
            ->where('shift', $data['shift'])
            ->exists();

        if ($isConflict) {
            return back()->withErrors([
                'date' => 'Jadwal pada tanggal dan sesi ini sudah terisi. Silakan pilih tanggal atau sesi lain.',
            ])->withInput();
        }

        $schedule = ReservationSchedule::create($data);

        $documentLink = app(BeritaAcaraPdfGenerator::class)->generate($schedule);

        if ($documentLink) {
            $schedule->document_link = $documentLink;
            $schedule->save();
        }

        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi berhasil dibuat.');
    }

    public function update(Request $request, ReservationSchedule $reservationSchedule): RedirectResponse
    {
        $validator = Validator::make($request->all(), $this->rules($reservationSchedule));

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $this->normalize($validator->validated());

        $checkDate = $data['date'] ?? $reservationSchedule->date;
        $checkShift = $data['shift'] ?? $reservationSchedule->shift;

        $isConflict = ReservationSchedule::where('date', $checkDate)
            ->where('shift', $checkShift)
            ->where('id', '!=', $reservationSchedule->id)
            ->exists();

        if ($isConflict) {
            return back()->withErrors([
                'date' => 'Jadwal pada tanggal dan sesi ini sudah terisi oleh reservasi lain. Silakan pilih tanggal atau sesi lain.',
            ])->withInput();
        }

        $oldDocumentLink = $reservationSchedule->document_link;
        $reservationSchedule->update($data);

        $documentLink = app(BeritaAcaraPdfGenerator::class)->generate($reservationSchedule);

        if ($documentLink) {
            if ($oldDocumentLink) {
                $urlPath = parse_url($oldDocumentLink, PHP_URL_PATH);
                if ($urlPath) {
                    $oldFileName = basename($urlPath);
                    $oldPath = public_path('beritaacara/'.$oldFileName);
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }

            $reservationSchedule->document_link = $documentLink;
            $reservationSchedule->save();
        }

        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi berhasil diperbarui.');
    }

    public function destroy(ReservationSchedule $reservationSchedule): RedirectResponse
    {
        if ($reservationSchedule->document_link) {
            $urlPath = parse_url($reservationSchedule->document_link, PHP_URL_PATH);
            if ($urlPath) {
                $oldFileName = basename($urlPath);
                $oldPath = public_path('beritaacara/'.$oldFileName);
                if (file_exists($oldPath) && is_file($oldPath)) {
                    unlink($oldPath);
                }
            }
        }

        $reservationSchedule->delete();

        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi berhasil dihapus.');
    }

    private function rules(?ReservationSchedule $schedule = null): array
    {
        $sometimes = $schedule ? 'sometimes|' : '';
        $datePrefix = $schedule ? ['sometimes'] : [];

        return [
            'date' => array_merge($datePrefix, [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $day = Carbon::parse($value)->dayOfWeekIso;
                    if (! in_array($day, [1, 2, 4, 5])) {
                        $fail('Tanggal harus hari Senin, Selasa, Kamis, atau Jumat.');
                    }
                },
            ]),
            'shift' => $sometimes.'required|in:09:00,13:00,15:00,09:00:00,13:00:00,15:00:00',
            'requested_by' => $sometimes.'required|string|max:255',
            'document_link' => 'nullable|string|url|max:255',
            'meeting_room' => 'nullable|string|max:255',
            'study_program' => 'nullable|string|max:255',
            'participants' => 'nullable|string|max:255',
            'agenda' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'prodi_signature_name' => 'nullable|string|max:255',
            'prodi_signature_position' => 'nullable|string|max:255',
            'related_party_signature_name' => 'nullable|string|max:255',
            'related_party_signature_position' => 'nullable|string|max:255',
        ];
    }

    private function normalize(array $data): array
    {
        if (isset($data['shift'])) {
            $shift = $data['shift'];
            if (strlen($shift) === 5) {
                $data['shift'] = $shift.':00';
            }
        }

        return $data;
    }
}
