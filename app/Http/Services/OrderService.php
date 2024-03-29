<?php

namespace App\Http\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProductReceiveStatus;
use App\Libraries\MyString;
use App\Models\Balance;
use App\Models\BestSellingCategory;
use App\Models\DeliveryBoyAccount;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\OrderLineItem;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\User;

class OrderService
{
    public $adminBalanceId = 1;
    public $commission     = 0;

    public function __construct()
    {
        $this->commission = setting('order_commission_percentage');
    }

    public function accept(int $orderId) // done
    {
        $order = Order::where([
            'status' => OrderStatus::PENDING,
            'id'     => $orderId,
        ])->first();
        if (!blank($order)) {
            if ($order->payment_status == PaymentStatus::UNPAID) {
                $balance = Balance::find($order->user->balance_id);
                if (!blank($balance) && $balance->balance >= $order->total) {
                    $payment = app(TransactionService::class)->payment($order->user->balance_id, $this->adminBalanceId, $order->total, $orderId);
                    if ($payment->status) {
                        $order->payment_status = PaymentStatus::PAID;
                        $order->payment_method = PaymentMethod::WALLET;
                        $order->paid_amount    = $order->total;
                    }
                }
            }
            $order->status = OrderStatus::ACCEPT;
            $order->save();
            $orderHistory = $this->status($orderId, OrderStatus::ACCEPT);
            if ($orderHistory->status) {
                ResponseService::set(['order_history_id' => $orderHistory->order_history_id]);
            }
            ResponseService::set([
                'status'   => true,
                'order_id' => $orderId,
            ]);
        } else {
            ResponseService::set([
                'status'  => false,
                'message' => 'Order not found',
            ]);
        }
        return ResponseService::response();
    }

    public function reject( int $orderId ) // done
    {
        $order = Order::where([
            'status' => OrderStatus::PENDING,
            'id'     => $orderId,
        ])->first();
        if ( !blank($order) ) {
            if ( $order->payment_status == PaymentStatus::PAID && $order->payment_method != PaymentMethod::CASH_ON_DELIVERY ) {
                $refund = app(TransactionService::class)->refund($this->adminBalanceId, $order->user->balance_id, $order->total, $orderId);
                if ( $refund->status ) {
                    $order->payment_status = PaymentStatus::UNPAID;
                    $order->payment_method = PaymentMethod::CASH_ON_DELIVERY;
                    $order->paid_amount    = 0;
                }
            }
            $order->status = OrderStatus::REJECT;
            $order->save();
            $orderHistory = $this->status($orderId, OrderStatus::REJECT);
            if ( $orderHistory->status ) {
                ResponseService::set([
                    'order_history_id' => $orderHistory->order_history_id,
                ]);
            }
            ResponseService::set([
                'status'   => true,
                'order_id' => $orderId,
            ]);
        } else {
            ResponseService::set([
                'status'  => false,
                'message' => 'Order not found',
            ]);
        }
        return ResponseService::response();
    }

    public function cancel(int $orderId) // done
    {
        $order = Order::where([
            'status' => OrderStatus::PENDING,
            'id'     => $orderId,
        ])->first();
        if (!blank($order)) {
            if ($order->payment_status == PaymentStatus::PAID && $order->payment_method != PaymentMethod::CASH_ON_DELIVERY) {
                $refund = app(TransactionService::class)->refund($this->adminBalanceId, $order->user->balance_id, $order->total, $orderId);
                if ($refund->status) {
                    $order->payment_status = PaymentStatus::UNPAID;
                    $order->payment_method = PaymentMethod::CASH_ON_DELIVERY;
                    $order->paid_amount    = 0;
                }
            }
            $order->status = OrderStatus::CANCEL;
            $order->save();
            $orderHistory = $this->status($orderId, OrderStatus::CANCEL);
            if ($orderHistory->status) {
                ResponseService::set([
                    'order_history_id' => $orderHistory->order_history_id,
                ]);
            }
            ResponseService::set([
                'status'   => true,
                'order_id' => $orderId,
            ]);
        } else {
            ResponseService::set([
                'status'  => false,
                'message' => 'Order not found',
            ]);
        }
        return ResponseService::response();
    }

