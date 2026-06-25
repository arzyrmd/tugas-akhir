<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomProductRequestFactory extends Factory
{
    public function definition(): array
    {
        $statuses = [
            'MENUNGGU_REVIEW',
            'PENAWARAN_DIBERIKAN',
            'DALAM_PENGERJAAN',
            'MENUNGGU_PELUNASAN',
            'SIAP_DIKIRIM',
            'SELESAI'
        ];

        $status = $this->faker->randomElement($statuses);
        $quotedPrice = $this->faker->numberBetween(500000, 5000000);
        $downPayment = $quotedPrice * 0.3;

        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'specifications' => $this->faker->paragraph(),
            'budget' => $this->faker->numberBetween(300000, 3000000),
            'desired_deadline' => $this->faker->dateTimeBetween('+2 weeks', '+2 months'),
            'status' => $status,
            'quoted_price' => $quotedPrice,
            'down_payment' => $downPayment,
            'remaining_payment' => $quotedPrice - $downPayment,
            'estimated_completion' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'admin_notes' => $status !== 'MENUNGGU_REVIEW' ? $this->faker->paragraph() : null,
            'dp_payment_date' => in_array($status, ['DALAM_PENGERJAAN', 'MENUNGGU_PELUNASAN', 'SIAP_DIKIRIM', 'SELESAI'])
                ? $this->faker->dateTimeBetween('-1 month', '-1 week')
                : null,
            'full_payment_date' => in_array($status, ['SIAP_DIKIRIM', 'SELESAI'])
                ? $this->faker->dateTimeBetween('-2 weeks', '-1 day')
                : null,
            'work_started_at' => in_array($status, ['DALAM_PENGERJAAN', 'MENUNGGU_PELUNASAN', 'SIAP_DIKIRIM', 'SELESAI'])
                ? $this->faker->dateTimeBetween('-1 month', '-2 weeks')
                : null,
            'work_completed_at' => in_array($status, ['MENUNGGU_PELUNASAN', 'SIAP_DIKIRIM', 'SELESAI'])
                ? $this->faker->dateTimeBetween('-2 weeks', '-3 days')
                : null,
            'shipping_date' => $status === 'SELESAI'
                ? $this->faker->dateTimeBetween('-1 week', '-1 day')
                : null,
            'delivery_date' => $status === 'SELESAI'
                ? $this->faker->dateTimeBetween('-6 days', 'now')
                : null,
        ];
    }
}
