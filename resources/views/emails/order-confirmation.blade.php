<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.order_confirmation') }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #2563eb; color: #fff; padding: 20px; text-align: center; }
        .body { padding: 30px; }
        .order-info { background: #f8fafc; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .item-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .total { font-size: 18px; font-weight: bold; color: #2563eb; text-align: right; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; color: #999; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ __('messages.order_placed') }}</h2>
            <p>Order #{{ $order->order_no }}</p>
        </div>
        <div class="body">
            <p>Hi {{ $order->user->name }},</p>
            <p>Your order has been placed successfully. Here are the details:</p>
            <div class="order-info">
                @foreach($order->items as $item)
                <div class="item-row">
                    <span>{{ $item->product_name }} x {{ $item->quantity }}</span>
                    <span>${{ number_format($item->subtotal, 2) }}</span>
                </div>
                @endforeach
            </div>
            <div class="total">
                Total: ${{ number_format($order->total, 2) }}
            </div>
            <p style="margin-top: 20px;">We'll send you another email when your order ships.</p>
            <p><a href="{{ route('order.detail', $order->order_no) }}" style="background: #2563eb; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">{{ __('messages.view_order') }}</a></p>
        </div>
        <div class="footer">
            <p>GlobMall - Global Online Shopping</p>
        </div>
    </div>
</body>
</html>