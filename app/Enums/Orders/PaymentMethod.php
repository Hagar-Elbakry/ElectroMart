<?php

namespace App\Enums\Orders;

enum PaymentMethod : string
{
    case Cash = 'cash';
    case CreditCard = 'credit cards';

}
