<?php
require 'vendor/autoload.php';
		
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RMOE_ExcelExport extends RMOE_Export
{
    protected function _header() {
        parent::_header();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // setting in subclass
        header('Content-Disposition: attachment;filename="order-'.date('Y_m_d_H_i_s').'.xlsx"'); // setting in subclass
    }
	
	public function get_product_type($size, $name) {
		$keyword = array(
			"man" => array(
				"/\\sman('s)?(\\s)?/i", 
				"/^man('s)?(\\s)?/i", 
				"/\\smen('s)?(\\s)?/i", 
				"/^men('s)?(\\s)?/i"),
			"woman" => array(
				"/\\swoman('s)?(\\s)?/i", 
				"/^woman('s)?(\\s)?/i", 
				"/\\swomen('s)?(\\s)?/i", 
				"/^women('s)?(\\s)?/i", 
				"/\\slady(\\s)?/i", 
				"/^lady(\\s)?/i", 
				"/^ladies(\\s)?/i"),
			"kid" => array(
				"/\\skid('s|s)?(\\s)?/i", 
				"/^kid('s|s)?(\\s)?/i", 
				"/\\syouth(\\s)?/i", 
				"/^youth(\\s)?/i", 
				"/\\spreschool(\\s)?/i", 
				"/^preschool(\\s)?/i", 
				"/\\snewborn(\\s)?/i",
				"/^newborn(\\s)?/i"),
		);
		//print_r($keyword);

		$flg = Null;
		$size = strtolower($size); // change $name to lowercase
		foreach ($keyword as $key => $words) {
			foreach ($words as $word) {
				if (preg_match($word, $size) > 0) { // match!
					$flg = $key;
					break;
				}
			}
		}
		if ($flg == "man") {
			return "男装";
		} else if ($flg == "woman") {
			return "女装";
		} else if ($flg == "kid") {
			return "童装";
		}

		$flg = Null;
		$name = strtolower($name); // change $name to lowercase
		foreach ($keyword as $key => $words) {
			foreach ($words as $word) {
				if (preg_match($word, $name) > 0) { // match!
					$flg = $key;
					break;
				}
			}
		}
		if ($flg == "man") {
			return "男装";
		} else if ($flg == "woman") {
			return "女装";
		} else if ($flg == "kid") {
			return "童装";
		} else {
			return "男装";
		}
	}

    public function export($orders) {
        //Create new PHPExcel object
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    
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
        // Add some order
        $delta = 15;
        $start = 1;
        foreach ($orders as $order) {			
			$address_flg = True;
            foreach($order['products'] as $product) {
                $size = 'N/A';
        		$end = $start + $delta - 1;
				$objPHPExcel->getActiveSheet()
                        ->mergeCells('A'.$start.':A'.$end.'')
                        ->setCellValue('A'.$start.'', $order['id'])
    
                        ->setCellValue('B'.(string)($start).'', $product['name'])
                        //->setCellValue('B'.(string)($start+1).'', get_product_type($product['name']).' 数量: '.$product['quantity'].'; '.$options_str)
                        ->setCellValue('B'.(string)($start+1).'', $this->get_product_type($product['size'], $product['name']).' 数量: '.$product['quantity'].'; '.'Size: '.$product['size'].'; '.'Name: '.$product['Input Name'].'; '.'Number: '.$product['Input Number'])
                        ->setCellValue('B'.(string)($start+2).'', $order['notes'])
                        ->mergeCells('B'.(string)($start+3).':B'.$end.'');
						
				if ($address_flg == True) {
                    $objPHPExcel->getActiveSheet()
                            ->setCellValue('C'.(string)($start + 3).'', $order['name'])
                            ->setCellValue('C'.(string)($start + 4).'', $order['address'])
                            ->setCellValue('C'.(string)($start + 5).'', '')
                            ->setCellValue('C'.(string)($start + 6).'', $order['city']. ', ' .$order['province']. ' ' .$order['post'])
                            ->setCellValue('C'.(string)($start + 7).'', $order['country'])
                            ->setCellValue('C'.(string)($start + 8).'', $order['tel']);
                    $address_flg = False;
                }

						
                $objPHPExcel->getActiveSheet()
                        //->setCellValue('D'.(string)($start + 1).'', 'Qty: '.$product['quantity'])
                        //->setCellValue('D'.(string)($start + 2).'', 'SKU: '.$product['sku'])
                        ->mergeCells('D'.(string)($start).':D'.$end.'')
                        ->setCellValue('D'.(string)($start).'', $order['shipping_method'])
                        // merge E, F, G
                        ->mergeCells('E'.(string)($start).':E'.$end.'')
                        ->mergeCells('F'.(string)($start).':F'.$end.'')
                        ->mergeCells('G'.(string)($start).':G'.$end.'')
                        ->getStyle('C'.(string)($start + 5))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                // add picture
                $imagePath = $product['image'];
                
                $objDrawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $objDrawing->setPath($imagePath, true);
                $objDrawing->setCoordinates('B'.(string)($start+4));
                $objDrawing->setHeight(200);
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                $start = $start + $delta;
            }
        }
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Orders');
		$this->_header();

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");
        $objWriter->save('php://output');
    }
}