    public function process(int $orderId) // done

    {
        $order = Order::where([
            'status' => OrderStatus::ACCEPT,
            'id'     => $orderId,
        ])->first();
        if (!blank($order)) {
            $order->status = OrderStatus::PROCESS;
            $order->save();
            $orderHistory = $this->status($orderId, OrderStatus::PROCESS);
            if ($orderHistory->status) {
                ResponseService::set(['order_history_id' => $orderHistory->order_history_id]);
            }
            ResponseService::set([
                'status'   => true,
                'order_id' => $orderId,
            ]);
        } else {
            ResponseService::set([
                'status'  => false,
                'message' => 'Order not found',
            ]);
        }
        return ResponseService::response();
    }

    public function completed(int $orderId) //done
    {
        $order = Order::orderOwner()->findOrFail($orderId);
        if (!blank($order)) {
            if ($order->payment_method == PaymentMethod::CASH_ON_DELIVERY && $order->payment_status == PaymentStatus::UNPAID) {
                $addFund = app(TransactionService::class)->addFund(0, $order->user->balance_id, PaymentMethod::CASH_ON_DELIVERY, $order->total, $orderId);
                if ($addFund->status) {
                    $payment = app(TransactionService::class)->payment($order->user->balance_id, $this->adminBalanceId, $order->total, $orderId);
                    if ($payment->status) {
                        $order->paid_amount    = $order->total;
                        $order->payment_status = PaymentStatus::PAID;
                    } else {
                        ResponseService::set([
                            'status'   => false,
                            'message'  => 'Payment does not complete',
                            'order_id' => $orderId,
                        ]);
                    }
                }

                $deliveryBoyAccount = DeliveryBoyAccount::where('user_id', $order->delivery_boy_id)->first();
                if (!blank($deliveryBoyAccount)) {
                    $deliveryBoyAccount->delivery_charge = $deliveryBoyAccount->delivery_charge + $order->delivery_charge;
                    $deliveryBoyAccount->balance         = $deliveryBoyAccount->balance + $order->total;
                    $deliveryBoyAccount->save();
                } else {
                    ResponseService::set([
                        'status'   => false,
                        'message'  => 'Delivery boy account does not found',
                        'order_id' => $orderId,
                    ]);
                }
            } else {
                $deliveryBoy = User::find($order->delivery_boy_id);
                if (!blank($deliveryBoy)) {
                    $deliveryBoyBalanceId = $deliveryBoy->balance_id;
                    $amount               = $order->delivery_charge;
                    $transfer             = app(TransactionService::class)->transfer($this->adminBalanceId, $deliveryBoyBalanceId, $amount, $orderId);
                    if ($transfer->status) {
                        ResponseService::set([
                            'status'   => true,
                            'order_id' => $orderId,
                            'amount'   => $amount,
                        ]);
                    } else {
                        ResponseService::set([
                            'status'   => false,
                            'message'  => 'Delivery boy payment fail',
                            'order_id' => $orderId,
                            'amount'   => $amount,
                        ]);
                    }
                } else {
                    ResponseService::set([
                        'status'   => false,
                        'message'  => 'Delivery boy not found',
                        'order_id' => $orderId,
                    ]);
                }
            }

            $order->status = OrderStatus::COMPLETED;
            $order->save();

            $bestSelling = $this->bestSelling($orderId);
            if ($bestSelling->status) {
                ResponseService::set([
                    'status'   => true,
                    'order_id' => $orderId,
                ]);
            } else {
                ResponseService::set([
                    'status'   => false,
                    'message'  => $bestSelling->message,
                    'order_id' => $orderId,
                ]);
            }
            $orderHistory = $this->status($orderId, OrderStatus::COMPLETED);
            if ($orderHistory->status) {
                ResponseService::set([
                    'order_history_id' => $orderHistory->order_history_id,
                ]);
            }
        }
        return ResponseService::response();
    }

