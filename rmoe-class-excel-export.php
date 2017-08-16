<?php

class RMOE_ExcelExport extends RMOE_Export
{
    protected function _header() {
        parent::_header();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // setting in subclass
        header('Content-Disposition: attachment;filename="address-list-'.date('Y_m_d_H_i_s').'.xlsx"'); // setting in subclass
    }

    public function export($data) {

    	require_once( plugin_dir_path(__FILE__) . "PHPExcel/Classes/PHPExcel.php");

        //Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
                    
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Richard Ma")
                ->setLastModifiedBy("Richard Ma")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
                                                                                                                                                                                                                                                                       
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        // Add some data
        $delta = 6;
        $start = 1;
        foreach ($data as $order) {
            foreach($order['products'] as $product) {
                $size = 'N/A';
        		$end = $start + $delta - 1;
                $objPHPExcel->getActiveSheet()
                        ->mergeCells('A'.$start.':A'.$end.'')
                        ->setCellValue('A'.$start.'', $order['id'])
    
                        ->setCellValue('B'.$start.'', $order['size'])
                        ->mergeCells('B'.(string)($start+1).':B'.$end.'')
                        ->setCellValue('C'.$start.'', $order['name'])
                        ->setCellValue('C'.(string)($start + 1).'', $order['address_1'])
                        ->setCellValue('C'.(string)($start + 2).'', $order['address_2'])
                        ->setCellValue('C'.(string)($start + 3).'', $order['city']. ', ' .$order['province']. ' ' .$order['post'])
                        ->setCellValue('C'.(string)($start + 4).'', $order['country'])
                        ->setCellValue('C'.(string)($start + 5).'', $order['tel'])
                        ->setCellValue('D'.$start.'', $product['name'])
                        ->setCellValue('D'.(string)($start + 1).'', 'Qty: '.$product['quantity'])
                        ->setCellValue('D'.(string)($start + 2).'', 'SKU: '.$product['sku'])
                        ->getStyle('C'.(string)($start + 5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                // add picture
                $imagePath = $product['image'];
                
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setPath($imagePath);
                $objDrawing->setCoordinates('B'.(string)($start+1));
                $objDrawing->setHeight(80);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                $start = $start + $delta;
            }
        }
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Orders');

        $this->_header();

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
}
