<?php

namespace Techkken\OrderStatusUpdates\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;

class OrderStatusUpdatesController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Current customer.
     */
    protected $currentCustomer;

    /**
     * OrderrRepository object
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository
    )
    {
        $this->middleware('customer');

        $this->_config = request('_config');


        $this->currentCustomer = auth()->guard('customer')->user();

        $this->orderRepository = $orderRepository;

        $this->invoiceRepository = $invoiceRepository;

    }


    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function index($id)
    {
        $order = $this->orderRepository->findOneWhere([
            'customer_id' => $this->currentCustomer->id,
            'id'          => $id,
        ]);

        if (! $order) {
            abort(404);
        }

        return view($this->_config['view'], compact('order'));
    }




}
