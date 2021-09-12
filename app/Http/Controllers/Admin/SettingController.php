<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use App\Libraries\MyString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Setting;

class SettingController extends BackendController
{

    public function __construct()
    {
        parent::__construct();
        $this->data['siteTitle'] = 'Settings';
        $this->middleware(['permission:setting']);
    }

    // Site Setting
    public function index()
    {
        return view('admin.setting.site', $this->data);
    }

    public function siteSettingUpdate(Request $request)
    {

        $niceNames    = [];
        $settingArray = $this->validate($request, $this->siteValidateArray(), [], $niceNames);

        if ($request->hasFile('site_logo')) {
            $site_logo                 = request('site_logo');
            $settingArray['site_logo'] = $site_logo->getClientOriginalName();
            $request->site_logo->move(public_path('images'), $settingArray['site_logo']);
        } else {
            unset($settingArray['site_logo']);
        }

        if (isset($settingArray['timezone'])) {
            MyString::setEnv('APP_TIMEZONE', $settingArray['timezone']);
            Artisan::call('optimize:clear');
        }

        Setting::set($settingArray);
        Setting::save();

        return redirect(route('admin.setting.index'))->withSuccess('The Site setting updated successfully');
    }

    // SMS Setting
    public function smsSetting()
    {
        return view('admin.setting.sms', $this->data);
    }

    public function smsSettingUpdate(Request $request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->smsValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();
        return redirect(route('admin.setting.sms'))->withSuccess('The SMS setting updated successfully.');
    }

    // Payment Setting
    public function paymentSetting()
    {
        return view('admin.setting.payment', $this->data);
    }

    public function paymentSettingUpdate(Request $request)
    {
        if ($request->settingtypepayment == 'stripe') {
            $this->stripeSetting($request);
        } else if ($request->settingtypepayment == 'razorpay') {
            $this->razorpaySetting($request);
        } else if ($request->settingtypepayment == 'paytm') {
            $this->paytmSetting($request);
        } 
         else {
            return redirect(route('admin.setting.payment'));
        }
        return redirect(route('admin.setting.payment'))->withSuccess('The Payment setting updated successfully.');
    }

    private function paytmSetting($request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->paytmValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();
    }

    private function stripeSetting($request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->stripeValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();
    }

    private function razorpaySetting($request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->razorpayValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();
    }

    private function paystackSetting($request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->paystackValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();
    }

    // EMail Setting
    public function emailSetting()
    {
        return view('admin.setting.email', $this->data);
    }

    public function emailSettingUpdate(Request $request)
    {
        $niceNames         = [];
        $emailSettingArray = $this->validate($request, $this->emailValidateArray(), [], $niceNames);

        Setting::set($emailSettingArray);
        Setting::save();

        return redirect(route('admin.setting.email'))->withSuccess('The Email setting updated successfully');
    }

    // Notification Setting
    public function notificationSetting()
    {
        return view('admin.setting.notification', $this->data);
    }

    public function notificationSettingUpdate(Request $request)
    {
        $niceNames                = [];
        $notificationSettingArray = $this->validate($request, $this->notificationValidateArray(), [], $niceNames);

        Setting::set($notificationSettingArray);
        Setting::save();

        return redirect(route('admin.setting.notification'))->withSuccess('The Notification setting updated successfully.');
    }

    // Social Setting
    public function socialLoginSetting()
    {
        return view('admin.setting.social-login', $this->data);
    }

    public function socialLoginSettingUpdate(Request $request)
    {
        if ($request->settingtypesocial == 'facebook') {
            $this->facebookSetting($request);
        } else if ($request->settingtypesocial == 'google') {
            $this->googleSetting($request);
        } else {
            return redirect(route('admin.setting.social-login'));
        }
        return redirect(route('admin.setting.social-login'))->withSuccess('The Social setting updated successfully');
    }

    private function facebookSetting($request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->facebookValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();

    }

    private function googleSetting($request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->googleValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();

    }

    // otp Setting
    public function otpSetting()
    {
        return view('admin.setting.otp', $this->data);
    }

    public function otpSettingUpdate(Request $request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->otpValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();
        return redirect(route('admin.setting.otp'))->withSuccess('The OTP setting updated successfully');
    }

    // Homepage Setting
    public function homepageSetting()
    {
        return view('admin.setting.homepage', $this->data);
    }

    public function homepageSettingUpdate(Request $request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->homepageValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();

        return redirect(route('admin.setting.homepage'))->withSuccess('The Home page setting updated successfully');
    }

    // SMS Setting
    public function socialSetting()
    {
        return view('admin.setting.social', $this->data);
    }

