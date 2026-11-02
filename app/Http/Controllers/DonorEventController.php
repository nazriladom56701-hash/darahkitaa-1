<?php

namespace App\Http\Controllers;

use App\Models\BloodStock;
use App\Models\BloodStockMutation;
use App\Models\Donor;
use App\Models\DonorEvent;
use App\Models\DonorHistory;
use App\Models\EventAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DonorEventController extends Controller
{
    public function index(Request $request): Response
    {
        $query = DonorEvent::withCount(['attendances as registered_count', 'attendances as present_count' => function ($q) {
            $q->where('attendance_status', 'present');
        }, 'attendances as passed_count' => function ($q) {
            $q->where('screening_status', 'passed');
        }])->orderBy('event_date', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('organizer', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate(8)->withQueryString();

        $counts = [
            'total' => DonorEvent::count(),
            'upcoming' => DonorEvent::where('status', 'upcoming')->count(),
            'ongoing' => DonorEvent::where('status', 'ongoing')->count(),
            'completed' => DonorEvent::where('status', 'completed')->count(),
        ];

        return Inertia::render('events/Index', [
            'events' => $events,
            'filters' => $request->only(['search', 'status']),
            'counts' => $counts,
        ]);
    }

    public function show(DonorEvent $event): Response
    {
        $event->load(['attendances.donor', 'attendances.officer']);

        $availableDonors = Donor::where('verification_status', '!=', 'suspended')
            ->whereNotIn('id', $event->attendances->pluck('donor_id'))
            ->select('id', 'name', 'donor_code', 'blood_type', 'rhesus', 'phone')
            ->get();

        return Inertia::render('events/Show', [
            'event' => $event,
            'availableDonors' => $availableDonors,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'address' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'quota' => 'required|integer|min:1',
            'target_bags' => 'required|integer|min:1',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        $nextId = DonorEvent::max('id') + 1;
        $eventCode = 'EVT-'.date('Y').'-'.str_pad((string) $nextId, 3, '0', STR_PAD_LEFT);

        DonorEvent::create(array_merge($validated, [
            'event_code' => $eventCode,
            'status' => 'upcoming',
        ]));

        return back()->with('success', "Jadwal event {$validated['title']} berhasil dibuat.");
    }

    public function update(Request $request, DonorEvent $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'address' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'quota' => 'required|integer|min:1',
            'target_bags' => 'required|integer|min:1',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:20',
            'description' => 'nullable|string',
        ]);

        $event->update($validated);

        return back()->with('success', "Data event {$event->title} berhasil diperbarui.");
    }

    /**
     * Aksi Admin: Absensi Donor
     */
    public function recordAttendance(Request $request, EventAttendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'attendance_status' => 'required|in:registered,present,absent',
            'screening_status' => 'required|in:pending,passed,failed',
            'blood_pressure' => 'nullable|string',
            'hemoglobin' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'component_type' => 'nullable|in:WB,PRC,TC,FFP',
        ]);

        DB::transaction(function () use ($attendance, $validated) {
            $prevStatus = $attendance->screening_status;
            $componentType = $validated['component_type'] ?? 'WB';

            $attendanceData = [
                'attendance_status' => $validated['attendance_status'],
                'screening_status' => $validated['screening_status'],
                'blood_pressure' => $validated['blood_pressure'] ?? null,
                'hemoglobin' => $validated['hemoglobin'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'attended_at' => now(),
                'officer_id' => auth()->id(),
            ];

            // If passed and was not previously passed, create donation record & add blood stock!
            if ($validated['screening_status'] === 'passed' && $prevStatus !== 'passed') {
                $donor = $attendance->donor;
                $bagCode = 'BAG-'.$donor->blood_type.($donor->rhesus === '+' ? 'POS' : 'NEG').'-'.date('Ymd').'-'.rand(100, 999);
                $attendanceData['blood_bag_code'] = $bagCode;

                // 1. Create Donor History
                DonorHistory::create([
                    'donor_id' => $donor->id,
                    'event_id' => $attendance->event_id,
                    'donation_date' => now()->toDateString(),
                    'blood_type' => $donor->blood_type,
                    'rhesus' => $donor->rhesus,
                    'component_type' => $componentType,
                    'volume_ml' => 350,
                    'blood_bag_code' => $bagCode,
                    'hemoglobin' => $validated['hemoglobin'] ?? $donor->hemoglobin,
                    'blood_pressure' => $validated['blood_pressure'] ?? "{$donor->systolic}/{$donor->diastolic}",
                    'screening_result' => 'passed',
                    'notes' => "Donasi pada event {$attendance->event->title}. ".($validated['notes'] ?? ''),
                    'officer_id' => auth()->id(),
                ]);

                // 2. Update Donor stats
                $donor->update([
                    'last_donation_date' => now()->toDateString(),
                    'total_donations' => $donor->total_donations + 1,
                    'hemoglobin' => $validated['hemoglobin'] ?? $donor->hemoglobin,
                ]);

                // 3. Add to Blood Stock
                $stock = BloodStock::firstOrCreate(
                    [
                        'blood_type' => $donor->blood_type,
                        'rhesus' => $donor->rhesus,
                        'component' => $componentType,
                    ],
                    [
                        'total_units' => 0,
                        'min_threshold' => 10,
                    ]
                );
                $stock->increment('total_units', 1);

                // 4. Log Mutation
                BloodStockMutation::create([
                    'blood_type' => $donor->blood_type,
                    'rhesus' => $donor->rhesus,
                    'component' => $componentType,
                    'type' => 'in',
                    'quantity' => 1,
                    'bag_code' => $bagCode,
                    'reference_type' => 'donation_event',
                    'reference_id' => $attendance->event_id,
                    'notes' => "Donor darah dari {$donor->name} pada event {$attendance->event->title}",
                    'officer_id' => auth()->id(),
                ]);
            }

            $attendance->update($attendanceData);
        });

        $donorName = $attendance->donor->name;
        $statusMsg = $validated['attendance_status'] === 'present'
            ? ($validated['screening_status'] === 'passed' ? 'Hadir & Lolos Skrining (Stok Darah +1 Berhasil Dicatat)' : 'Hadir (Gagal Skrining)')
            : 'Tercatat Tidak Hadir';

        return back()->with('success', "Absensi donor {$donorName} berhasil diperbarui: {$statusMsg}.");
    }

    public function addParticipant(Request $request, DonorEvent $event): RedirectResponse
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'notes' => 'nullable|string',
        ]);

        $regCode = 'REG-'.$event->id.'-'.rand(1000, 9999);

        EventAttendance::create([
            'event_id' => $event->id,
            'donor_id' => $validated['donor_id'],
            'registration_code' => $regCode,
            'attendance_status' => 'registered',
            'screening_status' => 'pending',
            'notes' => $validated['notes'] ?? 'Didaftarkan oleh admin di lokasi event',
        ]);

        return back()->with('success', 'Peserta berhasil didaftarkan ke event.');
    }
}
