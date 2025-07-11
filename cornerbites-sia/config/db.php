<?php
// config/db.php

/**
 * Class Database untuk mengelola koneksi ke database MySQL menggunakan PDO.
 */
class Database {
    // Kredensial database - GANTI DENGAN KREDENSIAL ANDA YANG SEBENARNYA
    private $host = 'localhost';
    private $db_name = 'corner_bites_sia'; // Pastikan nama database ini sudah Anda buat
    private $username = 'root';
    private $password = ''; // Kosongkan jika tidak ada password, atau isi password Anda
    private $conn; // Variabel untuk menyimpan objek koneksi PDO

    /**
     * Mendapatkan koneksi database PDO.
     * Mengatur mode error ke exception dan mode fetch default ke asosiatif.
     *
     * @return PDO|null Objek PDO jika koneksi berhasil, null jika gagal.
     */
    public function connect() {
        $this->conn = null; // Reset koneksi sebelumnya

        try {
            // Data Source Name (DSN) dengan charset
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8";
            
            // Buat objek PDO
            $this->conn = new PDO($dsn, $this->username, $this->password);

            // Atur atribut PDO untuk penanganan error dan mode fetch
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Perintah set names utf8 tidak lagi mutlak diperlukan jika charset=utf8 sudah di DSN,
            // tetapi tidak ada salahnya jika dibiarkan.
            // $this->conn->exec("set names utf8");

            // Debug: Test database connection
            error_log("Database connection successful to: " . $this->db_name);

            // Test query untuk memastikan tabel ada
            try {
                $testStmt = $this->conn->query("SHOW TABLES");
                $tables = $testStmt->fetchAll(PDO::FETCH_COLUMN);
                error_log("Available tables: " . implode(", ", $tables));
            } catch (PDOException $e) {
                error_log("Error checking tables: " . $e->getMessage());
            }

            // Add user_id columns to tables if they don't exist
            $tables_to_modify = [
                'products' => 'ALTER TABLE products ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
                'raw_materials' => 'ALTER TABLE raw_materials ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1', 
                'overhead_costs' => 'ALTER TABLE overhead_costs ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
                'labor_costs' => 'ALTER TABLE labor_costs ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
                'transactions' => 'ALTER TABLE transactions ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
                'transaction_items' => 'ALTER TABLE transaction_items ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
                'product_recipes' => 'ALTER TABLE product_recipes ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1'
            ];

            foreach ($tables_to_modify as $table => $alter_query) {
                try {
                    // Check if table exists first
                    $table_exists = $this->conn->prepare("SHOW TABLES LIKE '$table'");
                    $table_exists->execute();

                    if ($table_exists->rowCount() > 0) {
                        // Check if user_id column exists
                        $check_column = $this->conn->prepare("SHOW COLUMNS FROM `$table` LIKE 'user_id'");
                        $check_column->execute();

                        if ($check_column->rowCount() == 0) {
                            $this->conn->exec($alter_query);
                            // Add foreign key constraint
                            $this->conn->exec("ALTER TABLE `$table` ADD CONSTRAINT `fk_{$table}_user_id` FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
                            error_log("Added user_id column to table: $table");
                        }
                    }
                } catch (PDOException $e) {
                    error_log("Error modifying table $table: " . $e->getMessage());
                }
            }


        } catch(PDOException $e) {
            // Tangani error koneksi database
            error_log("Koneksi database gagal: " . $e->getMessage()); // Catat error ke log
            die("Koneksi database gagal: " . $e->getMessage()); // Hentikan skrip jika koneksi sangat krusial
        }

        return $this->conn; // Kembalikan objek koneksi
    }
}

// Inisialisasi koneksi database
$database = new Database();
$db = $database->connect();

// PENTING: Baris session_start() telah dihapus dari sini.
// session_start() hanya akan dipanggil di includes/auth_check.php.
?>