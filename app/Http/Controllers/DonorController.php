<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DonorController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Donor::with(['verifier', 'histories'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('donor_code', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('blood_type')) {
            $query->where('blood_type', $request->input('blood_type'));
        }

        if ($request->filled('status')) {
            $query->where('verification_status', $request->input('status'));
        }

        $donors = $query->paginate(10)->withQueryString();

        $counts = [
            'total' => Donor::count(),
            'verified' => Donor::where('verification_status', 'verified')->count(),
            'pending' => Donor::where('verification_status', 'pending')->count(),
            'suspended' => Donor::where('verification_status', 'suspended')->count(),
        ];

        return Inertia::render('donors/Index', [
            'donors' => $donors,
            'filters' => $request->only(['search', 'blood_type', 'status']),
            'counts' => $counts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:donors,nik',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'blood_type' => 'required|in:A,B,AB,O',
            'rhesus' => 'required|in:+,-',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'address' => 'nullable|string',
            'occupation' => 'nullable|string',
            'weight' => 'nullable|numeric|min:30',
            'systolic' => 'nullable|integer|min:70|max:220',
            'diastolic' => 'nullable|integer|min:40|max:140',
            'hemoglobin' => 'nullable|numeric|min:5|max:25',
        ]);

        $nextId = Donor::max('id') + 1;
        $donorCode = 'DNR-'.date('Y').'-'.str_pad((string) $nextId, 3, '0', STR_PAD_LEFT);

        Donor::create(array_merge($validated, [
            'donor_code' => $donorCode,
            'verification_status' => 'pending',
            'total_donations' => 0,
        ]));

        return back()->with('success', "Data calon donor {$validated['name']} berhasil ditambahkan dan siap diverifikasi.");
    }

    public function update(Request $request, Donor $donor): RedirectResponse
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:donors,nik,'.$donor->id,
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'blood_type' => 'required|in:A,B,AB,O',
            'rhesus' => 'required|in:+,-',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'address' => 'nullable|string',
            'occupation' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'systolic' => 'nullable|integer',
            'diastolic' => 'nullable|integer',
            'hemoglobin' => 'nullable|numeric',
        ]);

        $donor->update($validated);

        return back()->with('success', "Data profil {$donor->name} berhasil diperbarui.");
    }

    /**
     * Aksi Admin: Verifikasi Donor
     */
    public function verify(Request $request, Donor $donor): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,suspended',
            'notes' => 'nullable|string',
            'systolic' => 'nullable|integer',
            'diastolic' => 'nullable|integer',
            'hemoglobin' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
        ]);

        $updateData = [
            'verification_status' => $validated['status'],
            'verification_notes' => $validated['notes'] ?? null,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ];

        if (isset($validated['systolic'])) {
            $updateData['systolic'] = $validated['systolic'];
        }
        if (isset($validated['diastolic'])) {
            $updateData['diastolic'] = $validated['diastolic'];
        }
        if (isset($validated['hemoglobin'])) {
            $updateData['hemoglobin'] = $validated['hemoglobin'];
        }
        if (isset($validated['weight'])) {
            $updateData['weight'] = $validated['weight'];
        }

        $donor->update($updateData);

        $statusText = $validated['status'] === 'verified' ? 'TERVERIFIKASI (Layak Donor)' : 'DITANGGUHKAN';

        return back()->with('success', "Status donor {$donor->name} berhasil diubah menjadi {$statusText}.");
    }

    public function destroy(Donor $donor): RedirectResponse
    {
        $donor->delete();

        return back()->with('success', "Data donor {$donor->name} berhasil dihapus.");
    }
}
