<?php
namespace Webkul\Payment\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Illuminate\Support\Facades\Log;
/**
 * Generate Invoice Event handler
 *
 */
class GenerateInvoice
{
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * InvoiceRepository object
     *
     * @var \Webkul\Sales\Repositories\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Create the event listener.
     *
     * @param  Webkul\Sales\Repositories\OrderRepository $orderRepository
     * @param \Webkul\Sales\Repositories\InvoiceRepository invoiceRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository
        )
        {
            $this->orderRepository = $orderRepository;
            $this->invoiceRepository = $invoiceRepository;
        }

    /**
     * Generate a new invoice.
     *
     * @param  object  $order
     * @return void
     */
    public function handle($order)
    {
        if ($order->payment->method == 'gcash' && core()->getConfigData('sales.paymentmethods.gcash.generate_invoice')) {
            Log::channe('rdebug')->info(
                "Generating invoice for gcash"
            );
            $this->invoiceRepository->create($this->prepareInvoiceData($order), core()->getConfigData('sales.paymentmethods.gcash.invoice_status'), core()->getConfigData('sales.paymentmethods.gcash.order_status'));
            
            event(new \App\Events\NewOrderPlaced());
        }

    }

    /**
     * Prepares order's invoice data for creation.
     *
     * @return array
     */
    protected function prepareInvoiceData($order)
    {
        $invoiceData = ['order_id' => $order->id];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}