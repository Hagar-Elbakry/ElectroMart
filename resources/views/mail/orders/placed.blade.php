<x-mail::message>
    Thanks for your order!

    Your order {{$order->id}} has been placed successfully.

    Total : {{Number::currency($order->grand_total, 'EGp')}}
    Date : {{$order->created_at}}

<x-mail::button :url="$url">
    View Order
</x-mail::button>

Thanks for shopping with {{ config('app.name') }}
</x-mail::message>
