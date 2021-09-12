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
        $this->call(SettingTableSeeder::class);
//        $this->call(LocationTableSeeder::class);
//        $this->call(AreaTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(RolePermissionTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(DemoUserTableSeeder::class);
        $this->call(AdminPermissionTableSeeder::class);
        $this->call(UpdateTableSeeder::class);
        $this->call(BackendMenuTableSeeder::class);
//        $this->call(CategoryTableSeeder::class);
//        $this->call(ProductTableSeeder::class);
//        $this->call(ShopTableSeeder::class);
//        $this->call(ShopProductTableSeeder::class);
        $this->call(FooterMenuSectionTableSeeder::class);
        $this->call(TemplateTableSeeder::class);
        $this->call(PageTableSeeder::class);
//        $this->call(OrderTableSeeder::class);
//        $this->call(OrderLineItemTableSeeder::class);
//        $this->call(OrderHistoryTableSeeder::class);

	}
}
