<?php
/**
 * Created by PhpStorm.
 * User: miro
 * Date: 19. 9. 2018
 * Time: 21:05
 */

namespace App\Models;

use App\Models\Product;


class BrImportProductCvs extends BrImportCsv
{

    public function __construct($name)
    {
        parent::__construct($name);
    }

    protected function equipExplicitOuterCategories($rawValue,$id) {
        $catList = explode('|',$rawValue);
        $product = Product::find($id);
        $product->categories()->sync($catList);
    }

    protected function equipExplicitSku($rawValue) {
        return $rawValue.'_import';
    }

}