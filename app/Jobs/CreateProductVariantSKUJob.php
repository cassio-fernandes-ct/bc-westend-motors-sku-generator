<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\BigcommerceApiService;

class CreateProductVariantSKUJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $productId;

    /**
     * Create a new job instance.
     */
    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $bigCommerceService = new BigCommerceApiService();
        $productID = $this->productId;      

        $productVariants = $bigCommerceService->getAllProductVariants($productID);

        if($productVariants['meta']['pagination']['total'] == 1) {
            $productModifiers = $bigCommerceService->getProductModifiers($productID);
            if(isset($productModifiers['data']) && count($productModifiers['data']) > 0) {
                $optionSetCountArray = array_map(function($optionsSet) {
                    return count($optionsSet['option_values']);
                }, $productModifiers['data']);
                $totalCount = array_reduce($optionSetCountArray, function($carry, $item) {
                    return $carry * $item;
                }, 1);
                if($totalCount <= 600) {
                    $productSKU = $productVariants['data'][0]['sku'];
                    foreach($productModifiers['data'] as $option) {
                        // Create Product Option Value Data set
                        $optionValues = [];
                        foreach($option['option_values'] as $key => $value) {
                            $optionValues[] = [
                                "is_default" => false,
                                "label" => $value['label'],
                                "sort_order" => $key,
                            ];
                        }
                        $productOptionValue = [
                            "product_id" => $option['product_id'],
                            "display_name" => $option['display_name'],
                            "type" => $option['type'],
                            "option_values" => $optionValues
                        ];

                        // Now Delete Product Modifier
                        $deleteModifier = $bigCommerceService->deleteProductModifier($option['product_id'], $option['id']);

                        // Now Create Product Option
                        $newOption = $bigCommerceService->createProductOption($option['product_id'], $productOptionValue);
                    }
                    // Now Create New Product Variants
                    $productOptions = [];
                    $allProductOptions = $bigCommerceService->getProductOptions($productID);

                    foreach($allProductOptions['data'] as $option) {
                        $productOptions[][$option['display_name']] = [
                            'option_id' => $option['id'],
                            'type' => $option['type'],
                            'option_display_name' => $option['display_name'],
                            'option_values' => array_map(function($opt) {
                                return [
                                    'id' => $opt['id'],
                                    'label' => $opt['label']
                                ];
                            }, $option['option_values'])
                        ];
                    }
                    $optionCombinations = $this->generateOptionCombinations($productOptions);
                    foreach($optionCombinations as $key => $combination) {
                        $variantData = [
                            "sku" => $productSKU . '-' . $key,
                            "option_values" => $combination
                        ];
                        
                        $productVariant = $bigCommerceService->createProductVariant($productID, $variantData);
                    }
                }

            }
        }
    }

    private function generateOptionCombinations($data) {
        $results = [[]];
        foreach ($data as $attribute) {
            $newResults = [];          
            $optionValues = array_values($attribute)[0]["option_values"];
            $optionData = [
                "option_display_name" => array_values($attribute)[0]['option_display_name'],
                "option_id" => array_values($attribute)[0]['option_id'],
            ];
            foreach ($results as $result) {
                foreach ($optionValues as $option) {
                    $newResults[] = array_merge($result, [array_merge($optionData, $option)]);
                }
            }
            $results = $newResults;
        }
        return $results;
    }
}
