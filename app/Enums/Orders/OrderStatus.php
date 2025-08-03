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

    public function icon(): ?string {
        return match ($this) {
            self::New => 'heroicon-o-sparkles',
            self::Processing => 'heroicon-o-arrow-path',
            self::Shipped => 'heroicon-o-truck',
            self::Delivered => 'heroicon-o-check-circle',
            self::Canceled => 'heroicon-o-x-circle',
        };
    }

    public static function colors(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->color()])
            ->toArray();
    }

    public static function icons(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->icon()])
            ->toArray();
    }

}
