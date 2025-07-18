<?php
namespace Database\Seeders;

use App\Coupon;
use Illuminate\Database\Seeder;

class CouponsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $coupons = [
            [
                'code' => 'SAVE10',
                'type' => 'percent',
                'percent_off' => 10,
                'value' => null,
            ],
            [
                'code' => 'SAVE20',
                'type' => 'percent',
                'percent_off' => 20,
                'value' => null,
            ],
            [
                'code' => 'WELCOME50',
                'type' => 'fixed',
                'value' => 5000,
                'percent_off' => null,
            ],
            [
                'code' => 'NEWUSER',
                'type' => 'fixed',
                'value' => 2500,
                'percent_off' => null,
            ],
            [
                'code' => 'BLACKFRIDAY',
                'type' => 'percent',
                'percent_off' => 30,
                'value' => null,
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 1500,
                'percent_off' => null,
            ],
            [
                'code' => 'STUDENT15',
                'type' => 'percent',
                'percent_off' => 15,
                'value' => null,
            ],
            [
                'code' => 'LOYALTY25',
                'type' => 'percent',
                'percent_off' => 25,
                'value' => null,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::firstOrCreate(
                ['code' => $couponData['code']],
                $couponData
            );
        }
    }
}
