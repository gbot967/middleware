@@ .. @@
 <?php
 require_once __DIR__ . '/../includes/auth_check.php';
+require_once __DIR__ . '/../includes/user_middleware.php';
 require_once __DIR__ . '/../config/db.php';
@@ .. @@
 try {
     $conn = $db;
+    global $user_id;

     // Total Products
-    $stmt = $conn->query("SELECT COUNT(*) as total FROM products");
+    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE user_id = ?");
+    $stmt->execute([$user_id]);
     $result = $stmt->fetch();
     $totalProducts = $result ? ($result['total'] ?? 0) : 0;

     // Total Raw Materials (Bahan Baku + Kemasan)
-    $stmt = $conn->query("SELECT COUNT(*) as total FROM raw_materials");
+    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM raw_materials WHERE user_id = ?");
+    $stmt->execute([$user_id]);
     $result = $stmt->fetch();
     $totalRawMaterials = $result ? ($result['total'] ?? 0) : 0;

     // Total Bahan Baku only
-    $stmt = $conn->query("SELECT COUNT(*) as total FROM raw_materials WHERE type = 'bahan'");
+    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM raw_materials WHERE type = 'bahan' AND user_id = ?");
+    $stmt->execute([$user_id]);
     $result = $stmt->fetch();
     $totalBahanBaku = $result ? ($result['total'] ?? 0) : 0;

     // Total Kemasan only  
-    $stmt = $conn->query("SELECT COUNT(*) as total FROM raw_materials WHERE type = 'kemasan'");
+    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM raw_materials WHERE type = 'kemasan' AND user_id = ?");
+    $stmt->execute([$user_id]);
     $result = $stmt->fetch();
     $totalKemasan = $result ? ($result['total'] ?? 0) : 0;

     // Total Recipes Active (products that have recipes)
-    $stmt = $conn->query("SELECT COUNT(DISTINCT product_id) as total FROM product_recipes");
+    $stmt = $conn->prepare("SELECT COUNT(DISTINCT product_id) as total FROM product_recipes WHERE user_id = ?");
+    $stmt->execute([$user_id]);
     $result = $stmt->fetch();
     $totalRecipes = $result ? ($result['total'] ?? 0) : 0;

     // Total Labor Positions and Cost (hanya yang aktif)
     try {
         $stmt = $conn->query("SHOW TABLES LIKE 'labor_costs'");
         if ($stmt->rowCount() > 0) {
-            $stmt = $conn->query("SELECT COUNT(*) as total, COALESCE(SUM(hourly_rate), 0) as total_cost FROM labor_costs WHERE is_active = 1");
+            $stmt = $conn->prepare("SELECT COUNT(*) as total, COALESCE(SUM(hourly_rate), 0) as total_cost FROM labor_costs WHERE is_active = 1 AND user_id = ?");
+            $stmt->execute([$user_id]);
             $result = $stmt->fetch();
             $totalLaborPositions = $result ? ($result['total'] ?? 0) : 0;
             $totalLaborCost = $result ? ($result['total_cost'] ?? 0) : 0;
@@ .. @@
     // Total Overhead Items and Cost (hanya yang aktif)
     try {
         $stmt = $conn->query("SHOW TABLES LIKE 'overhead_costs'");
         if ($stmt->rowCount() > 0) {
-            $stmt = $conn->query("SELECT COUNT(*) as total, COALESCE(SUM(amount), 0) as total_cost FROM overhead_costs WHERE is_active = 1");
+            $stmt = $conn->prepare("SELECT COUNT(*) as total, COALESCE(SUM(amount), 0) as total_cost FROM overhead_costs WHERE is_active = 1 AND user_id = ?");
+            $stmt->execute([$user_id]);
             $result = $stmt->fetch();
             $totalOverheadItems = $result ? ($result['total'] ?? 0) : 0;
             $totalOverheadCost = $result ? ($result['total_cost'] ?? 0) : 0;
@@ .. @@
     // Calculate Average HPP (only products with calculated HPP)
-    $stmt = $conn->query("SELECT AVG(cost_price) as avg_hpp FROM products WHERE cost_price > 0 AND cost_price IS NOT NULL");
+    $stmt = $conn->prepare("SELECT AVG(cost_price) as avg_hpp FROM products WHERE cost_price > 0 AND cost_price IS NOT NULL AND user_id = ?");
+    $stmt->execute([$user_id]);
     $result = $stmt->fetch();
     $avgHPP = $result ? ($result['avg_hpp'] ?? 0) : 0;

     // Calculate Average Margin (only products with both cost and sale price)
-    $stmt = $conn->query("SELECT AVG(((sale_price - cost_price) / sale_price) * 100) as avg_margin FROM products WHERE sale_price > 0 AND cost_price > 0 AND sale_price IS NOT NULL AND cost_price IS NOT NULL");
+    $stmt = $conn->prepare("SELECT AVG(((sale_price - cost_price) / sale_price) * 100) as avg_margin FROM products WHERE sale_price > 0 AND cost_price > 0 AND sale_price IS NOT NULL AND cost_price IS NOT NULL AND user_id = ?");
+    $stmt->execute([$user_id]);
     $result = $stmt->fetch();
     $avgMargin = $result ? ($result['avg_margin'] ?? 0) : 0;

     // Count Profitable Products (profit > 0)
-    $stmt = $conn->query("SELECT COUNT(*) as total FROM products WHERE sale_price > cost_price AND sale_price > 0 AND cost_price > 0 AND sale_price IS NOT NULL AND cost_price IS NOT NULL");
+    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE sale_price > cost_price AND sale_price > 0 AND cost_price > 0 AND sale_price IS NOT NULL AND cost_price IS NOT NULL AND user_id = ?");
+    $stmt->execute([$user_id]);
     $result = $stmt->fetch();
     $profitableProducts = $result ? ($result['total'] ?? 0) : 0;

     // Pagination for profitability ranking
@@ .. @@
     $ranking_offset = ($ranking_page - 1) * $ranking_limit;

     // Count total products with HPP for pagination  
-    $stmt = $conn->query("SELECT COUNT(*) as total FROM products WHERE cost_price > 0 AND cost_price IS NOT NULL");
+    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE cost_price > 0 AND cost_price IS NOT NULL AND user_id = ?");
+    $stmt->execute([$user_id]);
     $result = $stmt->fetch();
     $total_products_ranking = $result ? ($result['total'] ?? 0) : 0;
     $total_ranking_pages = ceil($total_products_ranking / $ranking_limit);

     // Profitability Ranking with pagination (products with calculated HPP)
     if ($total_products_ranking > 0) {
-        $stmt = $conn->prepare("SELECT name, cost_price, 
+        $stmt = $conn->prepare("SELECT name, cost_price,
                               COALESCE(sale_price, 0) as sale_price, 
                               (COALESCE(sale_price, 0) - cost_price) as profit, 
                               CASE 
@@ -1,7 +1,7 @@
                                 WHEN COALESCE(sale_price, 0) > cost_price THEN 'Menguntungkan' 
                                 ELSE 'Rugi' 
                               END as status 
-                              FROM products WHERE cost_price > 0 AND cost_price IS NOT NULL
+                              FROM products WHERE cost_price > 0 AND cost_price IS NOT NULL AND user_id = ?
                               ORDER BY cost_price DESC LIMIT :limit OFFSET :offset");
+        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
         $stmt->bindValue(':limit', $ranking_limit, PDO::PARAM_INT);
         $stmt->bindValue(':offset', $ranking_offset, PDO::PARAM_INT);
         $stmt->execute();