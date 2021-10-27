<?php

use App\Donation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeed::class);
        $this->call(DonationSeed::class);
        $this->call(TransactionSeed::class);
        $this->call(InstitutionSeed::class);
        $this->call(BuySeed::class);
        $this->call(GraficSeed::class);
    }
}
