@@ .. @@
 require_once __DIR__ . '/../includes/auth_check.php'; // Pastikan user sudah login
+require_once __DIR__ . '/../includes/user_middleware.php';
 require_once __DIR__ . '/../config/db.php'; // Sertakan file koneksi database
@@ .. @@
 try {
     $conn = $db; // Menggunakan koneksi $db dari db.php
+    global $user_id;

     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
@@ .. @@
         if ($product_id) {
             // --- Update Produk --- (hanya update name dan unit, harga jual diatur di HPP)
-            $stmt = $conn->prepare("UPDATE products SET name = ?, unit = ? WHERE id = ?");
-            if ($stmt->execute([$name, $unit, $product_id])) {
+            $stmt = $conn->prepare("UPDATE products SET name = ?, unit = ? WHERE id = ? AND user_id = ?");
+            if ($stmt->execute([$name, $unit, $product_id, $user_id])) {
                 $_SESSION['product_message'] = ['text' => 'Produk berhasil diperbarui! Harga jual dapat diatur di halaman Manajemen Resep & HPP.', 'type' => 'success'];
             } else {
                 $_SESSION['product_message'] = ['text' => 'Gagal memperbarui produk.', 'type' => 'error'];
             }
         } else {
             // --- Tambah Produk Baru ---
-            $stmt = $conn->prepare("INSERT INTO products (name, unit, sale_price) VALUES (?, ?, ?)");
-            if ($stmt->execute([$name, $unit, $sale_price])) {
+            $stmt = $conn->prepare("INSERT INTO products (name, unit, sale_price, user_id) VALUES (?, ?, ?, ?)");
+            if ($stmt->execute([$name, $unit, $sale_price, $user_id])) {
                 $_SESSION['product_message'] = ['text' => 'Produk baru berhasil ditambahkan! Selanjutnya buat resep dan atur harga jual di halaman Manajemen Resep & HPP.', 'type' => 'success'];
             } else {
                 $_SESSION['product_message'] = ['text' => 'Gagal menambahkan produk baru.', 'type' => 'error'];
@@ .. @@
         // Cek apakah produk terkait dengan resep atau batch produksi
-        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM product_recipes WHERE product_id = ?");
-        $stmtCheck->execute([$product_id]);
+        $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM product_recipes WHERE product_id = ? AND user_id = ?");
+        $stmtCheck->execute([$product_id, $user_id]);
         if ($stmtCheck->fetchColumn() > 0) {
             $_SESSION['product_message'] = ['text' => 'Tidak bisa menghapus produk karena sudah memiliki resep yang terkait. Hapus resep terlebih dahulu.', 'type' => 'error'];
             header("Location: /cornerbites-sia/pages/produk.php");
             exit();
         }

-        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
-        if ($stmt->execute([$product_id])) {
+        $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
+        if ($stmt->execute([$product_id, $user_id])) {
             $_SESSION['product_message'] = ['text' => 'Produk berhasil dihapus!', 'type' => 'success'];
         } else {
             $_SESSION['product_message'] = ['text' => 'Gagal menghapus produk.', 'type' => 'error'];