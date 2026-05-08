<?php
/**
 * Direct migration + seed script (bypasses artisan for Laragon env)
 * Run: php setup_db.php
 */

$host = '127.0.0.1';
$port = 3306;
$db   = 'db_hugo_assistant';
$user = 'root';
$pass = '242224';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✅ Connected to MySQL\n";

    // Create DB if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db`");
    echo "✅ Database '$db' ready\n";

    // ── USERS table ──
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `username` VARCHAR(100) UNIQUE,
            `email` VARCHAR(255) UNIQUE NOT NULL,
            `email_verified_at` TIMESTAMP NULL,
            `password` VARCHAR(255) NOT NULL,
            `role` ENUM('admin','notaris','staff','freelancer') DEFAULT 'freelancer',
            `custom_id` VARCHAR(50) NULL,
            `avatar_url` VARCHAR(500) NULL,
            `remember_token` VARCHAR(100) NULL,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL
        ) ENGINE=InnoDB
    ");
    echo "✅ Table: users\n";

    // ── CASES table ──
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `cases` (
            `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `client_name` VARCHAR(255) NOT NULL,
            `phone` VARCHAR(30) NULL,
            `address` TEXT NULL,
            `case_name` VARCHAR(255) NOT NULL,
            `type` ENUM('PT','CV','Pribadi') NOT NULL,
            `status` ENUM('proses','selesai','tertunda') NOT NULL DEFAULT 'proses',
            `deadline` DATE NOT NULL,
            `file_ktp` VARCHAR(500) NULL,
            `file_npwp` VARCHAR(500) NULL,
            `file_kk` VARCHAR(500) NULL,
            `file_surat_tanah` VARCHAR(500) NULL,
            `file_surat_perintah` VARCHAR(500) NULL,
            `created_by` BIGINT UNSIGNED NULL,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL,
            FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB
    ");
    echo "✅ Table: cases\n";

    // ── PAYMENTS table ──
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `payments` (
            `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `case_id` BIGINT UNSIGNED NOT NULL,
            `amount` VARCHAR(100) NULL,
            `status` ENUM('lunas','sebagian','belum') NOT NULL DEFAULT 'belum',
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL,
            FOREIGN KEY (`case_id`) REFERENCES `cases`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB
    ");
    echo "✅ Table: payments\n";

    // ── PAYMENT HISTORIES table ──
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `payment_histories` (
            `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `payment_id` BIGINT UNSIGNED NOT NULL,
            `from_status` VARCHAR(30) NOT NULL,
            `to_status` VARCHAR(30) NOT NULL,
            `note` VARCHAR(500) NULL,
            `changed_by` BIGINT UNSIGNED NULL,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL,
            FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB
    ");
    echo "✅ Table: payment_histories\n";

    // ── OTHER LARAVEL TABLES ──
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
            `email` VARCHAR(255) PRIMARY KEY,
            `token` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP NULL
        ) ENGINE=InnoDB
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
            `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `tokenable_type` VARCHAR(255) NOT NULL,
            `tokenable_id` BIGINT UNSIGNED NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `token` VARCHAR(64) UNIQUE NOT NULL,
            `abilities` TEXT NULL,
            `last_used_at` TIMESTAMP NULL,
            `expires_at` TIMESTAMP NULL,
            `created_at` TIMESTAMP NULL,
            `updated_at` TIMESTAMP NULL
        ) ENGINE=InnoDB
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `sessions` (
            `id` VARCHAR(255) PRIMARY KEY,
            `user_id` BIGINT UNSIGNED NULL,
            `ip_address` VARCHAR(45) NULL,
            `user_agent` TEXT NULL,
            `payload` LONGTEXT NOT NULL,
            `last_activity` INT NOT NULL,
            INDEX `sessions_user_id_index` (`user_id`),
            INDEX `sessions_last_activity_index` (`last_activity`)
        ) ENGINE=InnoDB
    ");
    echo "✅ Laravel support tables ready\n";

    // ── MIGRATIONS table (mark as run) ──
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `migrations` (
            `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            `migration` VARCHAR(255) NOT NULL,
            `batch` INT NOT NULL
        ) ENGINE=InnoDB
    ");

    // ── SEED USERS ──
    $checkUser = $pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
    if ($checkUser == 0) {
        $hash = password_hash('password', PASSWORD_BCRYPT);
        $now  = date('Y-m-d H:i:s');

        $pdo->prepare("INSERT INTO `users` (name,username,email,password,role,custom_id,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)")->execute(['Admin HUGO','admin','admin@hugo.id',$hash,'admin','ADM001',$now,$now]);
        $adminId = $pdo->lastInsertId();
        $pdo->prepare("INSERT INTO `users` (name,username,email,password,role,custom_id,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)")->execute(['Notaris HUGO','notaris','notaris@hugo.id',$hash,'notaris','NOT001',$now,$now]);
        $pdo->prepare("INSERT INTO `users` (name,username,email,password,role,custom_id,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?)")->execute(['Staff Budi','staff.budi','budi@hugo.id',$hash,'staff','STA001',$now,$now]);
        echo "✅ Default users seeded (password: 'password')\n";

        // ── SEED CASES ──
        $casesData = [
            ['PT Maju Jaya','08111222333','Jl. Sudirman 10, Jakarta','Pendirian PT','PT','selesai','2026-02-10','Rp 15.000.000','lunas','2026-01-15'],
            ['CV Berkah Utama','08222333444','Jl. Thamrin 5, Jakarta','Perubahan CV','CV','proses','2026-02-28','Rp 8.500.000','sebagian','2026-02-01'],
            ['Andi Wijaya','08333444555','Jl. Kebon Jeruk 20, Jakarta','Akta Jual Beli Tanah','Pribadi','tertunda','2026-03-05','Rp 5.000.000','belum','2026-02-10'],
            ['Siti Rahayu','08444555666','Jl. Kuningan 15, Jakarta','Perjanjian Kerjasama','Pribadi','selesai','2026-01-20','Rp 3.000.000','lunas','2026-01-05'],
            ['PT Sinar Mas','08555666777','Jl. M.H. Thamrin 51, Jakarta','Akuisisi Saham','PT','proses','2026-03-15','Rp 25.000.000','sebagian','2026-02-15'],
            ['CV Jaya Abadi','08666777888','Jl. Gatot Subroto 88, Jakarta','Likuidasi CV','CV','selesai','2025-12-10','Rp 6.000.000','lunas','2025-11-20'],
            ['Bambang Hartono','08777888999','Jl. Cempaka Putih 7, Jakarta','Hibah Tanah','Pribadi','proses','2026-02-22','Rp 4.500.000','belum','2026-02-18'],
            ['PT Nusantara Corp','08888999000','Jl. Asia Afrika 1, Bandung','Perubahan Anggaran Dasar','PT','tertunda','2026-04-01','Rp 12.000.000','belum','2026-02-20'],
        ];

        foreach ($casesData as [$client,$phone,$addr,$casename,$type,$status,$deadline,$amount,$payStatus,$createdAt]) {
            $ts = $createdAt . ' 09:00:00';
            $pdo->prepare("INSERT INTO `cases` (client_name,phone,address,case_name,type,status,deadline,created_by,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?)")
                ->execute([$client,$phone,$addr,$casename,$type,$status,$deadline,$adminId,$ts,$ts]);
            $caseId = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO `payments` (case_id,amount,status,created_at,updated_at) VALUES (?,?,?,?,?)")
                ->execute([$caseId,$amount,$payStatus,$ts,$ts]);
            echo "  → Kasus: $client ($type - $status)\n";
        }
        echo "✅ 8 sample cases + payments seeded\n";
    } else {
        echo "ℹ️  Users already exist — skipping seed\n";
    }

    echo "\n🎉 Setup selesai! Buka http://localhost/HUGO-Assistant/public/login\n";
    echo "   Login: username='admin' password='password'\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
