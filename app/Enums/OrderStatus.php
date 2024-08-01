<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Out_for_delivery = 'out_for_delivery';
    case Delivered = 'delivered';
    case Canceled = 'canceled';
    case Returned = 'returned';
}
