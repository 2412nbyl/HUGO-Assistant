<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NotarisCase;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


use App\Models\CaseDocument;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default users if none exist
        if (User::count() === 0) {
            $admin = User::create([
                'name' => 'Admin HUGO',
                'username' => 'admin',
                'email' => 'admin@hugo.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'custom_id' => 'ADM001',
            ]);

            $notaris = User::create([
                'name' => 'Notaris HUGO',
                'username' => 'notaris',
                'email' => 'notaris@hugo.id',
                'password' => Hash::make('password'),
                'role' => 'notaris',
                'custom_id' => 'NOT001',
            ]);

            User::create([
                'name' => 'Staff Budi',
                'username' => 'staff.budi',
                'email' => 'budi@hugo.id',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'custom_id' => 'STA001',
            ]);
        } else {
            $admin = User::where('role', 'admin')->first();
        }

        // Create more dummy users
        $roles = ['staff', 'freelancer'];
        for ($i = 1; $i <= 15; $i++) {
            $role = $roles[array_rand($roles)];
            User::firstOrCreate(
                ['email' => "user{$i}@hugo.id"],
                [
                    'name' => "User {$i} ($role)",
                    'username' => "user{$i}",
                    'password' => Hash::make('password'),
                    'role' => $role,
                    'custom_id' => strtoupper(substr($role, 0, 3)) . str_pad($i + 10, 3, '0', STR_PAD_LEFT),
                ]
            );
        }

        // Create sample cases
        $casesData = [
            ['PT Maju Jaya', '08111222333', 'Jl. Sudirman 10, Jakarta', 'Pendirian PT', 'PT', 'selesai', '2026-02-10', '15000000'],
            ['CV Berkah Utama', '08222333444', 'Jl. Thamrin 5, Jakarta', 'Perubahan CV', 'CV', 'proses', '2026-02-28', '8500000'],
            ['Andi Wijaya', '08333444555', 'Jl. Kebon Jeruk 20, Jakarta', 'Akta Jual Beli Tanah', 'Pribadi', 'tertunda', '2026-03-05', '5000000'],
            ['Siti Rahayu', '08444555666', 'Jl. Kuningan 15, Jakarta', 'Perjanjian Kerjasama', 'Pribadi', 'selesai', '2026-01-20', '3000000'],
            ['PT Sinar Mas', '08555666777', 'Jl. M.H. Thamrin 51, Jakarta', 'Akuisisi Saham', 'PT', 'proses', '2026-03-15', '25000000'],
            ['CV Jaya Abadi', '08666777888', 'Jl. Gatot Subroto 88, Jakarta', 'Likuidasi CV', 'CV', 'selesai', '2025-12-10', '6000000'],
            ['Bambang Hartono', '08777888999', 'Jl. Cempaka Putih 7, Jakarta', 'Hibah Tanah', 'Pribadi', 'proses', '2026-02-22', '4500000'],
            ['PT Nusantara Corp', '08888999000', 'Jl. Asia Afrika 1, Bandung', 'Perubahan Anggaran Dasar', 'PT', 'tertunda', '2026-04-01', '12000000'],
        ];

        // Generate additional 50 dummy cases
        $types = ['PT', 'CV', 'Pribadi'];
        $statuses = ['selesai', 'proses', 'tertunda'];
        for ($i = 1; $i <= 50; $i++) {
            $type = $types[array_rand($types)];
            $status = $statuses[array_rand($statuses)];
            $casesData[] = [
                "Klien Dummy $i", 
                "08" . rand(100000000, 999999999), 
                "Jl. Dummy $i", 
                "Kasus Dummy $i", 
                $type, 
                $status, 
                date('Y-m-d', strtotime('+' . rand(-30, 60) . ' days')), 
                rand(10, 50) * 100000
            ];
        }

        $payStatus = ['selesai' => 'lunas', 'proses' => 'sebagian', 'tertunda' => 'belum'];

        // 1. Create Clients
        $clients = [];
        for ($i = 1; $i <= 10; $i++) {
            $clients[] = \App\Models\Client::create([
                'name' => "Klien Utama $i",
                'phone' => "08" . rand(100000000, 999999999),
                'address' => "Alamat Klien $i",
                'birth_date' => date('Y-m-d', strtotime('-' . rand(20, 50) . ' years')),
            ]);
        }

        // 2. Create Staff
        $positions = ['Staff Notaris', 'Staff PPAT', 'Administrasi', 'IT Support'];
        for ($i = 1; $i <= 5; $i++) {
            \App\Models\Staff::create([
                'name' => "Staff Ahli $i",
                'position' => $positions[array_rand($positions)],
                'work_status' => 'Aktif',
                'email' => "staff$i@hugo.id",
                'phone' => "08" . rand(100000000, 999999999),
            ]);
        }

        // 3. Create Cases (Linked to Clients)
        foreach ($casesData as $index => [$client, $phone, $address, $caseName, $type, $status, $deadline, $amount]) {
            $randomClient = $clients[array_rand($clients)];
            
            // Assign dummy files to exactly 50% of the cases (even indexes)
            $hasDummyFiles = ($index % 2 === 0);

            // Distribute created_at beautifully across the last 18 months
            $created_at = \Carbon\Carbon::now()->subMonths(rand(0, 18))->subDays(rand(1, 28));
            $newDeadline = $created_at->copy()->addDays(rand(15, 45))->format('Y-m-d');

            $caseData = [
                'client_name' => $client,
                'phone' => $phone,
                'address' => $address,
                'case_name' => $caseName,
                'type' => $type,
                'status' => $status,
                'deadline' => $newDeadline,
                'created_by' => $admin?->id,
                'id_klien' => $randomClient->id_klien, // Link to client
            ];

            if ($hasDummyFiles) {
                $caseData['file_ktp'] = 'case-files/dummy_ktp.pdf';
                $caseData['file_npwp'] = 'case-files/dummy_npwp.pdf';
                $caseData['file_kk'] = 'case-files/dummy_kk.pdf';
                if ($type === 'Pribadi') {
                    $caseData['file_buku_nikah'] = 'case-files/dummy_buku_nikah.pdf';
                } else {
                    $caseData['file_surat_perintah'] = 'case-files/dummy_surat_perintah.pdf';
                }
            }

            $case = NotarisCase::create($caseData);
            
            // Set custom created_at/updated_at and save
            $case->created_at = $created_at;
            $case->updated_at = $created_at->copy()->addDays(rand(1, 14));
            $case->timestamps = false;
            $case->save();

            if ($hasDummyFiles) {
                // Ensure physical dummy files exist in storage/app/public/case-files
                @mkdir(storage_path('app/public/case-files'), 0755, true);
                @file_put_contents(storage_path('app/public/case-files/dummy_ktp.pdf'), '%PDF-1.4 ... Dummy KTP PDF Content');
                @file_put_contents(storage_path('app/public/case-files/dummy_npwp.pdf'), '%PDF-1.4 ... Dummy NPWP PDF Content');
                @file_put_contents(storage_path('app/public/case-files/dummy_kk.pdf'), '%PDF-1.4 ... Dummy KK PDF Content');
                @file_put_contents(storage_path('app/public/case-files/dummy_buku_nikah.pdf'), '%PDF-1.4 ... Dummy Buku Nikah PDF Content');
                @file_put_contents(storage_path('app/public/case-files/dummy_surat_perintah.pdf'), '%PDF-1.4 ... Dummy Surat Perintah PDF Content');

                // Also populate CaseDocument database table for requirement files
                foreach (['file_ktp', 'file_npwp', 'file_kk', 'file_buku_nikah', 'file_surat_perintah'] as $field) {
                    if (!empty($case->$field)) {
                        $doc = CaseDocument::create([
                            'id_kasus' => $case->id_kasus,
                            'filename' => basename($case->$field),
                            'filepath' => $case->$field,
                            'uploaded_by' => $admin?->id,
                        ]);
                        $doc->created_at = $created_at;
                        $doc->updated_at = $created_at;
                        $doc->timestamps = false;
                        $doc->save();
                    }
                }

                // Add a supporting/finished document to CaseDocument
                @mkdir(storage_path('app/public/case-documents'), 0755, true);
                $docPath = 'case-documents/dokumen_pendukung_dummy.pdf';
                @file_put_contents(storage_path('app/public/' . $docPath), '%PDF-1.4 ... Dummy Supporting Document Content');
                
                $supportDoc = CaseDocument::create([
                    'id_kasus' => $case->id_kasus,
                    'filename' => 'dokumen_pendukung_dummy.pdf',
                    'filepath' => $docPath,
                    'uploaded_by' => $admin?->id,
                ]);
                $supportDoc->created_at = $created_at;
                $supportDoc->updated_at = $created_at;
                $supportDoc->timestamps = false;
                $supportDoc->save();
            }

            $pay = Payment::create([
                'id_kasus' => $case->id_kasus,
                'amount' => $amount,
                'status' => $payStatus[$status],
            ]);
            $pay->created_at = $created_at;
            $pay->updated_at = $created_at;
            $pay->timestamps = false;
            $pay->save();

            // 4. Create Archives
            $archive = \App\Models\Archive::create([
                'client_name' => $case->client_name,
                'id_kasus' => $case->id_kasus,
                'id_klien' => $case->id_klien,
                'folder_location' => "/Arsip/2026/$type/" . Str::slug($case->client_name),
            ]);
            $archive->created_at = $created_at;
            $archive->updated_at = $created_at;
            $archive->timestamps = false;
            $archive->save();
        }
    }
}

