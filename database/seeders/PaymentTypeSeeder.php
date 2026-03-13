<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $paymentTypes = [
            ['name' => 'Registration', 'amount' => 0.00],
            ['name' => 'Bereavement', 'amount' => 0.00],
            ['name' => 'Wedding', 'amount' => 0.00],
            ['name' => 'Freewill Donation', 'amount' => 0.00],
        ];

        foreach ($paymentTypes as $paymentType) {
            PaymentType::firstOrCreate(
                ['name' => $paymentType['name']],
                ['amount' => $paymentType['amount']]
            );
        }
    }
}
