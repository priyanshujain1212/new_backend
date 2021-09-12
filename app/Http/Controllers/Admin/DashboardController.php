<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ShopStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\BackendController;
use App\Models\Order;
use App\Models\Shop;
use App\User;
use Illuminate\Http\Request;

class DashboardController extends BackendController
{
    public function __construct()
    {
        parent::__construct();
        $this->data['siteTitle'] = 'Dashboard';
        $this->middleware([ 'permission:dashboard' ])->only('index');
    }

    public function index()
    {
        $this->data['months'] = [
            1 => 'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
        $orders       = Order::orderBy('id', 'desc')->orderOwner();
        $totalOrders  = $orders->get();
        $recentOrders = Order::orderBy('id', 'desc')->whereDate('created_at', date('Y-m-d'))->orderOwner()->get();
        $yearlyOrders = Order::orderBy('id', 'desc')->where('status', '!=', OrderStatus::CANCEL)->whereYear('created_at', date('Y'))->orderOwner()->get();
        $totalIncome = 0;
        if ( !blank($totalOrders) ) {
            foreach ( $totalOrders as $totalOrder ) {
                if ( OrderStatus::COMPLETED == $totalOrder->status ) {
                    $totalIncome += $totalOrder->paid_amount;
                }
            }
        }
        $monthWiseTotalIncome    = [];
        $monthDayWiseTotalIncome = [];
        $monthWiseTotalOrder     = [];
        $monthDayWiseTotalOrder  = [];
        if ( !blank($yearlyOrders) ) {
            foreach ( $yearlyOrders as $yearlyOrder ) {
                $monthNumber = (int)date('m', strtotime($yearlyOrder->created_at));
                $dayNumber   = (int)date('d', strtotime($yearlyOrder->created_at));
                if ( !isset($monthDayWiseTotalIncome[ $monthNumber ][ $dayNumber ]) ) {
                    $monthDayWiseTotalIncome[ $monthNumber ][ $dayNumber ] = 0;
                }
                $monthDayWiseTotalIncome[ $monthNumber ][ $dayNumber ] += $yearlyOrder->paid_amount;
                if ( !isset($monthWiseTotalIncome[ $monthNumber ]) ) {
                    $monthWiseTotalIncome[ $monthNumber ] = 0;
                }
                $monthWiseTotalIncome[ $monthNumber ] += $yearlyOrder->paid_amount;
                if ( !isset($monthDayWiseTotalOrder[ $monthNumber ][ $dayNumber ]) ) {
                    $monthDayWiseTotalOrder[ $monthNumber ][ $dayNumber ] = 0;
                }
                $monthDayWiseTotalOrder[ $monthNumber ][ $dayNumber ] += 1;
                if ( !isset($monthWiseTotalOrder[ $monthNumber ]) ) {
                    $monthWiseTotalOrder[ $monthNumber ] = 0;
                }
                $monthWiseTotalOrder[ $monthNumber ] += 1;
            }
        }
        $this->data['monthWiseTotalIncome']    = $monthWiseTotalIncome;
        $this->data['monthDayWiseTotalIncome'] = $monthDayWiseTotalIncome;
        $this->data['monthWiseTotalOrder']     = $monthWiseTotalOrder;
        $this->data['monthDayWiseTotalOrder']  = $monthDayWiseTotalOrder;
        $this->data['totalOrders'] = $totalOrders;
        $this->data['totalIncome'] = $totalIncome;
        if ( auth()->user()->myrole == UserRole::ADMIN ) {
            $this->data['totalUsers'] = User::where([ 'status' => UserStatus::ACTIVE ])->get();
            $this->data['totalShops'] = Shop::where([ 'status' => ShopStatus::ACTIVE ])->get();
        } elseif ( auth()->user()->myrole == UserRole::SHOPOWNER || auth()->user()->myrole == UserRole::DELIVERYBOY ) {
            if ( auth()->user()->myrole == UserRole::SHOPOWNER ) {
                $this->data['totalPendingOrders'] = $orders->pending()->get();
            }
            $this->data['totalProcessOrders']  = $orders->process()->get();
            $this->data['totalCompleteOrders'] = $orders->complete()->get();
        }
        $this->data['recentOrders'] = $recentOrders;
        return view('admin.dashboard.index', $this->data);
    }

    public function dayWiseIncomeOrder( Request $request )
    {
        $type          = $request->type;
        $monthID       = $request->monthID;
        $dayWiseData   = $request->dayWiseData;
        $showChartData = [];
        if ( $type && $monthID ) {
            $days        = date('t', mktime(0, 0, 0, $monthID, 1, date('Y')));
            $dayWiseData = json_decode($dayWiseData, true);
            for ( $i = 1; $i <= $days; $i++ ) {
                $showChartData[ $i ] = isset($dayWiseData[ $i ]) ? $dayWiseData[ $i ] : 0;
            }
        } else {
            for ( $i = 1; $i <= 31; $i++ ) {
                $showChartData[ $i ] = 0;
            }
        }
        echo json_encode($showChartData);
    }

}