    public function productReceive(int $orderId, int $productReceiveStatus) // done

    {
        $order = Order::where([
            'status' => OrderStatus::PROCESS,
            'id'     => $orderId,
        ])->first();
        if (!blank($order)) {
            if ($productReceiveStatus == ProductReceiveStatus::RECEIVE) {
                $shop          = Shop::find($order->shop_id);
                $shopBalanceId = !blank($shop) ? $shop->user->balance_id : 0;
                $amount        = ($order->sub_total - ($order->sub_total / 100) * $this->commission);
                $transfer      = app(TransactionService::class)->transfer($this->adminBalanceId, $shopBalanceId, $amount, $orderId);

                if ($transfer->status) {
                    $order->status = OrderStatus::ON_THE_WAY;
                    $orderHistory  = $this->status($orderId, OrderStatus::ON_THE_WAY);
                    if ($orderHistory->status) {
                        ResponseService::set(['order_history_id' => $orderHistory->order_history_id]);
                    }
                }
            }
            $order->product_received = $productReceiveStatus;

            $order->save();
            ResponseService::set([
                'status'   => true,
                'order_id' => $orderId,
            ]);
        } else {
            ResponseService::set([
                'status'  => false,
                'message' => 'Order not found',
            ]);
        }
        return ResponseService::response();
    }

    public function status(int $orderId, int $status) // done

    {
        $orderHistory = OrderHistory::where(['order_id' => $orderId])->latest()->first();
        if (!blank($orderHistory)) {
            if ($orderHistory->current_status != $status) {
                $orderHistory = OrderHistory::create([
                    'order_id'        => $orderId,
                    'previous_status' => $orderHistory->current_status,
                    'current_status'  => $status,
                ]);
            }
        } else {
            $orderHistory = OrderHistory::create([
                'order_id'        => $orderId,
                'previous_status' => null,
                'current_status'  => OrderStatus::PENDING,
            ]);
            $orderHistory = OrderHistory::create([
                'order_id'        => $orderId,
                'previous_status' => $orderHistory->current_status,
                'current_status'  => $status,
            ]);
        }
        ResponseService::set([
            'status'           => true,
            'order_history_id' => $orderHistory->id,
        ]);
        return ResponseService::response();
    }

    public function bestSelling(int $orderId)
    {
        $orderItems = OrderLineItem::where(['order_id' => $orderId])->get();
        if (!blank($orderItems)) {
            foreach ($orderItems as $item) {
                $bestProduct = ShopProduct::where([
                    'product_id' => $item->product_id,
                    'shop_id'    => $item->shop_id,
                ])->first();
                if ($bestProduct) {
                    $bestProduct->counter = $bestProduct->counter + $item->quantity;
                    $bestProduct->save();
                }
                if (!blank($item->product->categories)) {
                    foreach ($item->product->categories as $category) {
                        $bestCategory = BestSellingCategory::where([
                            'category_id' => $category->id,
                            'shop_id'     => $item->shop_id,
                        ])->first();
                        if ($bestCategory) {
                            $bestCategory->counter = $bestCategory->counter + $item->quantity;
                            $bestCategory->save();
                        } else {
                            BestSellingCategory::create([
                                'category_id' => $category->id,
                                'shop_id'     => $item->shop_id,
                                'counter'     => $item->quantity,
                            ]);
                        }
                    }
                }
            }
            ResponseService::set([
                'status'   => true,
                'order_id' => $orderId,
            ]);
        }
        return ResponseService::response();
    }

