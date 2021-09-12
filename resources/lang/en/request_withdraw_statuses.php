<?php

use App\Enums\RequestWithdrawStatus;

return [
    RequestWithdrawStatus::PENDING   => 'Pending',
    RequestWithdrawStatus::ACCEPT    => 'Accept',
    RequestWithdrawStatus::DECLINE   => 'Decline',
    RequestWithdrawStatus::COMPLETED => 'Completed'
];
