@@ .. @@
             // Add user_id columns to tables if they don't exist
-            $tables_to_modify = [
-                'products' => 'ALTER TABLE products ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
-                'raw_materials' => 'ALTER TABLE raw_materials ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1', 
-                'overhead_costs' => 'ALTER TABLE overhead_costs ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
-                'labor_costs' => 'ALTER TABLE labor_costs ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
-                'transactions' => 'ALTER TABLE transactions ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
-                'transaction_items' => 'ALTER TABLE transaction_items ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1',
-                'product_recipes' => 'ALTER TABLE product_recipes ADD COLUMN user_id INT(11) NOT NULL DEFAULT 1'
-            ];
-
-            foreach ($tables_to_modify as $table => $alter_query) {
-                try {
-                    // Check if table exists first
-                    $table_exists = $this->conn->prepare("SHOW TABLES LIKE '$table'");
-                    $table_exists->execute();
-
-                    if ($table_exists->rowCount() > 0) {
-                        // Check if user_id column exists
-                        $check_column = $this->conn->prepare("SHOW COLUMNS FROM `$table` LIKE 'user_id'");
-                        $check_column->execute();
-
-                        if ($check_column->rowCount() == 0) {
-                            $this->conn->exec($alter_query);
-                            // Add foreign key constraint
-                            $this->conn->exec("ALTER TABLE `$table` ADD CONSTRAINT `fk_{$table}_user_id` FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
-                            error_log("Added user_id column to table: $table");
-                        }
-                    }
-                } catch (PDOException $e) {
-                    error_log("Error modifying table $table: " . $e->getMessage());
-                }
-            }
+            // User isolation sudah ada di database schema
+            error_log("Database connection established with user isolation support");