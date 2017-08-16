<?php

class RMOE_CsvExport extends RMOE_Export
{
    protected function _header() {
        parent::_header();
        header('Content-Type: text/csv'); // setting in subclass
        header('Content-Disposition: attachment;filename="address-list-'.date('Y_m_d_H_i_s').'.csv"'); // setting in subclass
    }

    public function export($data) {
        $output = "OrderNo, Name, Address, City, Province, Post, Country, Tel" . PHP_EOL;

        foreach($data as $item) {
            $output .= $item['id'].',';
            $output .= $item['name'].',';
            $output .= $item['address'].',';
            $output .= $item['city'].',';
            $output .= $item['province'].',';
            $output .= $item['post'].',';
            $output .= $item['country'].',';
            $output .= $item['tel'].',';
            $output .= PHP_EOL;
        }

        $this->_header();
        echo $output;
    }
}