    public function order($data): object// done
    {
        $order = [
            'user_id'         => $data['user_id'],
            'shop_id'         => $data['shop_id'],
            'total'           => $data['total'] + $data['delivery_charge'],
            'sub_total'       => $data['total'],
            'delivery_charge' => $data['delivery_charge'],
            'status'          => OrderStatus::PENDING,
            'address'         => $data['address'],
            'mobile'          => $data['mobile'],
            'lat'             => $data['lat'] ?? 0,
            'long'            => $data['long'] ?? 0,
            'misc'            => json_encode(["remarks" => '']),
            'payment_method'  => $data['payment_method'],
            'payment_status'  => $data['payment_status'],
            'paid_amount'     => $data['paid_amount'],
        ];
        $order   = Order::create($order);
        $orderId = $order->id;
        OrderHistory::create([
            'order_id'        => $orderId,
            'previous_status' => null,
            'current_status'  => OrderStatus::PENDING,
        ]);

        if (!blank($data['items'])) {
            $i              = 0;
            $orderLineItems = [];
            foreach ($data['items'] as $item) {
                $optionTotal = 0;
                if (isset($item['options']) && !blank($item['options'])) {
                    foreach ($item['options'] as $option) {
                        $optionTotal += $option['price'];
                    }
                }
                $orderLineItems[$i] = [
                    'order_id'                  => $orderId,
                    'shop_id'                   => $item['shop_id'],
                    'product_id'                => $item['product_id'],
                    'quantity'                  => $item['quantity'],
                    'unit_price'                => $item['unit_price'],
                    'discounted_price'          => $item['discounted_price'],
                    'item_total'                => ($item['unit_price'] * $item['quantity']),
                    'shop_product_variation_id' => $item['shop_product_variation_id'],
                    'options'                   => json_encode($item['options']),
                    'options_total'             => $optionTotal,
                    'created_at'                => date('Y-m-d H:i:s'),
                    'updated_at'                => date('Y-m-d H:i:s'),
                ];
                $i++;
            }
            OrderLineItem::insert($orderLineItems);
            ResponseService::set([
                'status'   => true,
                'order_id' => $orderId,
            ]);
        } else {
            ResponseService::set(['message' => 'Items not found']);
        }

        $order       = Order::findOrFail($orderId);
        $order->misc = json_encode([
            'order_code' => 'ORD-' . MyString::code($orderId),
            'remarks'    => isset($data['remarks']) ? $data['remarks'] : '',
        ]);
        $order->save();
        if ($data['payment_status'] == PaymentStatus::PAID) {
            if($data['payment_method'] != PaymentMethod::WALLET ) {
                $addFund = app(TransactionService::class)->addFund(0, $order->user->balance_id, $data['payment_method'], $order->total, $orderId);
            }

            if($this->adminBalanceId != $order->user->balance_id) {
                app(TransactionService::class)->payment($order->user->balance_id, $this->adminBalanceId, $order->total, $orderId);
            }
        }

        return ResponseService::response();
    }

    public function orderUpdate(int $orderId, int $status)
    {
        $orderStatus = (object) [
            'status'  => false,
            'message' => 'Not found',
        ];
        if ($status == OrderStatus::ACCEPT) {
            $orderStatus = $this->accept($orderId);
        } elseif ($status == OrderStatus::REJECT) {
            $orderStatus = $this->reject($orderId);
        } elseif ($status == OrderStatus::CANCEL) {
            $orderStatus = $this->cancel($orderId);
        } elseif ($status == OrderStatus::PROCESS) {
            $orderStatus = $this->process($orderId);
        } elseif ($status == OrderStatus::COMPLETED) {
            $orderStatus = $this->completed($orderId);
        }

        if ($orderStatus->status) {
            ResponseService::set([
                'status'           => $orderStatus->status,
                'order_id'         => $orderStatus->order_id,
                'order_history_id' => $orderStatus->order_history_id,
            ]);
        }
        return ResponseService::response();
    }

}
