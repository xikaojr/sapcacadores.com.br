<?php

class App_Formate {

    public static function data($data) {
        $data = explode(' ', $data);
        $data = explode('-', $data[0]);
        $date = array_reverse($data);
        return implode('/', $date);
    }

    public static function hora($data) {
        $data = explode(' ', $data);
        if (isset($data[1])) {
            $data = explode('-', $data[1]);
        } else {
            $data = explode('-', $data[0]);
        }
        $date = array_reverse($data);
        return implode('/', $date);
    }

    public static function nota($nota) {
        return number_format($nota, 2, ',', '.');
    }

}
