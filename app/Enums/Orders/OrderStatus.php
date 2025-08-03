<?php

namespace App\Enums\Orders;

enum OrderStatus :string
{
    case New = 'new';
    case Processing = 'processing';
    case Shipped = 'shipped';

    case Delivered = 'delivered';
    case Canceled = 'canceled';

    public function getLabel(): ?string {
        return match ($this) {
            self::New => 'New',
            self::Processing => 'Processing',
            self::Shipped => 'Shipped',
            self::Delivered => 'Delivered',
            self::Canceled => 'Canceled',
        };
    }

    public function color() : ?string {
        return match ($this) {
            self::New => 'blue',
            self::Processing => 'yellow',
            self::Shipped => 'gray',
            self::Delivered => 'green',
            self::Canceled => 'red',
        };
    }

}
