@@ .. @@
 require_once __DIR__ . '/../includes/auth_check.php'; // Pastikan user sudah login
+require_once __DIR__ . '/../includes/user_middleware.php';
 require_once __DIR__ . '/../config/db.php'; // Sertakan file koneksi database
@@ .. @@
 try {
     $conn = $db; // Menggunakan koneksi $db dari db.php
+    global $user_id;

     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
@@ .. @@
         if ($bahan_baku_id) {
             // Update Bahan Baku
-            $stmt = $conn->prepare("UPDATE raw_materials SET name = ?, brand = ?, type = ?, unit = ?, default_package_quantity = ?, purchase_price_per_unit = ?, updated_at = CURRENT_TIMESTAMP() WHERE id = ?");
-            if ($stmt->execute([$name, $brand, $type, $unit, $purchase_size, $purchase_price_per_unit, $bahan_baku_id])) {
+            $stmt = $conn->prepare("UPDATE raw_materials SET name = ?, brand = ?, type = ?, unit = ?, default_package_quantity = ?, purchase_price_per_unit = ?, updated_at = CURRENT_TIMESTAMP() WHERE id = ? AND user_id = ?");
+            if ($stmt->execute([$name, $brand, $type, $unit, $purchase_size, $purchase_price_per_unit, $bahan_baku_id, $user_id])) {
                 $_SESSION['bahan_baku_message'] = ['text' => 'Bahan baku berhasil diperbarui!', 'type' => 'success'];
             } else {
                 $_SESSION['bahan_baku_message'] = ['text' => 'Gagal memperbarui bahan baku.', 'type' => 'error'];
@@ -1,7 +1,7 @@
         } else {
             // Tambah Bahan Baku Baru
             // Cek duplikasi nama dan brand (boleh sama nama jika brand berbeda)
-            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM raw_materials WHERE name = ? AND brand = ?");
-            $stmtCheck->execute([$name, $brand]);
+            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM raw_materials WHERE name = ? AND brand = ? AND user_id = ?");
+            $stmtCheck->execute([$name, $brand, $user_id]);
             if ($stmtCheck->fetchColumn() > 0) {
                 $_SESSION['bahan_baku_message'] = ['text' => 'Kombinasi nama dan brand sudah ada. Gunakan kombinasi lain.', 'type' => 'error'];
                 header("Location: /cornerbites-sia/pages/bahan_baku.php");
                 exit();
             }

-            $stmt = $conn->prepare("INSERT INTO raw_materials (name, brand, type, unit, default_package_quantity, purchase_price_per_unit) VALUES (?, ?, ?, ?, ?, ?)");
-            if ($stmt->execute([$name, $brand, $type, $unit, $purchase_size, $purchase_price_per_unit])) {
+            $stmt = $conn->prepare("INSERT INTO raw_materials (name, brand, type, unit, default_package_quantity, purchase_price_per_unit, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
+            if ($stmt->execute([$name, $brand, $type, $unit, $purchase_size, $purchase_price_per_unit, $user_id])) {
                 $_SESSION['bahan_baku_message'] = ['text' => 'Bahan baku baru berhasil ditambahkan!', 'type' => 'success'];
             } else {
                 $_SESSION['bahan_baku_message'] = ['text' => 'Gagal menambahkan bahan baku baru.', 'type' => 'error'];
@@ .. @@
         // Cek apakah bahan baku ini digunakan di resep produk mana pun
-        $stmtCheckRecipe = $conn->prepare("SELECT COUNT(*) FROM product_recipes WHERE raw_material_id = ?");
-        $stmtCheckRecipe->execute([$bahan_baku_id]);
+        $stmtCheckRecipe = $conn->prepare("SELECT COUNT(*) FROM product_recipes WHERE raw_material_id = ? AND user_id = ?");
+        $stmtCheckRecipe->execute([$bahan_baku_id, $user_id]);
         if ($stmtCheckRecipe->fetchColumn() > 0) {
             $_SESSION['bahan_baku_message'] = ['text' => 'Tidak bisa menghapus bahan baku karena sudah digunakan dalam resep produk. Hapus resep yang menggunakan bahan baku ini terlebih dahulu.', 'type' => 'error'];
             header("Location: /cornerbites-sia/pages/bahan_baku.php");
             exit();
         }

-        $stmt = $conn->prepare("DELETE FROM raw_materials WHERE id = ?");
-        if ($stmt->execute([$bahan_baku_id])) {
+        $stmt = $conn->prepare("DELETE FROM raw_materials WHERE id = ? AND user_id = ?");
+        if ($stmt->execute([$bahan_baku_id, $user_id])) {
             $_SESSION['bahan_baku_message'] = ['text' => 'Bahan baku berhasil dihapus!', 'type' => 'success'];
         } else {
             $_SESSION['bahan_baku_message'] = ['text' => 'Gagal menghapus bahan baku.', 'type' => 'error'];