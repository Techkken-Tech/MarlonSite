<div class="container">
    @if (core()->getConfigData('customer.settings.newsletter.subscription'))
    <div class="tk-row newsletter-block">
        <span><?php echo __("Subscribe to our newsletter and receive exclusive offers every week"); ?></span>
        <span></span>
    </div>
    <div class="subscribe-newsletter col-lg-6">
        <div class="form-container">
            <form action="{{ route('shop.subscribe') }}">
                <div class="subscriber-form-div">
                    <div class="control-group">
                        <input type="email" name="subscriber_email" class="control subscribe-field" placeholder="{{ __('Enter email address') }}" aria-label="Newsletter" required />

                        <button class="theme-btn subscribe-btn fw6">
                            {{ __('shop::app.subscription.subscribe') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>