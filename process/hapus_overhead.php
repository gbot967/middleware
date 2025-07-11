@@ .. @@
 session_start();
 require_once __DIR__ . '/../includes/auth_check.php';
+require_once __DIR__ . '/../includes/user_middleware.php';
 require_once __DIR__ . '/../config/db.php';

 if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     try {
         $conn = $db;
+        global $user_id;
         $type = $_POST['type'] ?? '';

         if ($type == 'overhead') {
@@ .. @@
             // Soft delete - set is_active = 0
-            $stmt = $conn->prepare("UPDATE overhead_costs SET is_active = 0, updated_at = NOW() WHERE id = ?");
-            $stmt->execute([$overhead_id]);
+            $stmt = $conn->prepare("UPDATE overhead_costs SET is_active = 0, updated_at = NOW() WHERE id = ? AND user_id = ?");
+            $stmt->execute([$overhead_id, $user_id]);

             if ($stmt->rowCount() > 0) {
@@ .. @@
            // Soft delete - set is_active = 0
-            $stmt = $conn->prepare("UPDATE labor_costs SET is_active = 0, updated_at = NOW() WHERE id = ?");
-            $stmt->execute([$labor_id]);
+            $stmt = $conn->prepare("UPDATE labor_costs SET is_active = 0, updated_at = NOW() WHERE id = ? AND user_id = ?");
+            $stmt->execute([$labor_id, $user_id]);

             if ($stmt->rowCount() > 0) {