<?php

namespace Tests\Feature;

use App\Models\BloodStock;
use App\Models\Donor;
use App\Models\DonorEvent;
use App\Models\EventAttendance;
use App\Models\HospitalRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DarahKitaaFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin PMI',
            'email' => 'admin@darahkita.id',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Flow 1: Dashboard: Ringkasan stok dan aktivitas (Umum)
     */
    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertOk();
    }

    /**
     * Flow 2: Data Donor & Aksi Admin: Verifikasi
     */
    public function test_admin_can_verify_donor()
    {
        $donor = Donor::create([
            'donor_code' => 'DNR-2026-999',
            'nik' => '3171000000000001',
            'name' => 'Budi Pendonor',
            'phone' => '08123456789',
            'blood_type' => 'O',
            'rhesus' => '+',
            'gender' => 'L',
            'birth_date' => '1995-05-15',
            'verification_status' => 'pending',
        ]);

        // Aksi Admin: Verifikasi
        $response = $this->actingAs($this->admin)->post(route('donors.verify', $donor), [
            'status' => 'verified',
            'notes' => 'Memenuhi semua kriteria skrining fisik dan Hb.',
            'systolic' => 120,
            'diastolic' => 80,
            'hemoglobin' => 14.2,
            'weight' => 68,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('donors', [
            'id' => $donor->id,
            'verification_status' => 'verified',
            'verified_by' => $this->admin->id,
        ]);
    }

    /**
     * Flow 3: Jadwal Donor (Event, kuota) & Aksi Admin: Absensi donor
     */
    public function test_admin_can_record_attendance_and_auto_increment_stock()
    {
        $donor = Donor::create([
            'donor_code' => 'DNR-2026-998',
            'nik' => '3171000000000002',
            'name' => 'Ani Wijaya',
            'phone' => '085711223344',
            'blood_type' => 'A',
            'rhesus' => '+',
            'gender' => 'P',
            'birth_date' => '1998-03-20',
            'verification_status' => 'verified',
        ]);

        $event = DonorEvent::create([
            'event_code' => 'EVT-2026-999',
            'title' => 'Aksi Donor Kampus',
            'organizer' => 'PMI',
            'location' => 'Aula Gedung A',
            'event_date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '12:00',
            'quota' => 50,
            'target_bags' => 40,
            'pic_name' => 'dr. Rangga',
            'pic_phone' => '081299998888',
        ]);

        $attendance = EventAttendance::create([
            'event_id' => $event->id,
            'donor_id' => $donor->id,
            'registration_code' => 'REG-EVT-999-01',
            'attendance_status' => 'registered',
            'screening_status' => 'pending',
        ]);

        // Aksi Admin: Absensi donor
        $response = $this->actingAs($this->admin)->post(route('events.attendance.record', $attendance), [
            'attendance_status' => 'present',
            'screening_status' => 'passed',
            'blood_pressure' => '118/76',
            'hemoglobin' => 13.2,
            'component_type' => 'WB',
            'notes' => 'Donor lancar 350ml.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('event_attendances', [
            'id' => $attendance->id,
            'attendance_status' => 'present',
            'screening_status' => 'passed',
        ]);

        // Verify blood stock incremented automatically!
        $this->assertDatabaseHas('blood_stocks', [
            'blood_type' => 'A',
            'rhesus' => '+',
            'component' => 'WB',
            'total_units' => 1,
        ]);

        // Verify donor history recorded
        $this->assertDatabaseHas('donor_histories', [
            'donor_id' => $donor->id,
            'screening_result' => 'passed',
        ]);
    }

    /**
     * Flow 4: Stok Darah (Per golongan) & Aksi Admin: Update stok
     */
    public function test_admin_can_update_stock_manually()
    {
        $response = $this->actingAs($this->admin)->post(route('stocks.update'), [
            'blood_type' => 'B',
            'rhesus' => '+',
            'component' => 'PRC',
            'mutation_type' => 'in',
            'quantity' => 5,
            'bag_code' => 'BAG-TEST-B-01',
            'notes' => 'Penerimaan donasi langsung UDD PMI',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('blood_stocks', [
            'blood_type' => 'B',
            'rhesus' => '+',
            'component' => 'PRC',
            'total_units' => 5,
        ]);

        $this->assertDatabaseHas('blood_stock_mutations', [
            'blood_type' => 'B',
            'rhesus' => '+',
            'type' => 'in',
            'quantity' => 5,
        ]);
    }

    /**
     * Flow 5: Permintaan (Rumah sakit) & Aksi Admin: Setujui / tolak
     */
    public function test_admin_can_approve_hospital_request_and_deduct_stock()
    {
        // Prepare 10 units of blood stock
        BloodStock::create([
            'blood_type' => 'O',
            'rhesus' => '+',
            'component' => 'WB',
            'total_units' => 10,
            'min_threshold' => 5,
        ]);

        $request = HospitalRequest::create([
            'request_code' => 'REQ-TEST-001',
            'hospital_name' => 'RSUP Cipto Mangunkusumo',
            'doctor_in_charge' => 'dr. Tri Sp.B',
            'contact_phone' => '021-3143707',
            'patient_name' => 'Tn. Joko Santoso',
            'medical_record_number' => 'RM-99001',
            'blood_type' => 'O',
            'rhesus' => '+',
            'component' => 'WB',
            'units_requested' => 3,
            'urgency' => 'emergency',
            'status' => 'pending',
        ]);

        // Aksi Admin: Setujui
        $response = $this->actingAs($this->admin)->post(route('requests.approve', $request), [
            'handover_recipient' => 'Ambulans RS Cipto - Bpk. Joko',
            'notes' => 'Kondisi coolbox dingin memenuhi standar.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('hospital_requests', [
            'id' => $request->id,
            'status' => 'approved',
            'action_by' => $this->admin->id,
        ]);

        // Verify stock deducted: 10 - 3 = 7
        $this->assertDatabaseHas('blood_stocks', [
            'blood_type' => 'O',
            'rhesus' => '+',
            'component' => 'WB',
            'total_units' => 7,
        ]);
    }

    public function test_admin_can_reject_hospital_request_with_reason()
    {
        $request = HospitalRequest::create([
            'request_code' => 'REQ-TEST-002',
            'hospital_name' => 'RS Fatmawati',
            'doctor_in_charge' => 'dr. Nabila',
            'contact_phone' => '021-7501524',
            'patient_name' => 'Ny. Maria',
            'medical_record_number' => 'RM-88221',
            'blood_type' => 'AB',
            'rhesus' => '-',
            'component' => 'TC',
            'units_requested' => 2,
            'urgency' => 'normal',
            'status' => 'pending',
        ]);

        // Aksi Admin: Tolak
        $response = $this->actingAs($this->admin)->post(route('requests.reject', $request), [
            'rejection_reason' => 'Stok trombosit AB negatif sedang kosong.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('hospital_requests', [
            'id' => $request->id,
            'status' => 'rejected',
            'rejection_reason' => 'Stok trombosit AB negatif sedang kosong.',
        ]);
    }

    /**
     * Flow 6: Laporan dan notifikasi: Ekspor data, kirim pengingat (Umum)
     */
    public function test_admin_can_send_reminder_and_export_csv()
    {
        $donor = Donor::create([
            'donor_code' => 'DNR-2026-997',
            'nik' => '3171000000000003',
            'name' => 'Rizky Pratama',
            'phone' => '081299887766',
            'blood_type' => 'B',
            'rhesus' => '+',
            'gender' => 'L',
            'birth_date' => '1990-01-01',
            'last_donation_date' => now()->subDays(80),
            'verification_status' => 'verified',
        ]);

        // Kirim Pengingat
        $response = $this->actingAs($this->admin)->post(route('reports.send-reminder'), [
            'donor_id' => $donor->id,
            'channel' => 'whatsapp',
            'custom_message' => 'Halo Kak Rizky, waktu donor darah Anda sudah tiba!',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reminder_notifications', [
            'donor_id' => $donor->id,
            'recipient_name' => 'Rizky Pratama',
            'channel' => 'whatsapp',
        ]);

        // Ekspor CSV
        $exportResponse = $this->actingAs($this->admin)->get(route('reports.export-csv', ['type' => 'donors']));
        $exportResponse->assertOk();
    }
}
