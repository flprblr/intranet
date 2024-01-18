<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Automattic\WooCommerce\Client;
use App\Models\Ecommerce;
use App\Models\Stock;

class GetWoocommerceProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Woocommerce Products';

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

            // DB::table('stocks')->truncate();

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

                $productsPage = 1;
                $productsCount = 0;

                do {

                    $products = $woocommerce->get('products', [
                        'status' => 'publish',
                        'page' => $productsPage,
                        'per_page' => 100,
                        'order' => 'asc',
                    ]);

                    foreach($products as $product) {
                        $countTotal++;

                        if($product->type === 'simple') {
                            Stock::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'ecommerce_id' => $ecommerce->id,
                                ],
                                [
                                    'type' => $product->type,
                                    'sku' => $product->sku,
                                    'woo_stock' => $product->stock_quantity,
                                    'sap_stock' => $product->stock_quantity,
                                ]
                            );

                        }
                        elseif($product->type === 'variable') {

                            $variationsPage = 1;
                            $variationsCount = 0;

                            do {

                                $variations = $woocommerce->get('products/' . $product->id . '/variations', [
                                    'page' => $variationsPage,
                                    'per_page' => 10,
                                    'order' => 'asc',
                                ]);

                                foreach($variations as $variation) {
                                    Stock::updateOrCreate(
                                        [
                                            'product_id' => $product->id,
                                            'variation_id' => $variation->id,
                                            'ecommerce_id' => $ecommerce->id,
                                        ],
                                        [
                                            'type' => 'variation',
                                            'sku' => $variation->sku,
                                            'woo_stock' => $variation->stock_quantity,
                                            'sap_stock' => $variation->stock_quantity,
                                        ]
                                    );
                                }

                                $variationsCount = count($variations);
                                $variationsPage++;

                            } while ($variationsCount > 0);
                            
                        }
                        else {
                            Log::channel('woo-stocks')->info('SKU de tipo desconocido: ' . json_encode($product->sku));
                        }
                        
                    }

                    $productsCount = count($products);
                    $productsPage++;

                } while ($productsCount > 0);

            }
            
        } catch (\Exception $e) {
            Log::channel('woo-stock')->error($e->getMessage());
        }

        $endTime = Carbon::now();
        $executionTime = $endTime->diffInSeconds($startTime);
        $formattedTime = sprintf("%02d:%02d", floor($executionTime / 60), $executionTime % 60);
        Log::channel('woo-stocks')->info('GET ' . $countTotal  . ' Woocommerce Products: ' . $formattedTime);

        return Command::SUCCESS;
    }
}
