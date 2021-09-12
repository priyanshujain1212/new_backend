<?php

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::find(UserRole::ADMIN);
        if (!blank($role)) {
            $role->givePermissionTo(Permission::all());
        }

        $role = Role::find(UserRole::SHOPOWNER);
        if (!blank($role)) {
            $shopOwnerPermission[]['name'] = 'dashboard';
            $shopOwnerPermission[]['name'] = 'request-products';
            $shopOwnerPermission[]['name'] = 'request-products_create';
            $shopOwnerPermission[]['name'] = 'request-products_edit';
            $shopOwnerPermission[]['name'] = 'request-products_delete';
            $shopOwnerPermission[]['name'] = 'request-products_show';
            $shopOwnerPermission[]['name'] = 'orders';
            $shopOwnerPermission[]['name'] = 'orders_create';
            $shopOwnerPermission[]['name'] = 'orders_edit';
            $shopOwnerPermission[]['name'] = 'orders_delete';
            $shopOwnerPermission[]['name'] = 'orders_show';
            $shopOwnerPermission[]['name'] = 'shop';
            $shopOwnerPermission[]['name'] = 'shop_create';
            $shopOwnerPermission[]['name'] = 'shop_edit';
            $shopOwnerPermission[]['name'] = 'shop_delete';
            $shopOwnerPermission[]['name'] = 'shop_show';
            $shopOwnerPermission[]['name'] = 'request-withdraw';
            $shopOwnerPermission[]['name'] = 'request-withdraw_create';
            $shopOwnerPermission[]['name'] = 'request-withdraw_edit';
            $shopOwnerPermission[]['name'] = 'withdraw';
            $shopOwnerPermission[]['name'] = 'transaction';
            $shopOwnerPermission[]['name'] = 'shop-owner-sales-report';
            $permissions                   = Permission::whereIn('name', $shopOwnerPermission)->get();
            $role->givePermissionTo($permissions);
        }

        $role = Role::find(UserRole::DELIVERYBOY);
        if (!blank($role)) {
            $deliveryBoyPermission[]['name'] = 'dashboard';
            $deliveryBoyPermission[]['name'] = 'orders';
            $deliveryBoyPermission[]['name'] = 'orders_edit';
            $deliveryBoyPermission[]['name'] = 'orders_show';
            $deliveryBoyPermission[]['name'] = 'order-notification';
            $deliveryBoyPermission[]['name'] = 'request-withdraw';
            $deliveryBoyPermission[]['name'] = 'request-withdraw_create';
            $deliveryBoyPermission[]['name'] = 'request-withdraw_edit';
            $deliveryBoyPermission[]['name'] = 'withdraw';
            $deliveryBoyPermission[]['name'] = 'transaction';
            $permissions                     = Permission::whereIn('name', $deliveryBoyPermission)->get();
            $role->givePermissionTo($permissions);
        }
    }
}
