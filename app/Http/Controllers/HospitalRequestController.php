<?php

namespace App\Http\Controllers;

use App\Models\BloodStock;
use App\Models\BloodStockMutation;
use App\Models\HospitalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class HospitalRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $query = HospitalRequest::with('officer')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->input('urgency'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('hospital_name', 'like', "%{$search}%")
                    ->orWhere('patient_name', 'like', "%{$search}%")
                    ->orWhere('request_code', 'like', "%{$search}%")
                    ->orWhere('medical_record_number', 'like', "%{$search}%");
            });
        }

        $requests = $query->paginate(10)->withQueryString();

        $counts = [
            'total' => HospitalRequest::count(),
            'pending' => HospitalRequest::where('status', 'pending')->count(),
            'approved' => HospitalRequest::where('status', 'approved')->count(),
            'rejected' => HospitalRequest::where('status', 'rejected')->count(),
            'completed' => HospitalRequest::where('status', 'completed')->count(),
            'emergency' => HospitalRequest::where('status', 'pending')->where('urgency', 'emergency')->count(),
        ];

        // Also fetch current stocks so the admin modal can display current stock availability
        $currentStocks = BloodStock::all()
            ->keyBy(fn ($item) => "{$item->blood_type}_{$item->rhesus}_{$item->component}")
            ->map(fn ($item) => $item->total_units);

        return Inertia::render('requests/Index', [
            'requests' => $requests,
            'filters' => $request->only(['search', 'status', 'urgency']),
            'counts' => $counts,
            'currentStocks' => $currentStocks,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hospital_name' => 'required|string|max:255',
            'doctor_in_charge' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:50',
            'patient_name' => 'required|string|max:255',
            'medical_record_number' => 'required|string|max:100',
            'blood_type' => 'required|in:A,B,AB,O',
            'rhesus' => 'required|in:+,-',
            'component' => 'required|in:WB,PRC,TC,FFP',
            'units_requested' => 'required|integer|min:1',
            'urgency' => 'required|in:normal,urgent,emergency',
            'diagnosis_notes' => 'nullable|string',
        ]);

        $nextId = HospitalRequest::max('id') + 1;
        $requestCode = 'REQ-RS-'.date('Y').'-'.str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);

        HospitalRequest::create(array_merge($validated, [
            'request_code' => $requestCode,
            'status' => 'pending',
        ]));

        return back()->with('success', "Permintaan darah dari {$validated['hospital_name']} berhasil dicatat dengan kode {$requestCode}.");
    }

    /**
     * Aksi Admin: Setujui Permintaan
     */
    public function approve(Request $request, HospitalRequest $hospitalRequest): RedirectResponse
    {
        $validated = $request->validate([
            'handover_recipient' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($hospitalRequest, $validated) {
            $stock = BloodStock::where('blood_type', $hospitalRequest->blood_type)
                ->where('rhesus', $hospitalRequest->rhesus)
                ->where('component', $hospitalRequest->component)
                ->first();

            $available = $stock ? $stock->total_units : 0;
            if ($available < $hospitalRequest->units_requested) {
                abort(422, "Stok kantong darah {$hospitalRequest->blood_type_full} ({$hospitalRequest->component}) tidak mencukupi. Tersedia: {$available} kantong, diminta: {$hospitalRequest->units_requested} kantong.");
            }

            // Deduct blood stock
            $stock->decrement('total_units', $hospitalRequest->units_requested);

            // Create mutation record
            BloodStockMutation::create([
                'blood_type' => $hospitalRequest->blood_type,
                'rhesus' => $hospitalRequest->rhesus,
                'component' => $hospitalRequest->component,
                'type' => 'out',
                'quantity' => -$hospitalRequest->units_requested,
                'reference_type' => 'hospital_request',
                'reference_id' => $hospitalRequest->id,
                'notes' => "Distribusi darah ke {$hospitalRequest->hospital_name} untuk pasien {$hospitalRequest->patient_name}. ".($validated['notes'] ?? ''),
                'officer_id' => auth()->id(),
            ]);

            // Update request
            $hospitalRequest->update([
                'status' => 'approved',
                'handover_recipient' => $validated['handover_recipient'] ?? 'Petugas Ambulans / Kurir RS',
                'action_by' => auth()->id(),
                'action_at' => now(),
            ]);
        });

        return back()->with('success', "Permintaan darah {$hospitalRequest->request_code} dari {$hospitalRequest->hospital_name} DISAPROVE/DISETUJUI. Stok inventaris otomatis terpotong.");
    }

    /**
     * Aksi Admin: Tolak Permintaan
     */
    public function reject(Request $request, HospitalRequest $hospitalRequest): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $hospitalRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'action_by' => auth()->id(),
            'action_at' => now(),
        ]);

        return back()->with('success', "Permintaan darah {$hospitalRequest->request_code} DITOLAK dengan alasan tersimpan.");
    }

    public function complete(Request $request, HospitalRequest $hospitalRequest): RedirectResponse
    {
        $hospitalRequest->update([
            'status' => 'completed',
        ]);

        return back()->with('success', "Permintaan darah {$hospitalRequest->request_code} telah diserahterimakan dan berstatus SELESAI.");
    }
}
