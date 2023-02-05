<?php

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
        $this->call(AdminUserTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ProductImageTableSeeder::class);
        $this->call(ProductListTableSeeder::class);
        $this->call(ProductOrderListTableSeeder::class);
        $this->call(ProductOrderDetailTableSeeder::class);
        $this->call(PaywaysTableSeeder::class);
        $this->call(BanksTableSeeder::class);
        $this->call(UserUsualAddressTableSeeder::class);
        $this->call(FundingType::class);
        $this->call(EcpayDomainTableSeeder::class);
        $this->call(EcpaySettingTableSeeder::class);
        $this->call(CreditDepositAccountTableSeeder::class);
        $this->call(UsersDepositPayment::class);
        $this->call(PaymentSettingTableSeeder::class);
    }
}
