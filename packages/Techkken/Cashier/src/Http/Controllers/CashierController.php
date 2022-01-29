<?php

namespace Techkken\Cashier\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Techkken\Cashier\DataGrids\CashierDataGrid;
use Webkul\Admin\DataGrids\OrderDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Repositories\OrderRepository;
use \Webkul\Sales\Repositories\OrderCommentRepository;

class CashierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * OrderCommentRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderCommentRepository
     */
    protected $orderCommentRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\OrderCommentRepository  $orderCommentRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderCommentRepository $orderCommentRepository
    ) {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;

        $this->orderCommentRepository = $orderCommentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $qry = Order::query();

            if ($request->has('order_number') && $request->order_number != '')
                $qry->where('id', $request->order_number);

            if ($request->has('search_keyword') && $request->search_keyword != '') {
                $qry->where(function ($sub_qry) use ($request) {
                    $sub_qry->Where('customer_first_name', 'LIKE', (string) $request->search_keyword . '%');
                    $sub_qry->orWhere('customer_last_name', 'LIKE', (string) $request->search_keyword . '%');
                    $sub_qry->orWhere('customer_company_name', 'LIKE', (string) $request->search_keyword . '%');
                });
            }

            if ($request->has('status') && $request->status != 'None') {
                $qry->where('status', strtolower($request->status));
            }
            else {
                $qry->where('status', 'LIKE', 'pending%');
            }

            if ($request->has('date_start') && $request->has('date_end') && $request->date_start != '' && $request->date_end != '') {
                $ds = Carbon::createFromFormat('Y-m-d', $request->date_start);
                $de = Carbon::createFromFormat('Y-m-d', $request->date_end);

                $qry->WhereBetween('created_at', [$ds->startOfDay(), $de->endOfDay()]);
            }

            //Execute the select query built by refactoring
            $page_count = 50;
            $qry_res = $qry->orderBy('id')
                ->paginate($page_count);

            return view($this->_config['view'])->with(['orders' => $qry_res]);
        } catch (Exception $ex) {
            //Log::error('reading softwares.', [$ex->getMessage()]);
            return response()->json(['status' => $ex->getMessage()], 400);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function reloadTable()
    {
        try {
            $qry = Order::query();

            $qry->where('status', 'LIKE', 'pending%');


            //Execute the select query built by refactoring
            $page_count = 50;
            $qry_res = $qry->orderBy('id')
                ->paginate($page_count);

            return view($this->_config['view'])->with(['orders' => $qry_res]);

        } catch (Exception $ex) {
            //Log::error('reading softwares.', [$ex->getMessage()]);
            return response()->json(['status' => $ex->getMessage()], 400);
        }
    }


    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $order = $this->orderRepository->findOrFail($id);

        return view($this->_config['view'], compact('order'));
    }

    public function viewOrder($id)
    {
        $order = $this->orderRepository->findOrFail($id);

        $addresses = $order->addresses;

        $payment = $order->payment;

        $items = $order->items;

        $invoices = $order->invoices;

        $comments = $order->comments;

        $collection = collect($order, $addresses, $payment, $items, $invoices, $comments);

        return $collection;
    }

    /**
     * Cancel action for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $result = $this->orderRepository->cancel($id);

        if ($result) {
            session()->flash('success', trans('admin::app.response.cancel-success', ['name' => 'Order']));
        } else {
            session()->flash('error', trans('admin::app.response.cancel-error', ['name' => 'Order']));
        }

        return redirect()->back();
    }

    /**
     * Add comment to the order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comment($id)
    {
        $data = array_merge(request()->all(), [
            'order_id' => $id,
        ]);

        $data['customer_notified'] = isset($data['customer_notified']) ? 1 : 0;

        Event::dispatch('sales.order.comment.create.before', $data);

        $comment = $this->orderCommentRepository->create($data);

        Event::dispatch('sales.order.comment.create.after', $comment);

        session()->flash('success', trans('admin::app.sales.orders.comment-added-success'));

        return redirect()->back();
    }

    public  function ProcessOrder($id){
        $order = $this->orderRepository->findOrFail($id);
       $this->orderRepository->updateOrderStatus($order, 'processing');


        session()->flash('success', trans('Processing order:'.$id, ['name' => 'Order']));

        return redirect()->back();
    }
}
