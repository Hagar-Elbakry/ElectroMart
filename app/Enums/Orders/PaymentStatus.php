<?php

namespace App\Enums\Orders;

enum PaymentStatus : string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';

    public function getLabel(): ?string {
        return match ($this) {
            self::Pending => 'pending',
            self::Completed => 'completed',
            self::Failed => 'failed',
        };
    }

    public function color() :?string {
        return match ($this) {
            self::Pending => 'gray',
            self::Completed => 'green',
            self::Failed => 'red',
        };
    }
}