    public function socialSettingUpdate(Request $request)
    {
        $niceNames    = [];
        $settingArray = $this->validate($request, $this->socialValidateArray(), [], $niceNames);

        Setting::set($settingArray);
        Setting::save();
        return redirect(route('admin.setting.social'))->withSuccess('The Social setting updated successfully.');
    }

    // Site Setting validation
    private function siteValidateArray()
    {
        return [
            'site_name'                       => 'required|string|max:100',
            'site_email'                      => 'required|string|max:100',
            'site_phone_number'               => 'required', 'max:60',
            'currency_name'                   => 'required|string|max:20',
            'currency_code'                   => 'required|string|max:20',
            'site_footer'                     => 'required|string|max:200',
            'timezone'                        => 'required|string',
            'site_logo'                       => 'nullable|mimes:jpeg,jpg,png,gif|max:3096',
            'site_description'                => 'required|string|max:500',
            'geolocation_distance_radius'     => 'required|numeric',
            'order_commission_percentage'     => 'required|numeric',
            'delivery_boy_order_amount_limit' => 'nullable|numeric',
            'order_attachment_checking'       => 'nullable|numeric',
            'ios_app_link'                    => 'nullable|string',
            'android_app_link'                => 'nullable|string',
        ];
    }

    // SMS Setting validation
    private function smsValidateArray()
    {
        return [
            'twilio_auth_token'  => 'required|string|max:200',
            'twilio_account_sid' => 'required|string|max:200',
            'twilio_from'        => 'required|string|max:20',
            'twilio_disabled'    => 'numeric',
        ];
    }

     // Payment Setting validation
     public function paytmValidateArray()
     {
         return [
             'paytm_merchant_ID'  => 'required|string|max:255',
             'paytm_merchant_key' => 'required|string|max:255',
             'settingtypepayment' => 'required|string',
         ];
     }

    // Payment Setting validation
    public function stripeValidateArray()
    {
        return [
            'stripe_key'         => 'required|string|max:255',
            'stripe_secret'      => 'required|string|max:255',
            'settingtypepayment' => 'required|string',
        ];
    }

    public function razorpayValidateArray()
    {
        return [
            'razorpay_key'       => 'required|string|max:255',
            'razorpay_secret'    => 'required|string|max:255',
            'settingtypepayment' => 'required|string',
        ];
    }

    private function paystackValidateArray()
    {
        return [
            'paystack_key'       => 'required|string|max:255',
            'settingtypepayment' => 'required|string',
        ];
    }

    // EMAIL Setting validation
    private function emailValidateArray()
    {
        return [
            'mail_host'         => 'required|string|max:100',
            'mail_port'         => 'required|string|max:100',
            'mail_username'     => 'required|string|max:100',
            'mail_password'     => 'required|string|max:100',
            'mail_from_name'    => 'required|string|max:100',
            'mail_from_address' => 'required|string|max:200',
            'mail_disabled'     => 'numeric',
        ];
    }

    // Notification Setting validation
    private function notificationValidateArray()
    {
        return [
            'fcm_secret_key' => 'required|string|max:255',
        ];
    }

    // Social Setting validation
    private function facebookValidateArray()
    {
        return [
            'facebook_key'      => 'required|string|max:255',
            'facebook_secret'   => 'required|string|max:255',
            'facebook_url'      => 'required|string|max:255',
            'settingtypesocial' => 'required|string',
        ];
    }

    private function googleValidateArray()
    {
        return [
            'google_key'        => 'required|string|max:255',
            'google_secret'     => 'required|string|max:255',
            'google_url'        => 'required|string|max:255',
            'settingtypesocial' => 'required|string',
        ];
    }

    // OTP Setting validation
    private function otpValidateArray()
    {
        return [
            'otp_type_checking' => 'required|string',
            'otp_digit_limit'   => 'required|numeric',
            'otp_expire_time'   => 'required|numeric|min:1|max:30',
        ];
    }

    // Homepage Setting validation
    private function homepageValidateArray()
    {
        return [
            'step_one_icon'          => 'required|string|max:100',
            'step_one_title'         => 'required|string|max:255',
            'step_one_description'   => 'required|string|max:255',
            'step_two_icon'          => 'required|string|max:100',
            'step_two_title'         => 'required|string|max:255',
            'step_two_description'   => 'required|string|max:255',
            'step_three_icon'        => 'required|string|max:100',
            'step_three_title'       => 'required|string|max:255',
            'step_three_description' => 'required|string|max:255',
        ];
    }

    // Social Setting validation
    private function socialValidateArray()
    {
        return [
            'facebook'  => 'nullable|string|max:100',
            'instagram' => 'nullable|string|max:100',
            'youtube'   => 'nullable|string|max:100',
            'twitter'   => 'nullable|string|max:100',
        ];
    }

}
