<?php

use App\Enums\OrderStatus;

return [

    OrderStatus::PENDING    => "Pending",
    OrderStatus::CANCEL     => "Cancel",
    OrderStatus::ACCEPT     => "Accept",
    OrderStatus::REJECT     => "Reject",
    OrderStatus::PROCESS    => "Process",
    OrderStatus::ON_THE_WAY => "On the Way",
    OrderStatus::COMPLETED  => "Completed",

];
