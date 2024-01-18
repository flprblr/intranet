<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Automattic\WooCommerce\Client;
use App\Models\Ecommerce;
use App\Models\Payment;
use App\Models\Order;
use App\Models\OrderDetail;

class GetWoocommerceOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:woocommerce';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Woocommerce Orders in Processing Status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = Carbon::now();
        
        $from = Carbon::now()->subDays(90)->toIso8601String();
        $to = Carbon::now()->toIso8601String();

        try {
            $ecommerces = Ecommerce::all();

            foreach ($ecommerces as $ecommerce) {
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
                        'status' => 'processing',
                        'page' => $page,
                        'per_page' => 100,
                        'order' => 'asc',
                        'dp' => 0,
                        'after' => $from,
                        'before' => $to
                    ]);


                    foreach ($processingOrders as $orderData) {

                        $order = Order::where('prefix', $ecommerce->prefix)
                                    ->where('order_number', $orderData->id)
                                    ->first();

                        if(!$order) {

                            if($orderData->payment_method === 'woo-mercado-pago-basic') {

                                $payment = new Payment();
                                $payment->method = 'Mercado Pago';
                                $payment->date = $orderData->date_paid_gmt;

                                foreach($orderData->meta_data as $metaData) {
                                    
                                    if (preg_match('/^Mercado Pago - (\d+) - (.*)$/', $metaData->key, $matches)) {
                                        
                                        $paymentId = $matches[1];
                                        $paymentInfo = $matches[2];
                        
                                        switch($paymentInfo) {
                                            case "payment_type":
                                                if ($metaData->value === 'account_money') {
                                                    $payment->amount = $orderData->total;
                                                    $payment->last_digits = '0000';
                                                }
                                                $payment->type = $this->getType($metaData->value);
                                                break;
                                            case "transaction_amount":
                                                $payment->amount = $metaData->value;
                                                break;
                                            case "card_last_four_digits":
                                                $payment->last_digits = $metaData->value;
                                                break;
                                            default:
                                                break;
                                        }
                                        $payment->external_id = $paymentId;
                                    }
                                }
                                $payment->save();
                            } // Mercado Pago Checkout Pro


                            if($orderData->payment_method === 'transbank_webpay_plus_rest') {

                                $payment = new Payment();
                                $payment->method = 'Webpay Plus';
                                $payment->date = $orderData->date_paid_gmt;

                                foreach($orderData->meta_data as $metaData) {
                                    switch ($metaData->key) {
                                        case "paymentCodeResult":
                                            $payment->type = $this->getType($metaData->value);
                                            break;
                                        case "amount":
                                            $payment->amount = $metaData->value;
                                            break;
                                        case "cardNumber":
                                            $payment->last_digits = $metaData->value;
                                            break;
                                        case "authorizationCode":
                                            $payment->external_id = $metaData->value;
                                            break;
                                        default:
                                            break;
                                    }
                                }
                                $payment->save();
                            } // Transbank Webpay Plus

                            $order = new Order();
                            $order->prefix = $ecommerce->prefix;
                            $order->order_number = $orderData->id;
                            $order->date_created = $orderData->date_created;
                            $order->date_modified = $orderData->date_modified;
                            $order->shipping_total = intval($orderData->shipping_total);
                            $order->net = intval($orderData->total) / 1.19;
                            $order->tax = intval($orderData->total) - (intval($orderData->total) / 1.19);
                            $order->total = intval($orderData->total);
                            $order->billing_first_name = ucwords(strtolower($orderData->billing->first_name));
                            $order->billing_last_name = ucwords(strtolower($orderData->billing->last_name));
                            $order->billing_phone = str_replace(' ', '', $orderData->billing->phone);
                            $order->billing_email = strtolower($orderData->billing->email);
                            $order->billing_country = $orderData->billing->country;
                            $order->billing_state = $orderData->billing->state;
                            $order->billing_city = $orderData->billing->city;
                            $order->billing_address_1 = ucwords(strtolower($orderData->billing->address_1));
                            $order->order_status_id = 1;
                            $order->ecommerce_id = $ecommerce->id;
                            $order->payment_id = $payment->id;

                            foreach ($orderData->meta_data as $metaData) {
                                if ($metaData->key === '_billing_rut') {
                                    $order->billing_rut = $this->formatRut($metaData->value);
                                    break;
                                }
                            }

                            if (intval($orderData->discount_total) > 0) {
                                foreach ($orderData->coupon_lines as $coupon) {
                                    $order->discount_code = $coupon->code;
                                    foreach ($coupon->meta_data as $couponMetaData) {
                                        $order->discount_type = $couponMetaData->value->discount_type;
                                        $order->discount_amount = $couponMetaData->value->amount;
                                    }
                                    $order->discount_total = intval($orderData->discount_total);
                                    break;
                                }
                            }
                            
                            $order->save();

                            foreach ($orderData->line_items as $item) {
                                $orderDetail = new OrderDetail;
                                $orderDetail->sku = $item->sku;
                                $orderDetail->description = $item->name;
                                $orderDetail->unit_price = intval($item->subtotal) / $item->quantity;
                                $orderDetail->quantity = $item->quantity;
                                $orderDetail->subtotal = intval($item->subtotal);
                                $orderDetail->final_price = intval($item->price);
                                $orderDetail->discount = intval($item->subtotal) - intval($item->total);
                                $orderDetail->total = intval($item->total);
                                $orderDetail->order_id = $order->id;
                                $orderDetail->save();
                            }

                            if (intval($orderData->shipping_total) > 0) {
                                $orderDetail = new OrderDetail;
                                $orderDetail->sku = '000000001000000031';
                                $orderDetail->description = 'GASTOS DE ENVIO';
                                $orderDetail->unit_price = intval($orderData->shipping_total);
                                $orderDetail->quantity = 1;
                                $orderDetail->subtotal = intval($orderData->shipping_total);
                                $orderDetail->final_price = intval($orderData->shipping_total);
                                $orderDetail->discount = 0;
                                $orderDetail->total = intval($orderData->shipping_total);
                                $orderDetail->order_id = $order->id;
                                $orderDetail->save();
                            }
                        }
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
        Log::channel('woo-orders')->info('GET Woocommerce Orders: ' . $executionTime . ' seconds');

        return Command::SUCCESS;
    }

	private function getType($originType)
	{
		switch ($originType) {
			case 'debit_card':
				$paymentType = 'Débito';
				break;
			case 'credit_card':
				$paymentType = 'Crédito';
				break;
			case 'account_money':
				$paymentType = 'Débito';
				break;
			case 'Venta Débito':
				$paymentType = 'Débito';
				break;
			case 'Venta Crédito':
				$paymentType = 'Crédito';
				break;
			default:
                $paymentType = 'Crédito';
				break;
		}

		return $paymentType;
	}
	
	private function formatRut($rut) {

    	$rut = preg_replace('/[^0-9kK]/', '', $rut);
        $body = substr($rut, 0, -1);
        $dv = substr($rut, -1);
    
        return $body . '-' . $dv;
    }
}
