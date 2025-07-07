<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class SuperAdminHelper
{
    /**
     * Static cache to prevent duplicate queries
     */
    private static ?array $superAdminIds = null;
    
   
    /**
     * Get all super admin user IDs (cached)
     */
    private static function getAllSuperAdminIds(): array
    {
        if (self::$superAdminIds == null) {
            self::$superAdminIds = DB::table('super_admins')->pluck('user_id')->toArray();
        }
        
        return self::$superAdminIds;
    }

    /**
     * Clear cache after database changes
     */
    private static function clearCache(): void
    {
        self::$superAdminIds = null;
    }

    /**
     * Check if a user is a super admin
     */
    public static function isSuperAdmin(int $userId): bool
    {
        return in_array($userId, self::getAllSuperAdminIds());
    }

    /**
     * Check if current user is a super admin
     */
    public static function isCurrentUserSuperAdmin(): bool
    {
        return auth()->check() && self::isSuperAdmin(auth()->id());
    }

    /**
     * CREATE: Make a user a super admin
     */
    public static function makeSuperAdmin(int $userId): bool
    {
        if (self::isSuperAdmin($userId)) {
            return true; // Already a super admin
        }

        try {
            DB::table('super_admins')->insert([
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            self::clearCache();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * DELETE: Remove super admin status
     */
    public static function removeSuperAdmin(int $userId): bool
    {

        $deleted = DB::table('super_admins')->where('user_id', $userId)->delete();
        
        if ($deleted > 0) {
            self::clearCache();
            return true;
        }

        return false;
    }
}