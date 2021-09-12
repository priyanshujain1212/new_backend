<?php

use App\Enums\PaymentMethod;

return [
    PaymentMethod::CASH_ON_DELIVERY => 'Cash On Delivery',
    PaymentMethod::PAYPAL           => 'Paypal',
    PaymentMethod::PAYTM            => 'Paytm',
    PaymentMethod::STRIPE           => 'Stripe',
    PaymentMethod::WALLET           => 'Credit'
];
