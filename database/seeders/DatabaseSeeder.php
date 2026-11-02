<?php

namespace Database\Seeders;

use App\Models\BloodStock;
use App\Models\BloodStockMutation;
use App\Models\Donor;
use App\Models\DonorEvent;
use App\Models\DonorHistory;
use App\Models\EventAttendance;
use App\Models\HospitalRequest;
use App\Models\ReminderNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@darahkita.id'],
            [
                'name' => 'dr. Sarah Amelia (Admin PMI)',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'email_verified_at' => now(),
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'petugas@darahkita.id'],
            [
                'name' => 'Budi Santoso (Petugas Lapangan)',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '081298765432',
                'email_verified_at' => now(),
            ]
        );

        // 2. Blood Stocks Initialization
        $bloodTypes = ['A', 'B', 'AB', 'O'];
        $rhesuses = ['+', '-'];
        $components = ['WB', 'PRC', 'TC', 'FFP'];

        // Initial default stock numbers
        $stockMatrix = [
            'A+' => ['WB' => 28, 'PRC' => 35, 'TC' => 12, 'FFP' => 15],
            'A-' => ['WB' => 4,  'PRC' => 3,  'TC' => 2,  'FFP' => 2],
            'B+' => ['WB' => 32, 'PRC' => 40, 'TC' => 18, 'FFP' => 20],
            'B-' => ['WB' => 5,  'PRC' => 4,  'TC' => 1,  'FFP' => 2],
            'AB+' => ['WB' => 14, 'PRC' => 16, 'TC' => 8,  'FFP' => 9],
            'AB-' => ['WB' => 2,  'PRC' => 2,  'TC' => 1,  'FFP' => 1],
            'O+' => ['WB' => 45, 'PRC' => 60, 'TC' => 24, 'FFP' => 28],
            'O-' => ['WB' => 6,  'PRC' => 5,  'TC' => 2,  'FFP' => 3],
        ];

        foreach ($bloodTypes as $bt) {
            foreach ($rhesuses as $rh) {
                $combo = $bt.$rh;
                foreach ($components as $comp) {
                    $units = $stockMatrix[$combo][$comp] ?? 10;
                    BloodStock::updateOrCreate(
                        ['blood_type' => $bt, 'rhesus' => $rh, 'component' => $comp],
                        [
                            'total_units' => $units,
                            'min_threshold' => in_array($rh, ['-']) ? 5 : 15,
                        ]
                    );
                }
            }
        }

        // 3. Donors Data
        $donorsData = [
            [
                'donor_code' => 'DNR-2026-001',
                'nik' => '3171012304950001',
                'name' => 'Ahmad Fadhilah',
                'email' => 'ahmad.fadhilah@gmail.com',
                'phone' => '081288223344',
                'blood_type' => 'O',
                'rhesus' => '+',
                'gender' => 'L',
                'birth_date' => '1995-04-23',
                'address' => 'Jl. Tebet Barat Dalam No. 12, Jakarta Selatan',
                'occupation' => 'Software Engineer',
                'weight' => 68.5,
                'systolic' => 118,
                'diastolic' => 78,
                'hemoglobin' => 14.8,
                'last_donation_date' => Carbon::now()->subDays(75),
                'total_donations' => 8,
                'verification_status' => 'verified',
                'verification_notes' => 'Donor rutin aktif. Kondisi prima dan memenuhi semua syarat.',
                'verified_at' => Carbon::now()->subMonths(3),
                'verified_by' => $admin->id,
            ],
            [
                'donor_code' => 'DNR-2026-002',
                'nik' => '3172021508980002',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza98@gmail.com',
                'phone' => '085711223344',
                'blood_type' => 'A',
                'rhesus' => '+',
                'gender' => 'P',
                'birth_date' => '1998-08-15',
                'address' => 'Jl. Rawamangun Muka No. 45, Jakarta Timur',
                'occupation' => 'Guru',
                'weight' => 54.0,
                'systolic' => 110,
                'diastolic' => 70,
                'hemoglobin' => 12.8,
                'last_donation_date' => Carbon::now()->subDays(65),
                'total_donations' => 4,
                'verification_status' => 'verified',
                'verification_notes' => 'Layak donor, Hb stabil di atas 12.5.',
                'verified_at' => Carbon::now()->subMonths(2),
                'verified_by' => $admin->id,
            ],
            [
                'donor_code' => 'DNR-2026-003',
                'nik' => '3275031012920003',
                'name' => 'Rian Pratama',
                'email' => 'rian.pratama@outlook.com',
                'phone' => '087899887766',
                'blood_type' => 'B',
                'rhesus' => '+',
                'gender' => 'L',
                'birth_date' => '1992-12-10',
                'address' => 'Perum Pondok Gede Blok C4, Bekasi',
                'occupation' => 'Karyawan Swasta',
                'weight' => 74.0,
                'systolic' => 125,
                'diastolic' => 82,
                'hemoglobin' => 15.2,
                'last_donation_date' => Carbon::now()->subDays(20),
                'total_donations' => 12,
                'verification_status' => 'verified',
                'verification_notes' => 'Pendonor medali 10x donor. Sangat sehat.',
                'verified_at' => Carbon::now()->subDays(20),
                'verified_by' => $admin->id,
            ],
            [
                'donor_code' => 'DNR-2026-004',
                'nik' => '3174042801010004',
                'name' => 'Dewi Anggraini',
                'email' => 'dewi.anggr@gmail.com',
                'phone' => '081344556677',
                'blood_type' => 'AB',
                'rhesus' => '+',
                'gender' => 'P',
                'birth_date' => '2001-01-28',
                'address' => 'Jl. Kemang Raya No. 89, Jakarta Selatan',
                'occupation' => 'Mahasiswi',
                'weight' => 49.5,
                'systolic' => 105,
                'diastolic' => 65,
                'hemoglobin' => 11.2,
                'last_donation_date' => null,
                'total_donations' => 0,
                'verification_status' => 'pending',
                'verification_notes' => 'Pendaftar baru, perlu verifikasi skrining awal (Hb terdeteksi 11.2 saat skrining formulir).',
                'verified_at' => null,
                'verified_by' => null,
            ],
            [
                'donor_code' => 'DNR-2026-005',
                'nik' => '3173051906940005',
                'name' => 'Dimas Wicaksono',
                'email' => 'dimas.wicak@yahoo.com',
                'phone' => '081122334455',
                'blood_type' => 'O',
                'rhesus' => '-',
                'gender' => 'L',
                'birth_date' => '1994-06-19',
                'address' => 'Apartemen Mediterania Garden Blok C, Jakarta Barat',
                'occupation' => 'Arsitek',
                'weight' => 71.0,
                'systolic' => 120,
                'diastolic' => 80,
                'hemoglobin' => 14.5,
                'last_donation_date' => Carbon::now()->subDays(92),
                'total_donations' => 5,
                'verification_status' => 'verified',
                'verification_notes' => 'Golongan langka O negatif (donor siaga PMI). Terverifikasi aktif.',
                'verified_at' => Carbon::now()->subMonths(4),
                'verified_by' => $admin->id,
            ],
            [
                'donor_code' => 'DNR-2026-006',
                'nik' => '3175060203970006',
                'name' => 'Farhan Ramadhan',
                'email' => 'farhan.ramadhan@gmail.com',
                'phone' => '085299881122',
                'blood_type' => 'B',
                'rhesus' => '-',
                'gender' => 'L',
                'birth_date' => '1997-03-02',
                'address' => 'Jl. Otista Raya No. 102, Jakarta Timur',
                'occupation' => 'Wiraswasta',
                'weight' => 63.0,
                'systolic' => 145,
                'diastolic' => 95,
                'hemoglobin' => 13.9,
                'last_donation_date' => Carbon::now()->subMonths(6),
                'total_donations' => 2,
                'verification_status' => 'suspended',
                'verification_notes' => 'Ditangguhkan sementara karena hipertensi tingkat 1 (145/95). Disarankan istirahat dan cek ulang 1 bulan ke depan.',
                'verified_at' => Carbon::now()->subDays(10),
                'verified_by' => $admin->id,
            ],
            [
                'donor_code' => 'DNR-2026-007',
                'nik' => '3276071409990007',
                'name' => 'Nadia Putri Maharani',
                'email' => 'nadia.putri@gmail.com',
                'phone' => '082155667788',
                'blood_type' => 'A',
                'rhesus' => '+',
                'gender' => 'P',
                'birth_date' => '1999-09-14',
                'address' => 'Jl. Margonda Raya No. 340, Depok',
                'occupation' => 'Dokter Gigi',
                'weight' => 56.5,
                'systolic' => 115,
                'diastolic' => 75,
                'hemoglobin' => 13.4,
                'last_donation_date' => null,
                'total_donations' => 0,
                'verification_status' => 'pending',
                'verification_notes' => 'Pendaftaran donor baru online, menunggu verifikasi berkas NIK dan kesehatan.',
                'verified_at' => null,
                'verified_by' => null,
            ],
            [
                'donor_code' => 'DNR-2026-008',
                'nik' => '3171082505900008',
                'name' => 'Hendra Setiawan',
                'email' => 'hendra.setiawan@gmail.com',
                'phone' => '081299001122',
                'blood_type' => 'AB',
                'rhesus' => '-',
                'gender' => 'L',
                'birth_date' => '1990-05-25',
                'address' => 'Jl. Cempaka Putih Tengah No. 18, Jakarta Pusat',
                'occupation' => 'Konsultan Manajemen',
                'weight' => 78.0,
                'systolic' => 122,
                'diastolic' => 80,
                'hemoglobin' => 15.0,
                'last_donation_date' => Carbon::now()->subDays(110),
                'total_donations' => 7,
                'verification_status' => 'verified',
                'verification_notes' => 'Donor langka AB negatif, memenuhi syarat donor kembali.',
                'verified_at' => Carbon::now()->subMonths(5),
                'verified_by' => $admin->id,
            ],
        ];

        $createdDonors = [];
        foreach ($donorsData as $data) {
            $createdDonors[] = Donor::create($data);
        }

        // 4. Sample Donor Histories
        DonorHistory::create([
            'donor_id' => $createdDonors[0]->id,
            'donation_date' => Carbon::now()->subDays(75),
            'blood_type' => 'O',
            'rhesus' => '+',
            'component_type' => 'WB',
            'volume_ml' => 350,
            'blood_bag_code' => 'BAG-O-POS-20260819-01',
            'hemoglobin' => 14.8,
            'blood_pressure' => '118/78',
            'screening_result' => 'passed',
            'notes' => 'Kondisi pendonor sangat baik.',
            'officer_id' => $staff->id,
        ]);

        DonorHistory::create([
            'donor_id' => $createdDonors[2]->id,
            'donation_date' => Carbon::now()->subDays(20),
            'blood_type' => 'B',
            'rhesus' => '+',
            'component_type' => 'PRC',
            'volume_ml' => 350,
            'blood_bag_code' => 'BAG-B-POS-20261013-05',
            'hemoglobin' => 15.2,
            'blood_pressure' => '125/82',
            'screening_result' => 'passed',
            'notes' => 'Donasi donor plasma konvalesen/PRC lancar.',
            'officer_id' => $staff->id,
        ]);

        // 5. Donor Events & Attendances
        $event1 = DonorEvent::create([
            'event_code' => 'EVT-2026-001',
            'title' => 'Aksi Donor Darah Peduli Kampus UI Depok',
            'organizer' => 'PMI Depok & BEM UI',
            'location' => 'Balairung Universitas Indonesia',
            'address' => 'Kampus Baru UI, Pondok Cina, Beji, Depok',
            'event_date' => Carbon::now()->addDays(3),
            'start_time' => '08:30:00',
            'end_time' => '14:00:00',
            'quota' => 200,
            'target_bags' => 150,
            'status' => 'upcoming',
            'pic_name' => 'dr. Rangga Raditya',
            'pic_phone' => '081299883311',
            'description' => 'Kegiatan donor darah massal semester genap untuk seluruh sivitas akademika dan umum.',
        ]);

        $event2 = DonorEvent::create([
            'event_code' => 'EVT-2026-002',
            'title' => 'Bakti Sosial Donor Darah Senayan City Mall',
            'organizer' => 'PMI DKI Jakarta & Rotary Club',
            'location' => 'Main Atrium Senayan City Lantai 1',
            'address' => 'Jl. Asia Afrika Lot 19, Gelora, Tanah Abang, Jakarta Pusat',
            'event_date' => Carbon::now(), // Today!
            'start_time' => '10:00:00',
            'end_time' => '17:00:00',
            'quota' => 120,
            'target_bags' => 100,
            'status' => 'ongoing',
            'pic_name' => 'Maya Wulandari, S.Kep',
            'pic_phone' => '085611229988',
            'description' => 'Donor darah mall weekend terbuka untuk pengunjung umum, disediakan merchandise suvenir menarik.',
        ]);

        $event3 = DonorEvent::create([
            'event_code' => 'EVT-2026-003',
            'title' => 'Donor Darah Rutin PT Telkom Landmark Tower',
            'organizer' => 'PMI Jakarta Selatan & Majelis Telkom',
            'location' => 'Auditorium Lt. 6 Telkom Landmark Tower',
            'address' => 'Jl. Gatot Subroto No. Kav 52, Mampang Prapatan',
            'event_date' => Carbon::now()->subDays(14),
            'start_time' => '09:00:00',
            'end_time' => '15:00:00',
            'quota' => 150,
            'target_bags' => 120,
            'status' => 'completed',
            'pic_name' => 'Agus Triyono',
            'pic_phone' => '081744332211',
            'description' => 'Kegiatan rutin donor darah triwulanan karyawan grup Telkom.',
        ]);

        // Attendances for Event 2 (Ongoing today - ready for Absensi Admin!)
        EventAttendance::create([
            'event_id' => $event2->id,
            'donor_id' => $createdDonors[0]->id,
            'registration_code' => 'REG-EVT2-001',
            'attendance_status' => 'registered', // Menunggu absensi
            'screening_status' => 'pending',
            'notes' => 'Peserta sudah tiba di ruang tunggu.',
        ]);

        EventAttendance::create([
            'event_id' => $event2->id,
            'donor_id' => $createdDonors[1]->id,
            'registration_code' => 'REG-EVT2-002',
            'attendance_status' => 'present', // Hadir & lolos
            'screening_status' => 'passed',
            'blood_pressure' => '115/72',
            'hemoglobin' => 13.0,
            'blood_bag_code' => 'BAG-A-POS-20261102-01',
            'notes' => 'Donor sukses 350ml WB.',
            'attended_at' => Carbon::now()->subHours(2),
            'officer_id' => $staff->id,
        ]);

        EventAttendance::create([
            'event_id' => $event2->id,
            'donor_id' => $createdDonors[3]->id,
            'registration_code' => 'REG-EVT2-003',
            'attendance_status' => 'registered',
            'screening_status' => 'pending',
            'notes' => 'Jadwal sesi 2 siang.',
        ]);

        EventAttendance::create([
            'event_id' => $event2->id,
            'donor_id' => $createdDonors[4]->id,
            'registration_code' => 'REG-EVT2-004',
            'attendance_status' => 'registered',
            'screening_status' => 'pending',
            'notes' => 'Donor O negatif darurat.',
        ]);

        // Attendances for Event 1 (Upcoming)
        EventAttendance::create([
            'event_id' => $event1->id,
            'donor_id' => $createdDonors[6]->id,
            'registration_code' => 'REG-EVT1-001',
            'attendance_status' => 'registered',
            'screening_status' => 'pending',
            'notes' => 'Pendaftaran online via web portal.',
        ]);

        // 6. Hospital Requests (Permintaan Rumah Sakit)
        $hospitalRequests = [
            [
                'request_code' => 'REQ-RS-2026-0101',
                'hospital_name' => 'RSUP Nasional Dr. Cipto Mangunkusumo (RSCM)',
                'doctor_in_charge' => 'dr. Andreas Sp.B(K)V',
                'contact_phone' => '021-3143707 / 081299887711',
                'patient_name' => 'Ny. Ratna Kusuma',
                'medical_record_number' => 'RM-RSCM-889021',
                'blood_type' => 'O',
                'rhesus' => '-',
                'component' => 'PRC',
                'units_requested' => 3,
                'urgency' => 'emergency', // Darurat Cito!
                'diagnosis_notes' => 'Operasi bedah vaskular darurat ruptur aneurisma aorta abdominalis. Butuh segera!',
                'status' => 'pending', // Menunggu persetujuan admin!
            ],
            [
                'request_code' => 'REQ-RS-2026-0102',
                'hospital_name' => 'RS Jantung dan Pembuluh Darah Harapan Kita',
                'doctor_in_charge' => 'dr. Tri Wahyuni, Sp.BTKV',
                'contact_phone' => '021-5684085',
                'patient_name' => 'Tn. Bambang Suherman',
                'medical_record_number' => 'RM-HK-442190',
                'blood_type' => 'A',
                'rhesus' => '+',
                'component' => 'WB',
                'units_requested' => 4,
                'urgency' => 'urgent',
                'diagnosis_notes' => 'Tindakan CABG (Coronary Artery Bypass Grafting) besok pagi pk 08.00.',
                'status' => 'pending', // Menunggu persetujuan admin!
            ],
            [
                'request_code' => 'REQ-RS-2026-0103',
                'hospital_name' => 'RSUP Fatmawati Jakarta Selatan',
                'doctor_in_charge' => 'dr. Irwan Setiadi, Sp.A',
                'contact_phone' => '021-7501524',
                'patient_name' => 'An. Keisha Aurelia (6 th)',
                'medical_record_number' => 'RM-FAT-771203',
                'blood_type' => 'B',
                'rhesus' => '+',
                'component' => 'TC',
                'units_requested' => 2,
                'urgency' => 'urgent',
                'diagnosis_notes' => 'Pasien DHF Grade 3 hari ke-5, trombosit 18.000 mcL.',
                'status' => 'approved',
                'rejection_reason' => null,
                'action_by' => $admin->id,
                'action_at' => Carbon::now()->subHours(4),
                'handover_recipient' => 'Kurir Medis RS Fatmawati - Bpk. Joko',
            ],
            [
                'request_code' => 'REQ-RS-2026-0104',
                'hospital_name' => 'RS Siloam Hospitals Kebon Jeruk',
                'doctor_in_charge' => 'dr. Hendra Widjaja, Sp.PD-KHOM',
                'contact_phone' => '021-25677888',
                'patient_name' => 'Tn. Gunawan Halim',
                'medical_record_number' => 'RM-SHKJ-109234',
                'blood_type' => 'AB',
                'rhesus' => '-',
                'component' => 'FFP',
                'units_requested' => 6,
                'urgency' => 'normal',
                'diagnosis_notes' => 'Permintaan cadangan elektif terapi koagulopati sirosis hepatis.',
                'status' => 'rejected',
                'rejection_reason' => 'Stok plasma beku segar (FFP) AB negatif sangat terbatas (< 2 unit). Disarankan koordinasi dengan UDD PMI Pusat untuk distribusi alternatif.',
                'action_by' => $admin->id,
                'action_at' => Carbon::now()->subDay(),
                'handover_recipient' => null,
            ],
            [
                'request_code' => 'REQ-RS-2026-0105',
                'hospital_name' => 'RSUD Tarakan Jakarta Pusat',
                'doctor_in_charge' => 'dr. Nabila Ramadhani, Sp.OG',
                'contact_phone' => '021-3503003',
                'patient_name' => 'Ibu Marlina Susanti',
                'medical_record_number' => 'RM-TRK-552140',
                'blood_type' => 'O',
                'rhesus' => '+',
                'component' => 'PRC',
                'units_requested' => 2,
                'urgency' => 'urgent',
                'diagnosis_notes' => 'Perdarahan pasca persalinan (HPP) atonia uteri. Hb 7.1 g/dL.',
                'status' => 'completed',
                'rejection_reason' => null,
                'action_by' => $admin->id,
                'action_at' => Carbon::now()->subDays(2),
                'handover_recipient' => 'Ambulans RSUD Tarakan - Sdr. Rudi',
            ],
        ];

        foreach ($hospitalRequests as $req) {
            HospitalRequest::create($req);
        }

        // 7. Blood Stock Mutations (Riwayat Mutasi Stok)
        BloodStockMutation::create([
            'blood_type' => 'B',
            'rhesus' => '+',
            'component' => 'TC',
            'type' => 'out',
            'quantity' => -2,
            'bag_code' => 'BAG-TC-B-0089',
            'reference_type' => 'hospital_request',
            'reference_id' => 3,
            'notes' => 'Distribusi ke RSUP Fatmawati untuk pasien anak DHF.',
            'officer_id' => $admin->id,
        ]);

        BloodStockMutation::create([
            'blood_type' => 'A',
            'rhesus' => '+',
            'component' => 'WB',
            'type' => 'in',
            'quantity' => 1,
            'bag_code' => 'BAG-A-POS-20261102-01',
            'reference_type' => 'donation',
            'notes' => 'Penerimaan kantong donasi event Senayan City dari donor Siti Nurhaliza.',
            'officer_id' => $staff->id,
        ]);

        // 8. Reminder Notifications (Pengingat dan Notifikasi)
        ReminderNotification::create([
            'recipient_type' => 'donor',
            'recipient_name' => 'Ahmad Fadhilah',
            'recipient_contact' => '081288223344',
            'type' => 'donor_recall',
            'title' => 'Pengingat Waktu Donor Darah Kembali',
            'message' => 'Halo Kak Ahmad Fadhilah! Terima kasih atas kebaikan Anda sebelumnya. Masa tunggu donor 60 hari sudah terlewati (terakhir donor 75 hari lalu). Tubuh Anda sudah siap untuk kembali menyelamatkan nyawa sesama di PMI DarahKitaa terdekat.',
            'channel' => 'whatsapp',
            'status' => 'sent',
            'donor_id' => $createdDonors[0]->id,
            'sent_at' => Carbon::now()->subDays(2),
            'sender_id' => $admin->id,
        ]);

        ReminderNotification::create([
            'recipient_type' => 'donor',
            'recipient_name' => 'Dimas Wicaksono',
            'recipient_contact' => '081122334455',
            'type' => 'donor_recall',
            'title' => 'Panggilan Donor Golongan Darah Langka O Negatif',
            'message' => 'Salam kemanusiaan Bpk Dimas Wicaksono. Saat ini persediaan kantong darah O negatif di PMI DarahKitaa sedang dalam status siaga/kritis. Jika Bapak sedang sehat, kami mengundang Bapak untuk mendonorkan darah di markas UDD terdekat.',
            'channel' => 'whatsapp',
            'status' => 'sent',
            'donor_id' => $createdDonors[4]->id,
            'sent_at' => Carbon::now()->subHours(6),
            'sender_id' => $admin->id,
        ]);
    }
}
