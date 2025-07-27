<?php

namespace App\Jobs;

use App\Models\FileUpload;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fileUpload;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\FileUpload  $fileUpload
     * @return void
     */
    public function __construct(FileUpload $fileUpload)
    {
        $this->fileUpload = $fileUpload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->fileUpload->update(['status' => 'processing']);

        try {
            $fileContents = Storage::disk('local')->get('private/uploads/' . $this->fileUpload->file_name);
            $file = tmpfile();
            fwrite($file, $fileContents);
            rewind($file);

            // Skip the header row
            fgetcsv($file);

            $data = [];
            while (($row = fgetcsv($file)) !== false) {
                $data[] = [
                    'UNIQUE_KEY' => $row[0],
                    'PRODUCT_TITLE' => $row[1],
                    'PRODUCT_DESCRIPTION' => $row[2],
                    'STYLE#' => $row[3],
                    'AVAILABLE_SIZES' => $row[4],
                    'BRAND_LOGO_IMAGE' => $row[5],
                    'THUMBNAIL_IMAGE' => $row[6],
                    'COLOR_SWATCH_IMAGE' => $row[7],
                    'PRODUCT_IMAGE' => $row[8],
                    'SPEC_SHEET' => $row[9],
                    'PRICE_TEXT' => $row[10],
                    'SUGGESTED_PRICE' => $row[11],
                    'CATEGORY_NAME' => $row[12],
                    'SUBCATEGORY_NAME' => $row[13],
                    'COLOR_NAME' => $row[14],
                    'COLOR_SQUARE_IMAGE' => $row[15],
                    'COLOR_PRODUCT_IMAGE' => $row[16],
                    'COLOR_PRODUCT_IMAGE_THUMBNAIL' => $row[17],
                    'SIZE' => $row[18],
                    'QTY' => $row[19],
                    'PIECE_WEIGHT' => $row[20],
                    'PIECE_PRICE' => $row[21],
                    'DOZENS_PRICE' => $row[22],
                    'CASE_PRICE' => $row[23],
                    'PRICE_GROUP' => $row[24],
                    'CASE_SIZE' => $row[25],
                    'INVENTORY_KEY' => $row[26],
                    'SIZE_INDEX' => $row[27],
                    'SANMAR_MAINFRAME_COLOR' => $row[28],
                    'MILL' => $row[29],
                    'PRODUCT_STATUS' => $row[30],
                    'COMPANION_STYLES' => $row[31],
                    'MSRP' => $row[32],
                    'MAP_PRICING' => $row[33],
                    'FRONT_MODEL_IMAGE_URL' => $row[34],
                    'BACK_MODEL_IMAGE' => $row[35],
                    'FRONT_FLAT_IMAGE' => $row[36],
                    'BACK_FLAT_IMAGE' => $row[37],
                    'PRODUCT_MEASUREMENTS' => $row[38],
                    'PMS_COLOR' => $row[39],
                    'GTIN' => $row[40],
                ];
            }

            fclose($file);

            // Clean up non-UTF-8 characters
            array_walk_recursive($data, function (&$item) {
                $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
            });

            // Upsert the data
            Product::upsert($data, ['UNIQUE_KEY'], [
                'PRODUCT_TITLE', 'PRODUCT_DESCRIPTION', 'STYLE#', 'AVAILABLE_SIZES', 'BRAND_LOGO_IMAGE',
                'THUMBNAIL_IMAGE', 'COLOR_SWATCH_IMAGE', 'PRODUCT_IMAGE', 'SPEC_SHEET', 'PRICE_TEXT',
                'SUGGESTED_PRICE', 'CATEGORY_NAME', 'SUBCATEGORY_NAME', 'COLOR_NAME', 'COLOR_SQUARE_IMAGE',
                'COLOR_PRODUCT_IMAGE', 'COLOR_PRODUCT_IMAGE_THUMBNAIL', 'SIZE', 'QTY', 'PIECE_WEIGHT',
                'PIECE_PRICE', 'DOZENS_PRICE', 'CASE_PRICE', 'PRICE_GROUP', 'CASE_SIZE', 'INVENTORY_KEY',
                'SIZE_INDEX', 'SANMAR_MAINFRAME_COLOR', 'MILL', 'PRODUCT_STATUS', 'COMPANION_STYLES',
                'MSRP', 'MAP_PRICING', 'FRONT_MODEL_IMAGE_URL', 'BACK_MODEL_IMAGE', 'FRONT_FLAT_IMAGE',
                'BACK_FLAT_IMAGE', 'PRODUCT_MEASUREMENTS', 'PMS_COLOR', 'GTIN'
            ]);

            $this->fileUpload->update(['status' => 'completed']);
        } catch (\Exception $e) {
            $this->fileUpload->update(['status' => 'failed']);
            // Log the error
            \Log::error('CSV Processing failed: ' . $e->getMessage());
        }
    }
}
