<?php
/**
 * Created by PhpStorm.
 * User: miro
 * Date: 18. 9. 2018
 * Time: 15:17
 */

namespace App\Models;


use Illuminate\Support\Facades\DB;

abstract class BrImport
{
    protected $name;
    protected $filePathway;
    protected $table;
    protected $columns;
    protected $selectedColumns;
    protected $explicitColumns;
    protected $explicitOuterColumns;

    function __construct($name)
    {
        $this->name = $name;
        $this->table = config('imports.'.$name.'.table');
        $this->columns =  config('imports.'.$name.'.columns');
        $this->explicitColumns =  config('imports.'.$name.'.explicitColumns');
        $this->explicitOuterColumns =  config('imports.'.$name.'.explicitOuterColumns');

    }

    public function setFilePathway($filePathway) {
        $this->filePathway = $filePathway;
    }

    public function setSelectedColumns($selectedColumns) {
        $this->selectedColumns = $selectedColumns;
    }

    protected function getExplColumnsFromSel() {
        return array_intersect($this->selectedColumns,$this->explicitColumns);
    }

    abstract function import();
}