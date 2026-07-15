<footer class="mt-4">
    <div class="amz-footer-back">
        <a href="#top">{{ __('messages.back_to_top') }}</a>
    </div>
    <div class="amz-footer-main">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-3 mb-4">
                    <h4>{{ __('messages.get_to_know_us') }}</h4>
                    <ul>
                        <li><a href="#">{{ __('messages.about') }}</a></li>
                        <li><a href="#">{{ __('messages.careers') }}</a></li>
                        <li><a href="#">{{ __('messages.press') }}</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 mb-4">
                    <h4>{{ __('messages.make_money') }}</h4>
                    <ul>
                        <li><a href="#">{{ __('messages.sell_products') }}</a></li>
                        <li><a href="#">{{ __('messages.become_affiliate') }}</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 mb-4">
                    <h4>{{ __('messages.let_us_help') }}</h4>
                    <ul>
                        <li><a href="{{ route('orders') }}">{{ __('messages.my_orders') }}</a></li>
                        <li><a href="#">{{ __('messages.shipping_policy') }}</a></li>
                        <li><a href="#">{{ __('messages.returns_policy') }}</a></li>
                        <li><a href="#">{{ __('messages.help') }}</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 mb-4">
                    <h4>{{ __('messages.payment') }}</h4>
                    <ul>
                        <li><i class="bi bi-credit-card-2-front"></i> Stripe</li>
                        <li><i class="bi bi-paypal"></i> PayPal</li>
                        <li><i class="bi bi-telephone"></i> +1 (800) 123-4567</li>
                        <li><i class="bi bi-envelope"></i> support@globmall.com</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="amz-footer-legal">
        <div class="amz-fl-logo"><i class="bi bi-globe2"></i> GlobMall</div>
        <p>&copy; {{ date('Y') }} GlobMall. {{ __('messages.all_rights_reserved') }}</p>
    </div>
</footer>
