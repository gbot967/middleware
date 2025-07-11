@@ .. @@
 session_start();
 require_once __DIR__ . '/../includes/auth_check.php';
 require_once __DIR__ . '/../includes/user_middleware.php';
 require_once __DIR__ . '/../config/db.php';

 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     try {
         $conn = $db;
+        global $user_id;
         $type = $_POST['type'] ?? '';

         if ($type === 'overhead') {
@@ .. @@
             if (!empty($overhead_id)) {
                 // Update overhead
-                $stmt = $conn->prepare("UPDATE overhead_costs SET name = ?, description = ?, amount = ?, allocation_method = ?, estimated_uses = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
-                if ($stmt->execute([$name, $description, $amount, $allocation_method, $estimated_uses, $overhead_id])) {
+                $stmt = $conn->prepare("UPDATE overhead_costs SET name = ?, description = ?, amount = ?, allocation_method = ?, estimated_uses = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
+                if ($stmt->execute([$name, $description, $amount, $allocation_method, $estimated_uses, $overhead_id, $user_id])) {
                     $_SESSION['overhead_message'] = [
                         'text' => 'Overhead berhasil diperbarui.',
                         'type' => 'success'
@@ .. @@
             } else {
                 // Insert new overhead
-                $stmt = $conn->prepare("INSERT INTO overhead_costs (name, description, amount, allocation_method, estimated_uses, created_at, updated_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
-                if ($stmt->execute([$name, $description, $amount, $allocation_method, $estimated_uses])) {
+                $stmt = $conn->prepare("INSERT INTO overhead_costs (name, description, amount, allocation_method, estimated_uses, created_at, updated_at, user_id) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, ?)");
+                if ($stmt->execute([$name, $description, $amount, $allocation_method, $estimated_uses, $user_id])) {
                     $_SESSION['overhead_message'] = [
                         'text' => 'Overhead berhasil ditambahkan.',
                         'type' => 'success'
@@ .. @@
            if (!empty($labor_id)) {
                 // Update labor
-                $stmt = $conn->prepare("UPDATE labor_costs SET position_name = ?, hourly_rate = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
-                if ($stmt->execute([$position_name, $hourly_rate, $labor_id])) {
+                $stmt = $conn->prepare("UPDATE labor_costs SET position_name = ?, hourly_rate = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
+                if ($stmt->execute([$position_name, $hourly_rate, $labor_id, $user_id])) {
                     $_SESSION['overhead_message'] = [
                         'text' => 'Data tenaga kerja berhasil diperbarui.',
                         'type' => 'success'
@@ .. @@
            } else {
                 // Insert new labor
-                $stmt = $conn->prepare("INSERT INTO labor_costs (position_name, hourly_rate, created_at, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
-                if ($stmt->execute([$position_name, $hourly_rate])) {
+                $stmt = $conn->prepare("INSERT INTO labor_costs (position_name, hourly_rate, created_at, updated_at, user_id) VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, ?)");
+                if ($stmt->execute([$position_name, $hourly_rate, $user_id])) {
                     $_SESSION['overhead_message'] = [
                         'text' => 'Data tenaga kerja berhasil ditambahkan.',
                         'type' => 'success'