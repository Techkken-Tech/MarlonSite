<section>
    <div class="container">
        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
        <div class="tk-row newsletter-block">
            <div class="col-md-6 col-sm-12 tk-flex">
                <span class="title tk-text-size1">
                    <?php echo __("Subscribe to our newsletter and receive exclusive offers every week"); ?>
                <span>
            </div>
            <div class="col-md-6 col-sm-12 tk-flex">
                <span class="actions">
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
                </span>
            </div>

        </div>
        @endif
    </div>
</section>