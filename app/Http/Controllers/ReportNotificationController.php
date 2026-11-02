<?php

namespace App\Http\Controllers;

use App\Models\BloodStock;
use App\Models\Donor;
use App\Models\DonorEvent;
use App\Models\HospitalRequest;
use App\Models\ReminderNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportNotificationController extends Controller
{
    public function index(Request $request): Response
    {
        // Donors eligible for recall (> 60 days or never donated)
        $eligibleDonors = Donor::where('verification_status', 'verified')
            ->where(function ($q) {
                $q->whereNull('last_donation_date')
                    ->orWhere('last_donation_date', '<=', Carbon::now()->subDays(60));
            })
            ->latest('last_donation_date')
            ->take(15)
            ->get();

        // Notification history
        $notifications = ReminderNotification::with(['donor', 'sender'])
            ->latest()
            ->paginate(10);

        // Stats summary for reports
        $reportStats = [
            'total_donors' => Donor::count(),
            'total_stock_bags' => BloodStock::sum('total_units'),
            'total_requests' => HospitalRequest::count(),
            'approved_requests' => HospitalRequest::where('status', 'approved')->count(),
            'completed_events' => DonorEvent::where('status', 'completed')->count(),
            'notifications_sent' => ReminderNotification::where('status', 'sent')->count(),
            'donors_ready_recall' => Donor::where('verification_status', 'verified')
                ->where(function ($q) {
                    $q->whereNull('last_donation_date')
                        ->orWhere('last_donation_date', '<=', Carbon::now()->subDays(60));
                })->count(),
        ];

        return Inertia::render('reports/Index', [
            'eligibleDonors' => $eligibleDonors,
            'notifications' => $notifications,
            'reportStats' => $reportStats,
        ]);
    }

    /**
     * Kirim Pengingat Individual ke Pendonor
     */
    public function sendReminder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'channel' => 'required|in:whatsapp,email,sms',
            'custom_message' => 'nullable|string',
        ]);

        $donor = Donor::findOrFail($validated['donor_id']);

        $message = $validated['custom_message'] ??
            "Halo {$donor->name}, salam kemanusiaan dari DarahKitaa (PMI). Waktu donor darah Anda telah tiba (terakhir donor: ".
            ($donor->last_donation_date ? Carbon::parse($donor->last_donation_date)->format('d M Y') : 'belum pernah').
            '). Tubuh Anda siap kembali menyelamatkan nyawa mereka yang membutuhkan. Ayo donor darah hari ini!';

        $notification = ReminderNotification::create([
            'recipient_type' => 'donor',
            'recipient_name' => $donor->name,
            'recipient_contact' => $donor->phone,
            'type' => 'donor_recall',
            'title' => 'Pengingat Waktu Donor Darah Kembali',
            'message' => $message,
            'channel' => $validated['channel'],
            'status' => 'sent',
            'donor_id' => $donor->id,
            'sent_at' => now(),
            'sender_id' => auth()->id(),
        ]);

        // Clean phone for whatsapp
        $cleanPhone = preg_replace('/[^0-9]/', '', $donor->phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62'.substr($cleanPhone, 1);
        }
        $waLink = "https://wa.me/{$cleanPhone}?text=".rawurlencode($message);

        return back()->with([
            'success' => "Pengingat berhasil dicatat dan dikirimkan ke {$donor->name}.",
            'waLink' => $waLink,
        ]);
    }

    /**
     * Kirim Pengingat Massal (Broadcast)
     */
    public function broadcastReminder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'blood_type' => 'nullable|in:A,B,AB,O',
            'message' => 'required|string|max:500',
        ]);

        $query = Donor::where('verification_status', 'verified')
            ->where(function ($q) {
                $q->whereNull('last_donation_date')
                    ->orWhere('last_donation_date', '<=', Carbon::now()->subDays(60));
            });

        if (! empty($validated['blood_type'])) {
            $query->where('blood_type', $validated['blood_type']);
        }

        $donors = $query->take(50)->get();
        $count = 0;

        foreach ($donors as $donor) {
            ReminderNotification::create([
                'recipient_type' => 'donor',
                'recipient_name' => $donor->name,
                'recipient_contact' => $donor->phone,
                'type' => 'donor_recall_broadcast',
                'title' => 'Panggilan Donor Darah Massal PMI',
                'message' => $validated['message'],
                'channel' => 'whatsapp',
                'status' => 'sent',
                'donor_id' => $donor->id,
                'sent_at' => now(),
                'sender_id' => auth()->id(),
            ]);
            $count++;
        }

        return back()->with('success', "Pengingat massal berhasil dikirimkan ke {$count} pendonor yang telah memenuhi syarat.");
    }

    /**
     * Ekspor Data CSV
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'donors');
        $filename = "darahkitaa-{$type}-".date('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($type) {
            $output = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($type === 'donors') {
                fputcsv($output, ['Kode Donor', 'NIK', 'Nama Lengkap', 'Golongan Darah', 'Rhesus', 'Jenis Kelamin', 'No WhatsApp', 'Status Verifikasi', 'Total Donor', 'Donor Terakhir']);
                Donor::chunk(100, function ($donors) use ($output) {
                    foreach ($donors as $d) {
                        fputcsv($output, [
                            $d->donor_code,
                            "'{$d->nik}'",
                            $d->name,
                            $d->blood_type,
                            $d->rhesus,
                            $d->gender,
                            $d->phone,
                            $d->verification_status,
                            $d->total_donations,
                            $d->last_donation_date ? $d->last_donation_date->format('Y-m-d') : '-',
                        ]);
                    }
                });
            } elseif ($type === 'stocks') {
                fputcsv($output, ['Golongan Darah', 'Rhesus', 'Komponen', 'Total Kantong (Unit)', 'Ambang Batas Minimum', 'Status Level']);
                BloodStock::chunk(100, function ($stocks) use ($output) {
                    foreach ($stocks as $s) {
                        fputcsv($output, [
                            $s->blood_type,
                            $s->rhesus,
                            $s->component,
                            $s->total_units,
                            $s->min_threshold,
                            $s->status_level,
                        ]);
                    }
                });
            } elseif ($type === 'requests') {
                fputcsv($output, ['Kode Permintaan', 'Nama Rumah Sakit', 'Pasien', 'Gol Darah', 'Komponen', 'Jumlah Kantong', 'Urgensi', 'Status', 'Tanggal Dibuat']);
                HospitalRequest::chunk(100, function ($reqs) use ($output) {
                    foreach ($reqs as $r) {
                        fputcsv($output, [
                            $r->request_code,
                            $r->hospital_name,
                            $r->patient_name,
                            $r->blood_type_full,
                            $r->component,
                            $r->units_requested,
                            $r->urgency,
                            $r->status,
                            $r->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
