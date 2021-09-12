<?php

use App\Models\BackendMenu;
use Illuminate\Database\Seeder;

class BackendMenuTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $parent = [
            'account' 	=> 11,
            'hrm' 		=> 16,
            'report'    => 21,
            'frontend' 	=> 26,
        ];

        $menus = [
            [
                'name'      => 'Dashboard',
                'link'      => 'dashboard',
                'icon'      => 'fas fa-laptop',
                'parent_id' => 0,
                'priority'  => 500,
                'status'    => 1,
            ],
            [
                'name'      => 'Locations',
                'link'      => 'location',
                'icon'      => 'fas fa-life-ring',
                'parent_id' => 0,
                'priority'  => 490,
                'status'    => 1,
            ],
            [
                'name'      => 'Areas',
                'link'      => 'area',
                'icon'      => 'fas fa-map',
                'parent_id' => 0,
                'priority'  => 480,
                'status'    => 1,
            ],
            [
                'name'      => 'Categories',
                'link'      => 'category',
                'icon'      => 'fas fa-list-ul',
                'parent_id' => 0,
                'priority'  => 470,
                'status'    => 1,
            ],
            [
                'name'      => 'Products',
                'link'      => 'products',
                'icon'      => 'fas fa-gift',
                'parent_id' => 0,
                'priority'  => 460,
                'status'    => 1,
            ],
            [
                'name'      => 'Request Products',
                'link'      => 'request-products',
                'icon'      => 'fas fa-glass-whiskey',
                'parent_id' => 0,
                'priority'  => 455,
                'status'    => 1,
            ],
            [
                'name'      => 'Shops',
                'link'      => 'shop',
                'icon'      => 'fas fa-university',
                'parent_id' => 0,
                'priority'  => 450,
                'status'    => 1,
            ],
            [
                'name'      => 'Orders',
                'link'      => 'orders',
                'icon'      => 'fas fa-cart-plus',
                'parent_id' => 0,
                'priority'  => 440,
                'status'    => 1,
            ],
            [
                'name'      => 'Rating',
                'link'      => 'rating',
                'icon'      => 'fas fa-star',
                'parent_id' => 0,
                'priority'  => 430,
                'status'    => 1,
            ],
            [
                'name'      => 'Order Notifications',
                'link'      => 'order-notification',
                'icon'      => 'fas fa-cubes',
                'parent_id' => 0,
                'priority'  => 420,
                'status'    => 1,
            ],
            [
                'name'      => 'Accounts',
                'link'      => '#',
                'icon'      => 'fas fa-th-large',
                'parent_id' => 0,
                'priority'  => 410,
                'status'    => 1,
            ],
            [
                'name'      => 'Transactions',
                'link'      => 'transaction',
                'icon'      => 'fas fa-calculator',
                'parent_id' => $parent['account'],
                'priority'  => 500,
                'status'    => 1,
            ],
            [
                'name'      => 'Collections',
                'link'      => 'collection',
                'icon'      => 'fas fa-credit-card',
                'parent_id' => $parent['account'],
                'priority'  => 490,
                'status'    => 1,
            ],
            [
                'name'      => 'Request Withdraw',
                'link'      => 'request-withdraw',
                'icon'      => 'fas fa-newspaper',
                'parent_id' => $parent['account'],
                'priority'  => 480,
                'status'    => 1,
            ],
            [
                'name'      => 'Withdraw',
                'link'      => 'withdraw',
                'icon'      => 'fas fa-money-bill-alt',
                'parent_id' => $parent['account'],
                'priority'  => 470,
                'status'    => 1,
            ],


            [
                'name'      => 'HRM',
                'link'      => '#',
                'icon'      => 'fas fa-id-card ',
                'parent_id' => 0,
                'priority'  => 400,
                'status'    => 1,
            ],
            [
                'name'      => 'Administrators',
                'link'      => 'administrators',
                'icon'      => 'fas fa-users',
                'parent_id' => $parent['hrm'],
                'priority'  => 500,
                'status'    => 1,
            ],
            [
                'name'      => 'Customers',
                'link'      => 'customers',
                'icon'      => 'fas fa-user-secret',
                'parent_id' => $parent['hrm'],
                'priority'  => 490,
                'status'    => 1,
            ],
            [
                'name'      => 'Delivery Boys',
                'link'      => 'delivery-boys',
                'icon'      => 'fas fa-user',
                'parent_id' => $parent['hrm'],
                'priority'  => 480,
                'status'    => 1,
            ],
            [
                'name'      => 'Role',
                'link'      => 'role',
                'icon'      => 'fas fa-star',
                'parent_id' => $parent['hrm'],
                'priority'  => 470,
                'status'    => 1,
            ],


            [
                'name'      => 'Report',
                'link'      => '#',
                'icon'      => 'fas fa-archive',
                'parent_id' => 0,
                'priority'  => 390,
                'status'    => 1,
            ],
            [
                'name'      => 'Shop Owner Sales',
                'link'      => 'shop-owner-sales-report',
                'icon'      => 'fas fa-list-alt',
                'parent_id' => $parent['report'],
                'priority'  => 500,
                'status'    => 1,
            ],
            [
                'name'      => 'Admin Commission',
                'link'      => 'admin-commission-report',
                'icon'      => 'fas fa-fax',
                'parent_id' => $parent['report'],
                'priority'  => 490,
                'status'    => 1,
            ],
            [
                'name'      => 'Credit Balance',
                'link'      => 'credit-balance-report',
                'icon'      => 'fas fa-balance-scale',
                'parent_id' => $parent['report'],
                'priority'  => 390,
                'status'    => 1,
            ],
            [
                'name'      => 'Delivery Order Balance',
                'link'      => 'cash-on-delivery-order-balance-report',
                'icon'      => 'fas fa-save',
                'parent_id' => $parent['report'],
                'priority'  => 290,
                'status'    => 1,
            ],


            [
                'name'      => 'Frontend',
                'link'      => '#',
                'icon'      => 'fas fa-braille',
                'parent_id' => 0,
                'priority'  => 380,
                'status'    => 1,
            ],
            [
                'name'      => 'Banners',
                'link'      => 'banner',
                'icon'      => 'fas fa-film',
                'parent_id' => $parent['frontend'],
                'priority'  => 500,
                'status'    => 1,
            ],
            [
                'name'      => 'Pages',
                'link'      => 'page',
                'icon'      => 'fas fa-sticky-note',
                'parent_id' => $parent['frontend'],
                'priority'  => 490,
                'status'    => 1,
            ],


            [
                'name'      => 'Updates',
                'link'      => 'updates',
                'icon'      => 'fas fa-cloud-download-alt',
                'parent_id' => 0,
                'priority'  => 370,
                'status'    => 1,
            ],
            [
                'name'      => 'Settings',
                'link'      => 'setting',
                'icon'      => 'fas fa-cogs',
                'parent_id' => 0,
                'priority'  => 360,
                'status'    => 1,
            ],
        ];

        BackendMenu::insert($menus);
    }
}
