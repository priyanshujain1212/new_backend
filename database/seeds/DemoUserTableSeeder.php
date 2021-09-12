<?php

use App\Models\DeliveryBoyAccount;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::find(2);
        $user = User::create([
            'first_name'     => 'Customer',
            'last_name'      => '1',
            'username'       => 'customer1',
            'email'          => 'customer@example.com',
            'phone'          => '+15005550006',
            'address'        => 'Dhaka, Bangladesh',
            'password'       => bcrypt('123456'),
            'remember_token' => Str::random(10),
        ]);
        if (!blank($user) && !blank($role)) {
            $user->assignRole($role->name);
        }

        $user = User::create([
            'first_name'     => 'Customer',
            'last_name'      => '2',
            'username'       => 'customer2',
            'email'          => 'customer2@example.com',
            'phone'          => '+15005550006',
            'address'        => 'Dhaka, Bangladesh',
            'password'       => bcrypt('123456'),
            'remember_token' => Str::random(10),
        ]);
        if (!blank($user) && !blank($role)) {
            $user->assignRole($role->name);
        }


        $role = Role::find(3);
        $user = User::create([
            'first_name'     => 'Shop',
            'last_name'      => '1',
            'username'       => 'shopowner1',
            'email'          => 'shopowner@example.com',
            'phone'          => '+15005550006',
            'address'        => 'Dhaka, Bangladesh',
            'password'       => bcrypt('123456'),
            'remember_token' => Str::random(10),
        ]);
        if (!blank($user) && !blank($role)) {
            $user->assignRole($role->name);
        }

        // $user = User::create([
        //     'first_name'     => 'Shop',
        //     'last_name'      => '2',
        //     'username'       => 'shopowner2',
        //     'email'          => 'shopowner2@example.com',
        //     'phone'          => '+15005550006',
        //     'address'        => 'Dhaka, Bangladesh',
        //     'password'       => bcrypt('123456'),
        //     'remember_token' => Str::random(10),
        // ]);
        // if (!blank($user) && !blank($role)) {
        //     $user->assignRole($role->name);
        // }


        $role = Role::find(4);
        $user = User::create([
            'first_name'     => 'Delivery',
            'last_name'      => 'Boy',
            'username'       => 'deliveryboy',
            'email'          => 'deliveryboy@example.com',
            'phone'          => '+15005550006',
            'address'        => 'Dhaka, Bangladesh',
            'password'       => bcrypt('123456'),
            'remember_token' => Str::random(10),
        ]);

        if (!blank($user) && !blank($role)) {
            $user->assignRole($role->name);
        }

        $deliveryBoyAccount                  = new DeliveryBoyAccount();
        $deliveryBoyAccount->user_id         = $user->id;
        $deliveryBoyAccount->delivery_charge = 0;
        $deliveryBoyAccount->balance         = 0;
        $deliveryBoyAccount->save();
    }
}
