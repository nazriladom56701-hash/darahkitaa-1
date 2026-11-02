<?php

namespace App\Http\Controllers;

use App\Models\BloodStock;
use App\Models\BloodStockMutation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BloodStockController extends Controller
{
    public function index(Request $request): Response
    {
        $stocks = BloodStock::orderBy('blood_type')->orderBy('rhesus')->get();

        $mutations = BloodStockMutation::with('officer')
            ->latest()
            ->paginate(10);

        // Calculate summary cards
        $totalUnits = $stocks->sum('total_units');
        $criticalStocks = $stocks->filter(fn ($s) => $s->status_level === 'critical')->count();
        $warningStocks = $stocks->filter(fn ($s) => $s->status_level === 'warning')->count();
        $safeStocks = $stocks->filter(fn ($s) => $s->status_level === 'safe')->count();

        // Blood groups overview
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $groupedStocks = [];

        foreach ($bloodGroups as $bg) {
            $type = substr($bg, 0, -1);
            $rhesus = substr($bg, -1);
            $items = $stocks->where('blood_type', $type)->where('rhesus', $rhesus);

            $groupedStocks[] = [
                'blood_type' => $type,
                'rhesus' => $rhesus,
                'blood_type_full' => $bg,
                'total_units' => $items->sum('total_units'),
                'min_threshold' => $items->max('min_threshold') ?? 10,
                'components' => $items->pluck('total_units', 'component')->toArray(),
            ];
        }

        return Inertia::render('stocks/Index', [
            'stocks' => $stocks,
            'groupedStocks' => $groupedStocks,
            'mutations' => $mutations,
            'stats' => [
                'total_units' => $totalUnits,
                'critical_count' => $criticalStocks,
                'warning_count' => $warningStocks,
                'safe_count' => $safeStocks,
            ],
        ]);
    }

    /**
     * Aksi Admin: Update Stok
     */
    public function updateStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'blood_type' => 'required|in:A,B,AB,O',
            'rhesus' => 'required|in:+,-',
            'component' => 'required|in:WB,PRC,TC,FFP',
            'mutation_type' => 'required|in:in,out,expired,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'required|string|max:255',
            'bag_code' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated) {
            $stock = BloodStock::firstOrCreate(
                [
                    'blood_type' => $validated['blood_type'],
                    'rhesus' => $validated['rhesus'],
                    'component' => $validated['component'],
                ],
                [
                    'total_units' => 0,
                    'min_threshold' => 10,
                ]
            );

            $qty = $validated['quantity'];
            $signedQty = $qty;

            if (in_array($validated['mutation_type'], ['out', 'expired'])) {
                if ($stock->total_units < $qty) {
                    abort(422, "Jumlah stok saat ini ({$stock->total_units}) tidak mencukupi untuk pengurangan {$qty} unit.");
                }
                $signedQty = -$qty;
                $stock->decrement('total_units', $qty);
            } elseif ($validated['mutation_type'] === 'in') {
                $stock->increment('total_units', $qty);
            } elseif ($validated['mutation_type'] === 'adjustment') {
                // Adjustment sets exact total
                $diff = $qty - $stock->total_units;
                $signedQty = $diff;
                $stock->update(['total_units' => $qty]);
            }

            BloodStockMutation::create([
                'blood_type' => $validated['blood_type'],
                'rhesus' => $validated['rhesus'],
                'component' => $validated['component'],
                'type' => $validated['mutation_type'],
                'quantity' => $signedQty,
                'bag_code' => $validated['bag_code'] ?? null,
                'reference_type' => 'manual_admin_update',
                'notes' => $validated['notes'],
                'officer_id' => auth()->id(),
            ]);
        });

        return back()->with('success', "Update stok kantong darah {$validated['blood_type']}{$validated['rhesus']} ({$validated['component']}) berhasil disimpan.");
    }
}
