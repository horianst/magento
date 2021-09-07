<?php
namespace Urgent\Cargus\Controller\Get;

use Magento\Framework\App\Action\Action;
use Urgent\Cargus\Model\UrgentCargus;
use Magento\Framework\App\ResourceConnection;

class Cities extends Action
{
    protected $resource;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
        return parent::__construct($context);
    }

    public function execute()
    {
        if ($this->getRequest()->getParam('id')) {
            $get_judet = trim(addslashes($this->getRequest()->getParam('id')));
            if ($get_judet == '') {
                echo 'null';
                die();
            } else {
                if (is_numeric($get_judet)) {
                    $id_judet = $get_judet;
                } else {
                    $judet['code'] = $get_judet;
                }
            }
        } else {
            echo 'null';
            die();
        }

        if ($this->getRequest()->getParam('val')) {
            $val = addslashes($this->getRequest()->getParam('val'));
        } else {
            $val = '';
        }

        $connection  = $this->resource->getConnection();

        if (isset($id_judet)) {
            $sql = "SELECT * FROM ".$connection->getTableName('directory_country_region')." WHERE country_id = 'RO' AND region_id = '".$id_judet."'";
            $result = $connection->fetchAll($sql);
            if (is_array($result) && count($result) == 1) {
                $judet = $result[0];
            } else {
                echo 'null';
                die();
            }
        }

        $urgentCargus = new UrgentCargus();

        // obtin lista de judete din api
        $judete = array();
        $dataJudete = $urgentCargus->getCounties();

        try{
            foreach ($dataJudete as $j) {
                $judete[strtolower($j->Abbreviation)] = $j->CountyId;
            }

            // obtin lista de localitati pe baza abrevierii judetului
            $localitati = $urgentCargus->getCities($judete[strtolower($judet['code'])]);
        }catch (\Exception $e){
            echo $e->getMessage();
        }

        echo '<option value="" rel="">-</option>'."\n";
        foreach ($localitati as $row) {
            echo '<option'.(trim(strtolower($val)) == trim(strtolower($row->Name)) ? ' selected="selected"' : '').' value="'.$row->Name.'">'.$row->Name.'</option>'."\n";
        }
    }
}