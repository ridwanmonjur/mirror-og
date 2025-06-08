<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         // Single table examples
//         $this->addIndexIfNotExists('users', 'email', 'users_email_index');
//         $this->addIndexIfNotExists('users', ['first_name', 'last_name'], 'users_name_index');
//         $this->addUniqueIndexIfNotExists('users', 'username', 'users_username_unique');
        
//         // Multiple tables with their indexes
//         $indexes = [
//             'posts' => [
//                 ['columns' => 'user_id', 'name' => 'posts_user_id_index'],
//                 ['columns' => ['category_id', 'status'], 'name' => 'posts_category_status_index'],
//                 ['columns' => 'slug', 'name' => 'posts_slug_unique', 'type' => 'unique'],
//                 ['columns' => ['title', 'content'], 'name' => 'posts_search_fulltext', 'type' => 'fulltext']
//             ],
//             'orders' => [
//                 ['columns' => 'user_id', 'name' => 'orders_user_id_index'],
//                 ['columns' => ['status', 'created_at'], 'name' => 'orders_status_date_index'],
//                 ['columns' => 'order_number', 'name' => 'orders_number_unique', 'type' => 'unique']
//             ],
//             'products' => [
//                 ['columns' => 'category_id', 'name' => 'products_category_id_index'],
//                 ['columns' => 'sku', 'name' => 'products_sku_unique', 'type' => 'unique'],
//                 ['columns' => ['name', 'description'], 'name' => 'products_search_fulltext', 'type' => 'fulltext']
//             ]
//         ];

//         foreach ($indexes as $tableName => $tableIndexes) {
//             foreach ($tableIndexes as $indexData) {
//                 $type = $indexData['type'] ?? 'index';
                
//                 switch ($type) {
//                     case 'unique':
//                         $this->addUniqueIndexIfNotExists($tableName, $indexData['columns'], $indexData['name']);
//                         break;
//                     case 'fulltext':
//                         $this->addFullTextIndexIfNotExists($tableName, $indexData['columns'], $indexData['name']);
//                         break;
//                     default:
//                         $this->addIndexIfNotExists($tableName, $indexData['columns'], $indexData['name']);
//                         break;
//                 }
//             }
//         }
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         // Drop single indexes
//         $this->dropIndexIfExists('users', 'users_email_index');
//         $this->dropIndexIfExists('users', 'users_name_index');
//         $this->dropUniqueIndexIfExists('users', 'users_username_unique');
        
//         // Drop indexes from multiple tables
//         $indexesToDrop = [
//             'posts' => [
//                 ['name' => 'posts_user_id_index', 'type' => 'index'],
//                 ['name' => 'posts_category_status_index', 'type' => 'index'],
//                 ['name' => 'posts_slug_unique', 'type' => 'unique'],
//                 ['name' => 'posts_search_fulltext', 'type' => 'fulltext']
//             ],
//             'orders' => [
//                 ['name' => 'orders_user_id_index', 'type' => 'index'],
//                 ['name' => 'orders_status_date_index', 'type' => 'index'],
//                 ['name' => 'orders_number_unique', 'type' => 'unique']
//             ],
//             'products' => [
//                 ['name' => 'products_category_id_index', 'type' => 'index'],
//                 ['name' => 'products_sku_unique', 'type' => 'unique'],
//                 ['name' => 'products_search_fulltext', 'type' => 'fulltext']
//             ]
//         ];

//         foreach ($indexesToDrop as $tableName => $tableIndexes) {
//             foreach ($tableIndexes as $indexData) {
//                 switch ($indexData['type']) {
//                     case 'unique':
//                         $this->dropUniqueIndexIfExists($tableName, $indexData['name']);
//                         break;
//                     case 'fulltext':
//                         $this->dropFullTextIndexIfExists($tableName, $indexData['name']);
//                         break;
//                     default:
//                         $this->dropIndexIfExists($tableName, $indexData['name']);
//                         break;
//                 }
//             }
//         }
//     }

//     /**
//      * Check if a table exists
//      */
//     private function tableExists(string $tableName): bool
//     {
//         return Schema::hasTable($tableName);
//     }

//     /**
//      * Check if an index exists on a table using Laravel's built-in method
//      */
//     private function indexExists(string $tableName, string $indexName): bool
//     {
//         return $this->tableExists($tableName) && Schema::hasIndex($tableName, $indexName);
//     }

//     /**
//      * Add regular index if table exists and index doesn't exist
//      */
//     private function addIndexIfNotExists(string $tableName, $columns, string $indexName): void
//     {
//         if (!$this->indexExists($tableName, $indexName)) {
//             Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
//                 $table->index($columns, $indexName);
//             });
//         }
//     }

//     /**
//      * Add unique index if table exists and index doesn't exist
//      */
//     private function addUniqueIndexIfNotExists(string $tableName, $columns, string $indexName): void
//     {
//         if (!$this->indexExists($tableName, $indexName)) {
//             Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
//                 $table->unique($columns, $indexName);
//             });
//         }
//     }

//     /**
//      * Add full-text index if table exists and index doesn't exist
//      */
//     private function addFullTextIndexIfNotExists(string $tableName, $columns, string $indexName): void
//     {
//         if (!$this->indexExists($tableName, $indexName)) {
//             Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
//                 $table->fullText($columns, $indexName);
//             });
//         }
//     }

//     /**
//      * Drop regular index if table and index exist
//      */
//     private function dropIndexIfExists(string $tableName, string $indexName): void
//     {
//         if ($this->indexExists($tableName, $indexName)) {
//             Schema::table($tableName, function (Blueprint $table) use ($indexName) {
//                 $table->dropIndex($indexName);
//             });
//         }
//     }

//     /**
//      * Drop unique index if table and index exist
//      */
//     private function dropUniqueIndexIfExists(string $tableName, string $indexName): void
//     {
//         if ($this->indexExists($tableName, $indexName)) {
//             Schema::table($tableName, function (Blueprint $table) use ($indexName) {
//                 $table->dropUnique($indexName);
//             });
//         }
//     }

//     /**
//      * Drop full-text index if table and index exist
//      */
//     private function dropFullTextIndexIfExists(string $tableName, string $indexName): void
//     {
//         if ($this->indexExists($tableName, $indexName)) {
//             Schema::table($tableName, function (Blueprint $table) use ($indexName) {
//                 $table->dropFullText($indexName);
//             });
//         }
//     }
// }