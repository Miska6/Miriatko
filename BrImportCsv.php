<?php
/**
 * Created by PhpStorm.
 * User: miro
 * Date: 18. 9. 2018
 * Time: 15:47
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;

abstract class BrImportCsv extends \App\Models\BrImport
{

    private $delimiter;

    public function __construct($name)
    {
        parent::__construct($name);
        $this->delimiter = config('imports.'.$name.'.delimiter');
    }

    public function import() {
        $file = fopen($this->filePathway,'r');
        while(($row = fgetcsv($file,null,$this->delimiter)) !== FALSE) {
            $assocRow = array_combine($this->columns,$row);
            $this->filterSelectedCols($assocRow);
            $explicitOuterCols = $this->dropExplicitOuterCols($assocRow);
            $this->equipExplicitCols($assocRow);

            $lastInsertId = DB::table($this->table)->insertGetId($assocRow);

            foreach($explicitOuterCols as $colName => $explicitOuterCol) {
                $this->equipExplicitOuterCol($colName,$explicitOuterCol,$lastInsertId);
            }
        }
    }

    private function filterSelectedCols(&$assocRow) {
        $selectedColumnsFlipped = array_flip($this->selectedColumns);
        $assocRow = array_intersect_key($assocRow, $selectedColumnsFlipped);
    }

    private function dropExplicitOuterCols(&$assocRow) {
        $explicitCols = [];
        foreach($this->explicitOuterColumns as $explicitOuterColumn) {
            if(isset($assocRow[$explicitOuterColumn])) {
                $explicitCols[$explicitOuterColumn] = $assocRow[$explicitOuterColumn];
                unset($assocRow[$explicitOuterColumn]);
            }
        }
        return $explicitCols;
    }

    protected function equipExplicitCols(&$assocRow) {
        foreach($this->getExplColumnsFromSel() as $explicitCol) {
            if(isset($assocRow[$explicitCol])) {
                $assocRow[$explicitCol] = $this->equipExplicitCol($explicitCol, $assocRow[$explicitCol]);
            }
        }
    }

    protected function equipExplicitCol($colName,$rawValue) {
        if(method_exists($this,'equipExplicit'.ucfirst($colName))) {
            return $this->{'equipExplicit'.ucfirst($colName)}($rawValue);
        };
        return $rawValue;
    }

    protected function equipExplicitOuterCol($colName,$rawValue,$id) {
        if(method_exists($this,'equipExplicitOuter'.ucfirst($colName))) {
            $this->{'equipExplicitOuter'.ucfirst($colName)}($rawValue,$id);
        };
    }

}