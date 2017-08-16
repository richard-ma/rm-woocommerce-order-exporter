<?php

abstract class RMOE_Export
{
    protected function _header() {
        //header('Content-Type: text/csv'); // setting in subclass
        //header('Content-Disposition: attachment;filename="address-list-'.date('Y_m_d_H_i_s').'.csv"'); // setting in subclass
        header('Cache-Control: max-age=0');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Pragma: public'); // HTTP/1.0
    }

    abstract public function export($data);
}
