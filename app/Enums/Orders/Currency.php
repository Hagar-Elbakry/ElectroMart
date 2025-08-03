<?php

namespace App\Enums\Orders;

enum Currency :string
{
    case EGP = 'egp';
    case EUR = 'eur';
    case SAR = 'sar';
    case USD = 'usd';
}
