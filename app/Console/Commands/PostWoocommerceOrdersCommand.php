<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Automattic\WooCommerce\Client;
use App\Models\Ecommerce;
use App\Models\Order;
use App\Models\OrderStatus;

class PostWoocommerceOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:woocommerce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Woocommerce Orders in Completed Status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $startTime = Carbon::now();

        $from = Carbon::now()->subDays(100)->toIso8601String();
        $to = Carbon::now()->toIso8601String();

        try {
            $ecommerces = Ecommerce::select('id', 'prefix', 'url', 'api_key', 'api_secret')
                                    ->where('status', '=', '1')
                                    ->get();

            foreach ($ecommerces as $ecommerce) {
                // $woocommerce = new Client(
                //     $ecommerce->url,
                //     $ecommerce->api_key,
                //     $ecommerce->api_secret,
                //     [
                //         'wp_api' => true,
                //         'version' => 'wc/v3',
                //         'timeout' => 10
                //     ]
                // );

                // $page = 1;
                // $processingOrdersCount = 0;
                // do {
                //     $processingOrders = $woocommerce->get('orders', [
                //         'status' => 'completed',
                //         'page' => $page,
                //         'per_page' => 100,
                //         'order' => 'asc',
                //         'dp' => 0,
                //         'after' => $from,
                //         'before' => $to
                //     ]);

                //     foreach ($processingOrders as $orderData) {
                //         $this->changeStatus($orderData->id, 10, $ecommerce);
                //     }

                //     $processingOrdersCount = count($processingOrders);
                //     $page++;

                // } while ($processingOrdersCount > 0);

                $woocommerce = new Client(
                    $ecommerce->url,
                    $ecommerce->api_key,
                    $ecommerce->api_secret,
                    [
                        'wp_api' => true,
                        'version' => 'wc/v3',
                        'timeout' => 10
                    ]
                );

                $page = 1;
                $processingOrdersCount = 0;
                do {
                    $processingOrders = $woocommerce->get('orders', [
                        'status' => 'pos-ok',
                        'page' => $page,
                        'per_page' => 100,
                        'order' => 'asc',
                        'dp' => 0,
                        'after' => $from,
                        'before' => $to
                    ]);

                    foreach ($processingOrders as $orderData) {
                        $this->changeStatus($orderData->id, 11, $ecommerce);
                    }

                    $processingOrdersCount = count($processingOrders);
                    $page++;

                } while ($processingOrdersCount > 0);
            }

        } catch (\Exception $e) {
            Log::channel('woo-orders')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        Log::channel('woo-orders')->info('PUT Woocommerce Orders: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

    private function changeStatus($orderId, $statusId, $ecommerce)
    {

        $order = Order::where('prefix', '=', $ecommerce->prefix)
                    ->where('order_number', '=', $orderId)
                    ->first();

        if($order) {
            $order->order_status_id = $statusId;
            $order->save();
        }

        $status = OrderStatus::where('id', $statusId)->pluck('slug')->first();

        $woocommerce = new Client(
            $ecommerce->url,
            $ecommerce->api_key,
            $ecommerce->api_secret,
            [
                'wp_api' => true,
                'version' => 'wc/v3',
                'timeout' => 10
            ]
        );

        $woocommerce->put("orders/{$order->order_number}", [
            'status' => $status
        ]);
        
    }
}
