<?php

/*
 * Explode order IDs
 *
 * @param ids: string like "3, 25-30 , 88-84"
 *
 * @return list contains ids [3, 25, 26, 27, 28, 29, 30]
 */
function rm_explode_ids($ids_string) {
    $orders = explode(',', $ids_string);

    foreach ($orders as $key => $id) {
        # trim space for id
        $id = trim($id);
        # remove m-n element
        unset($orders[$key]);

        if (strpos($id, '-') != false) {
            # process m-n format

            # get m & n as $start & $end
            list($start, $end) = explode('-', $id);
            # $end is less than $start then swap them
            if ($end < $start) list($start, $end) = array($end, $start);
            # push m-n items to result
            for ($i = $start; $i <= $end; $i++) {
                array_push($orders, (int)$i);
            }
        } else {
            # process m format
            array_push($orders, (int)$id);
        }
    }

    # sort ids
    sort($orders);

    return $orders;
}
# unit test
//var_dump(rm_explode_ids("3, 25-30 , 88-84"));
//die();
