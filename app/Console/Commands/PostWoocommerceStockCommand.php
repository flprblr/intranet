<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Automattic\WooCommerce\Client;
use App\Models\Ecommerce;
use App\Models\Stock;

class PostWoocommerceStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Woocommerce Stock';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $startTime = Carbon::now();
        
        try {

            $countTotal = 0;

            $ecommerces = Ecommerce::select('id','url','api_key','api_secret')->where('status', 1)->get();

                foreach ($ecommerces as $ecommerce) {

                    $woocommerce = new Client(
                        $ecommerce->url,
                        $ecommerce->api_key,
                        $ecommerce->api_secret,
                        [
                            'wp_api' => true,
                            'version' => 'wc/v3',
                            'timeout' => 10000
                        ]
                    );

                    $simples = Stock::select('product_id','SKU','sap_stock')
                                    ->where('ecommerce_id', $ecommerce->id)
                                    ->where('type', 'simple')
                                    ->whereRaw('woo_stock != sap_stock')
                                    ->orderBy('product_id')
                                    ->get();

                    if ($simples->isNotEmpty()) {
                        $data = ['update' => []];

                        foreach ($simples as $simple) {
                            $countTotal++;
                            $data['update'][] = [
                                'id' => $simple->product_id,
                                'stock_quantity' => $simple->sap_stock,
                                'manage_stock' => true,
                            ];
                        }

                        $woocommerce->post('products/batch', $data);
                    }

                    $variables = Stock::select('product_id')
                                    ->where('ecommerce_id', $ecommerce->id)
                                    ->where('type', 'variation')
                                    ->whereRaw('woo_stock != sap_stock')
                                    ->orderBy('product_id')
                                    ->get();

                    if ($variables->isNotEmpty()) {
                        foreach ($variables as $variable) {
                            $countTotal++;
                            $variations = Stock::select('variation_id','SKU','sap_stock')
                                        ->where('product_id', $variable->product_id)
                                        ->whereRaw('woo_stock != sap_stock')
                                        ->orderBy('variation_id')
                                        ->get();
    
                            $data = ['update' => []];
    
                            foreach($variations as $variation) {
                                $data['update'][] = [
                                    'id' => $variation->variation_id,
                                    'stock_quantity' => $variation->sap_stock,
                                    'manage_stock' => true,
                                ];
                                
                            }
                            
                            $woocommerce->post('products/' . $variable->product_id . '/variations/batch', $data);
                        }
                    }
                    
                }

            DB::table('stocks')->truncate();
            
        } catch (\Exception $e) {
            Log::channel('woo-stocks')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        $formattedTime = sprintf("%02d:%02d", floor($executionTime / 60), $executionTime % 60);
        Log::channel('woo-stocks')->info('POST ' . $countTotal  . ' Product Stock: ' . $formattedTime);

        return Command::SUCCESS;
    }
}
