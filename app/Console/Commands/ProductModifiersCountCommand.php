<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BigcommerceApiService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductModifiersExport;

class ProductModifiersCountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:product-modifiers-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bigCommerceService = new BigCommerceApiService();
        $products = $bigCommerceService->getAllProducts(250, 1);
        $totalItems = $products["meta"]["pagination"]["total"];
        $currentItem = 1;
        $currentPage = 1;
        $csvExportData = [];
        $csvIndex = 1;
        $totalPages = $products["meta"]["pagination"]["total_pages"];

        while ($currentPage <= $totalPages) {
            if ($currentPage != 1) {
                $products = $bigCommerceService->getAllProducts(250, $currentPage);
            }
            if(isset($products['data']) && count($products['data']) > 0) {
                foreach($products['data'] as $index => $product) {
                    $this->info("Product ID: " . $product['id'] . ". Progress: " . $currentItem . '/' . $totalItems . " Page count: " . $currentPage . '/' . $totalPages); 
                    $csvExportData[$csvIndex]['PRODUCT_ID'] = $product['id'];
                    $csvExportData[$csvIndex]['PRODUCT_NAME'] = $product['name'];

                    $productModifiers = $bigCommerceService->getProductModifiers($product['id']);

                    if(isset($productModifiers['data']) && count($productModifiers['data']) > 0) {
                        $csvExportData[$csvIndex]['OPTION_SET_COUNT'] = count($productModifiers['data']);
                        $optionSetCountArray = array_map(function($optionsSet) {
                            return count($optionsSet['option_values']);
                        }, $productModifiers['data']);
                        $totalCount = array_reduce($optionSetCountArray, function($carry, $item) {
                            return $carry * $item;
                        }, 1);
                        $csvExportData[$csvIndex]['OPTION_SET_MAX_VARIANTS'] = $totalCount;

                        foreach($productModifiers['data'] as $key => $optionSets) {
                            $csvExportData[$csvIndex]['OPTION_SET_' . $key + 1] = count($optionSets['option_values']);
                            $optionSetArray[] = count($optionSets['option_values']);
                        }

                    } else {
                        $csvExportData[$csvIndex]['OPTION_SET_COUNT'] = "--";
                        $csvExportData[$csvIndex]['OPTION_SET_MAX_VARIANTS'] = "--";
                        $csvExportData[$csvIndex]['OPTION_SET_1'] = "--";
                    }
                    $csvIndex ++;
                    $currentItem++;
                }
                $currentPage++;
            }
        }

        Excel::store(new ProductModifiersExport($csvExportData), 'exports/products.csv');
        $this->info("Process Done!");
    }
}
