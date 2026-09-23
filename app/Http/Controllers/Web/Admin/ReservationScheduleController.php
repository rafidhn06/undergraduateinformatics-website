<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\Shift;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReservationScheduleStoreRequest;
use App\Http\Requests\Admin\ReservationScheduleUpdateRequest;
use App\Models\ReservationSchedule;
use App\Services\Reservation\BeritaAcaraPdfGenerator;
use App\Services\Reservation\ReservationDocumentException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReservationScheduleController extends Controller
{
    public function __construct(private readonly BeritaAcaraPdfGenerator $documents)
    {
    }

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

        return view('admin.dashboard.reservation', [
            'reservationTableReady' => $reservationTableReady,
            'reservationDetailsReady' => $reservationDetailsReady,
            'reservations' => $reservations,
        ]);
    }

    public function create(): View
    {
        return view('admin.dashboard.reservation-create');
    }

    public function edit(ReservationSchedule $reservationSchedule): View
    {
        return view('admin.dashboard.reservation-edit', ['reservation' => $reservationSchedule]);
    }

    public function store(ReservationScheduleStoreRequest $request): RedirectResponse
    {
        $data = $this->normalize($request->validated());

        $isConflict = ReservationSchedule::where('date', $data['date'])
            ->where('shift', $data['shift'])
            ->exists();

        if ($isConflict) {
            return back()->withErrors([
                'date' => 'Jadwal pada tanggal dan sesi ini sudah terisi. Silakan pilih tanggal atau sesi lain.',
            ])->withInput();
        }

        $schedule = ReservationSchedule::create($data);

        try {
            $this->documents->generate($schedule);
        } catch (ReservationDocumentException $e) {
            return redirect()->route('admin.reservations.index')
                ->with('success', 'Reservasi berhasil dibuat.')
                ->with('error', $e->getMessage());
        }

        $schedule->document_link = route('admin.reservations.document', $schedule);
        $schedule->save();

        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi berhasil dibuat.');
    }

    public function update(ReservationScheduleUpdateRequest $request, ReservationSchedule $reservationSchedule): RedirectResponse
    {
        $data = $this->normalize($request->validated());

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

        try {
            $this->documents->generate($reservationSchedule);
        } catch (ReservationDocumentException $e) {
            return redirect()->route('admin.reservations.index')
                ->with('success', 'Reservasi berhasil diperbarui.')
                ->with('error', $e->getMessage());
        }

        $this->deleteLegacyDocumentFile($oldDocumentLink);

        $reservationSchedule->document_link = route('admin.reservations.document', $reservationSchedule);
        $reservationSchedule->save();

        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi berhasil diperbarui.');
    }

    public function destroy(ReservationSchedule $reservationSchedule): RedirectResponse
    {
        $this->deleteDocumentFile($reservationSchedule);

        $reservationSchedule->delete();

        return redirect()->route('admin.reservations.index')->with('success', 'Reservasi berhasil dihapus.');
    }

    public function showDocument(ReservationSchedule $reservationSchedule): BinaryFileResponse|RedirectResponse
    {
        $path = 'beritaacara/berita_acara_' . $reservationSchedule->id . '.pdf';

        if (Storage::disk('local')->exists($path)) {
            return response()->file(Storage::disk('local')->path($path));
        }

        if ($reservationSchedule->document_link) {
            $urlPath = parse_url($reservationSchedule->document_link, PHP_URL_PATH);
            if ($urlPath) {
                $oldPath = public_path('beritaacara/' . basename($urlPath));
                if (file_exists($oldPath) && is_file($oldPath)) {
                    return redirect($reservationSchedule->document_link);
                }
            }
        }

        abort(404);
    }

    private function deleteDocumentFile(ReservationSchedule $reservationSchedule): void
    {
        Storage::disk('local')->delete('beritaacara/berita_acara_' . $reservationSchedule->id . '.pdf');
        $this->deleteLegacyDocumentFile($reservationSchedule->document_link);
    }

    private function deleteLegacyDocumentFile(?string $documentLink): void
    {
        if ($documentLink) {
            $urlPath = parse_url($documentLink, PHP_URL_PATH);
            if ($urlPath) {
                $oldPath = public_path('beritaacara/' . basename($urlPath));
                if (file_exists($oldPath) && is_file($oldPath)) {
                    unlink($oldPath);
                }
            }
        }
    }

    private function normalize(array $data): array
    {
        if (isset($data['shift'])) {
            $data['shift'] = Shift::normalizeString($data['shift']);
        }

        return $data;
    }
}
