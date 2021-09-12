<?php

use Illuminate\Database\Seeder;
use Setting as SeederSetting;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settingArray['site_name']                       = 'Mr. Echo';
        $settingArray['site_email']                      = 'mr.echo.in@gmail.com';
        $settingArray['site_phone_number']               = '';
        $settingArray['site_logo']                       = 'logo.png';
        $settingArray['site_footer']                     = '@ All Rights Reserved';
        $settingArray['site_description']                = 'Mr.Echo is the best online food order management system.';
        $settingArray['currency_name']                   = 'USD';
        $settingArray['currency_code']                   = '$';
        $settingArray['geolocation_distance_radius']     = 20;
        $settingArray['order_commission_percentage']     = 5;
        $settingArray['timezone']                        = '';
        $settingArray['twilio_auth_token']               = '';
        $settingArray['twilio_account_sid']              = '';
        $settingArray['twilio_from']                     = '';
        $settingArray['twilio_disabled']                 = 1;
        $settingArray['stripe_key']                      = 'pk_test_Kqmq6XXBwdoYJFLV1CSDnaxz';
        $settingArray['stripe_secret']                   = 'sk_test_JLeo9KvVZvhgsMzQ7KCl43in';
        $settingArray['razorpay_key']                    = '';
        $settingArray['razorpay_secret']                 = '';
        $settingArray['paytm_merchant_id']               = '';
        $settingArray['paytm_merchant_key']              = '';
        $settingArray['paytm_callback_url']              = '';
        $settingArray['paystack_key']                    = '';
        $settingArray['mail_host']                       = '';
        $settingArray['mail_port']                       = '';
        $settingArray['mail_username']                   = '';
        $settingArray['mail_password']                   = '';
        $settingArray['order_attachment_checking']       = '';
        $settingArray['delivery_boy_order_amount_limit'] = 10000;
        $settingArray['mail_from_name']                  = '';
        $settingArray['mail_from_address']               = '';
        $settingArray['mail_disabled']                   = 1;
        $settingArray['fcm_secret_key']                  = '';
        $settingArray['facebook_key']                    = '';
        $settingArray['facebook_secret']                 = '';
        $settingArray['facebook_url']                    = '';
        $settingArray['google_key']                      = '';
        $settingArray['google_secret']                   = '';
        $settingArray['google_url']                      = '';
        $settingArray['otp_type_checking']               = 'email';
        $settingArray['otp_digit_limit']                 = 6;
        $settingArray['otp_expire_time']                 = 10;
        $settingArray['purchase_code']                   = session()->has('purchase_code') ? session()->get('purchase_code') : "";
        $settingArray['purchase_username']               = session()->has('purchase_username') ? session()->get('purchase_username') : "";
        $settingArray['facebook']                        = '';
        $settingArray['instagram']                       = '';
        $settingArray['youtube']                         = '';
        $settingArray['twitter']                         = '';

        SeederSetting::set($settingArray);
        SeederSetting::save();
    }
}
