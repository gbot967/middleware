@@ .. @@
 require_once __DIR__ . '/../includes/auth_check.php';
+require_once __DIR__ . '/../includes/user_middleware.php';
 require_once __DIR__ . '/../config/db.php'; // Sertakan file koneksi database
@@ .. @@
 try {
     $conn = $db;
+    global $user_id;

     // Pagination setup
@@ .. @@
     // Build WHERE clause for search
-    $whereClause = "WHERE 1=1";
+    $whereClause = "WHERE user_id = :user_id";
     $params = [];
+    $params[':user_id'] = $user_id;

     // Search filter
     if (!empty($search)) {
         $whereClause .= " AND name LIKE :search";
         $params[':search'] = '%' . $search . '%';
     }