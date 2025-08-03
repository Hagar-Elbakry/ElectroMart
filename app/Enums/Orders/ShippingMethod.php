<?php

namespace App\Enums\Orders;

enum ShippingMethod :string
{
    case FedEx = 'fedex';
    case UPS = 'ups';
    case DHL = 'dhl';
}
