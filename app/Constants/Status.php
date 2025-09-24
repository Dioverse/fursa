<?php

namespace App\Constants;

class Status{

    const ENABLE = 1;
    const DISABLE = 0;

    const O_PENDING = 'pending';
    const O_CONFIRM = 'confirmed';
    const O_PROCESS = 'processing';
    const O_SHIP = 'shipping';
    const O_SHIPPED = 'shipped';
    const O_OUT_FOR_DEL = 'out for delivery';
    const O_DELIVERED = 'delivered';
    const O_CANCELLED = 'cancelled';
    const O_FAILED = 'failed';
}
