<?php

namespace Database\Seeders;

use App\Models\AchievementRule;
use App\Models\CaseAction;
use App\Models\PointTransaction;
use App\Models\Report;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentCase;
use App\Models\User;
use App\Models\ViolationRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1. Tahun Ajaran
        $schoolYear = SchoolYear::query()->updateOrCreate(
            ['name' => '2026/2027'],
            [
                'starts_on' => '2026-07-01',
                'ends_on' => '2027-06-30',
                'is_active' => true,
            ]
        );

        // 2. Master Aturan Pelanggaran SMAN 6 Bandung
        $violations = [
            ['code' => 'P-01', 'name' => 'Keterlambatan masuk sekolah', 'points' => 10, 'severity' => 'ringan', 'description' => 'Terlambat lebih dari 10 menit setelah bel masuk'],
            ['code' => 'P-02', 'name' => 'Seragam tidak rapi / atribut tidak lengkap', 'points' => 15, 'severity' => 'ringan', 'description' => 'Tidak memakai dasi, sabuk, atau badge resmi'],
            ['code' => 'P-03', 'name' => 'Meninggalkan kelas tanpa izin (bolos jam pelajaran)', 'points' => 25, 'severity' => 'sedang', 'description' => 'Berada di kantin/luar kelas saat KBM tanpa izin guru'],
            ['code' => 'P-04', 'name' => 'Menggunakan HP untuk hal non-edukatif saat KBM', 'points' => 20, 'severity' => 'ringan', 'description' => 'Main game atau sosmed saat guru menerangkan'],
            ['code' => 'P-05', 'name' => 'Merokok / membawa rokok atau vape di lingkungan sekolah', 'points' => 75, 'severity' => 'berat', 'description' => 'Kedapatan merokok/vaping di area sekolah atau radius 100m'],
            ['code' => 'P-06', 'name' => 'Berkelahi / kekerasan fisik di sekolah', 'points' => 150, 'severity' => 'berat', 'description' => 'Melakukan tindakan fisik yang melukai orang lain'],
            ['code' => 'P-07', 'name' => 'Perundungan (Bullying) verbal maupun fisik', 'points' => 200, 'severity' => 'berat', 'description' => 'Melakukan intimidasi berulang kepada siswa lain'],
            ['code' => 'P-08', 'name' => 'Membawa senjata tajam / zat terlarang (Zero Tolerance)', 'points' => 500, 'severity' => 'khusus', 'description' => 'Zero tolerance: tindakan langsung sidang dewan guru & orang tua'],
        ];

        foreach ($violations as $v) {
            ViolationRule::query()->updateOrCreate(['code' => $v['code']], $v);
        }

        // 3. Master Aturan Prestasi / Penebusan
        $achievements = [
            ['code' => 'A-01', 'name' => 'Juara 1 Lomba Akademik / Non-Akademik Tingkat Kota', 'points' => 50, 'pillar' => 'Prestasi Kompetisi', 'description' => 'Memperoleh peringkat 1 tingkat kota'],
            ['code' => 'A-02', 'name' => 'Juara 1/2/3 Tingkat Provinsi atau Nasional', 'points' => 100, 'pillar' => 'Prestasi Kompetisi', 'description' => 'Mengharumkan nama sekolah di tingkat regional/nasional'],
            ['code' => 'A-03', 'name' => 'Pengurus OSIS / MPK / Ekstrakurikuler Aktif', 'points' => 30, 'pillar' => 'Organisasi & Kepemimpinan', 'description' => 'Dedikasi kepemimpinan sekolah selama 1 periode'],
            ['code' => 'A-04', 'name' => 'Aksi Sosial & Relawan Lingkungan Sekolah (Penebusan)', 'points' => 25, 'pillar' => 'Penebusan Disiplin', 'description' => 'Membersihkan taman sekolah / perpustakaan secara sukarela'],
            ['code' => 'A-05', 'name' => 'Kehadiran 100% Sempurna dalam 1 Semester', 'points' => 40, 'pillar' => 'Kedisiplinan', 'description' => 'Tanpa alfa, izin, atau terlambat selama 6 bulan'],
        ];

        foreach ($achievements as $a) {
            AchievementRule::query()->updateOrCreate(['code' => $a['code']], $a);
        }

        // 4. Users (BK, Guru, Siswa)
        $bkUser = User::query()->updateOrCreate(
            ['username' => 'kesiswaan'],
            [
                'name' => 'Ibu Rahmawati, S.Pd (BK)',
                'email' => 'bk@sman6bdg.sch.id',
                'nip' => '198205152008012004',
                'role' => 'kesiswaan_bk',
                'password' => Hash::make('password'),
                'is_active' => true,
                'is_first_login' => false,
            ]
        );

        $guruWali1 = User::query()->updateOrCreate(
            ['username' => 'guru.budi'],
            [
                'name' => 'Drs. Budi Santoso (Wali X IPA 1)',
                'email' => 'budi@sman6bdg.sch.id',
                'nip' => '197903122005011003',
                'role' => 'guru',
                'password' => Hash::make('password'),
                'is_active' => true,
                'is_first_login' => false,
            ]
        );

        $guruWali2 = User::query()->updateOrCreate(
            ['username' => 'guru.siti'],
            [
                'name' => 'Siti Aminah, M.Pd (Wali XI IPS 1)',
                'email' => 'siti@sman6bdg.sch.id',
                'nip' => '198507202010012011',
                'role' => 'guru',
                'password' => Hash::make('password'),
                'is_active' => true,
                'is_first_login' => false,
            ]
        );

        $guruBiasa = User::query()->updateOrCreate(
            ['username' => 'guru.hendra'],
            [
                'name' => 'Hendra Wijaya, S.Kom (Guru Mapel)',
                'email' => 'hendra@sman6bdg.sch.id',
                'nip' => '199001012015011007',
                'role' => 'guru',
                'password' => Hash::make('password'),
                'is_active' => true,
                'is_first_login' => false,
            ]
        );

        // 5. Kelas
        $kelas1 = SchoolClass::query()->updateOrCreate(
            ['school_year_id' => $schoolYear->id, 'name' => 'X MIPA 1'],
            [
                'homeroom_teacher_id' => $guruWali1->id,
                'grade_level' => 10,
            ]
        );

        $kelas2 = SchoolClass::query()->updateOrCreate(
            ['school_year_id' => $schoolYear->id, 'name' => 'XI IPS 1'],
            [
                'homeroom_teacher_id' => $guruWali2->id,
                'grade_level' => 11,
            ]
        );

        // 6. Akun Siswa & Profil
        $siswaData = [
            ['username' => 'siswa.andi', 'nis' => '260001', 'nisn' => '0061234501', 'name' => 'Andi Pratama', 'gender' => 'male', 'class_id' => $kelas1->id],
            ['username' => 'siswa.citra', 'nis' => '260002', 'nisn' => '0061234502', 'name' => 'Citra Lestari', 'gender' => 'female', 'class_id' => $kelas1->id],
            ['username' => 'siswa.dimas', 'nis' => '260003', 'nisn' => '0061234503', 'name' => 'Dimas Saputra', 'gender' => 'male', 'class_id' => $kelas1->id],
            ['username' => 'siswa.rizky', 'nis' => '260004', 'nisn' => '0061234504', 'name' => 'Rizky Ramadhan', 'gender' => 'male', 'class_id' => $kelas2->id],
        ];

        $students = [];
        foreach ($siswaData as $sd) {
            $u = User::query()->updateOrCreate(
                ['username' => $sd['username']],
                [
                    'name' => $sd['name'],
                    'email' => "{$sd['username']}@siswa.sman6bdg.sch.id",
                    'role' => 'siswa',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'is_first_login' => false,
                ]
            );

            $st = Student::query()->updateOrCreate(
                ['nis' => $sd['nis']],
                [
                    'user_id' => $u->id,
                    'class_id' => $sd['class_id'],
                    'nisn' => $sd['nisn'],
                    'name' => $sd['name'],
                    'gender' => $sd['gender'],
                    'is_active' => true,
                ]
            );

            // Inisialisasi Saldo Awal 2000 jika belum ada
            if ($st->pointTransactions()->count() === 0) {
                PointTransaction::query()->create([
                    'student_id' => $st->id,
                    'school_year_id' => $schoolYear->id,
                    'performed_by' => $bkUser->id,
                    'type' => 'initial_balance',
                    'points' => 2000,
                    'balance_before' => 0,
                    'balance_after' => 2000,
                    'description' => 'Saldo Poin Awal Tahun Ajaran 2026/2027',
                    'transacted_at' => $now->copy()->subMonths(1),
                ]);
            }

            $students[$sd['nis']] = $st;
        }

        // 7. Dummy Laporan & Transaksi Poin Demo
        $ruleTerlambat = ViolationRule::query()->where('code', 'P-01')->first();
        $ruleJuara = AchievementRule::query()->where('code', 'A-01')->first();

        // Andi: Prestasi +50 -> Saldo 2050
        $rep1 = Report::query()->create([
            'student_id' => $students['260001']->id,
            'reported_by' => $guruWali1->id,
            'verified_by' => $bkUser->id,
            'achievement_rule_id' => $ruleJuara?->id,
            'type' => 'achievement',
            'occurred_on' => $now->copy()->subDays(5),
            'description' => 'Meraih Juara 1 Olimpiade Sains Kota Bandung',
            'status' => 'approved',
            'verification_note' => 'Dokumen sertifikat terverifikasi valid oleh panitia.',
            'verified_at' => $now->copy()->subDays(4),
        ]);

        PointTransaction::query()->create([
            'student_id' => $students['260001']->id,
            'school_year_id' => $schoolYear->id,
            'performed_by' => $bkUser->id,
            'reference_type' => Report::class,
            'reference_id' => $rep1->id,
            'type' => 'achievement',
            'points' => 50,
            'balance_before' => 2000,
            'balance_after' => 2050,
            'description' => 'Penambahan Poin Prestasi: Juara 1 Lomba Akademik',
            'transacted_at' => $now->copy()->subDays(4),
        ]);

        // Dimas: Pelanggaran Terlambat -10 -> Saldo 1990
        $rep2 = Report::query()->create([
            'student_id' => $students['260003']->id,
            'reported_by' => $guruBiasa->id,
            'verified_by' => $bkUser->id,
            'violation_rule_id' => $ruleTerlambat?->id,
            'type' => 'violation',
            'occurred_on' => $now->copy()->subDays(2),
            'description' => 'Terlambat 20 menit saat upacara bendera hari Senin',
            'status' => 'approved',
            'verification_note' => 'Disetujui. Peringatan lisan telah diberikan oleh guru piket.',
            'verified_at' => $now->copy()->subDays(1),
        ]);

        PointTransaction::query()->create([
            'student_id' => $students['260003']->id,
            'school_year_id' => $schoolYear->id,
            'performed_by' => $bkUser->id,
            'reference_type' => Report::class,
            'reference_id' => $rep2->id,
            'type' => 'violation',
            'points' => -10,
            'balance_before' => 2000,
            'balance_after' => 1990,
            'description' => 'Pengurangan Poin: Keterlambatan masuk sekolah',
            'transacted_at' => $now->copy()->subDays(1),
        ]);

        // Rizky: Laporan Pending Menunggu BK
        Report::query()->create([
            'student_id' => $students['260004']->id,
            'reported_by' => $guruWali2->id,
            'violation_rule_id' => ViolationRule::query()->where('code', 'P-03')->first()?->id,
            'type' => 'violation',
            'occurred_on' => $now->copy()->subHours(6),
            'description' => 'Terlihat berada di kantin saat jam pelajaran Matematika berlangsung.',
            'status' => 'pending',
        ]);

        // 8. Dummy Kasus Pembinaan Siswa
        $case1 = StudentCase::query()->create([
            'student_id' => $students['260003']->id,
            'report_id' => $rep2->id,
            'created_by' => $bkUser->id,
            'title' => 'Pembinaan Kedisiplinan Kehadiran Pagi',
            'description' => 'Siswa telah beberapa kali terlambat pada awal pekan. Dilakukan konseling waktu bangun tidur & persiapan pagi.',
            'status' => 'in_progress',
            'confidentiality' => 'normal',
        ]);

        CaseAction::query()->create([
            'case_id' => $case1->id,
            'performed_by' => $bkUser->id,
            'action' => 'Konseling Individu & Penyusunan Komitmen Waktu',
            'note' => 'Siswa berjanji berangkat sebelum pukul 06.30 WIB. Wali kelas memantau kehadiran harian.',
            'performed_at' => $now->copy()->subDays(1),
        ]);
    }
}
