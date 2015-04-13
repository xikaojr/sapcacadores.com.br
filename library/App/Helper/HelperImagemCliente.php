<?php

class Zend_View_Helper_HelperImagemCliente extends Zend_View_Helper_Abstract {

    public function helperImagemCliente($extra = 'P', $cc = null, $negativa = null, $centro_custo = null, $sistema = null) {
        
        if (!empty($sistema) && $sistema) {
            $imagem = '/images/icongresso/' . $sistema . '_logo.png';
        } else {
            $imagem = '/images/default/cliente-sem-logo.jpg';
        }


        if (!empty($cc)) {
            $cc = "_{$cc}";
        }
        if (!empty($negativa)) {
            $negativa = "_{$negativa}";
        }

        if (is_file(PUBLIC_PATH . "images/clientes/" . CLIENTE . '/' . $centro_custo . "/logo" . $extra . ".jpg")) {
            $imagem = "/images/clientes/" . CLIENTE . $centro_custo . '/' . "/logo" . $extra . ".jpg";
        }

        if (is_file(PUBLIC_PATH . "images/clientes/" . CLIENTE . '/' . $centro_custo . "/logo" . $extra . ".png")) {
            $imagem = "/images/clientes/" . CLIENTE . '/' . $centro_custo . "/logo" . $extra . ".png";
        }

        if (is_file(PUBLIC_PATH . "images/clientes/" . CLIENTE . '/' . $centro_custo . "/logo" . $extra . "{$cc}.jpg")) {
            $imagem = "/images/clientes/" . CLIENTE . '/' . $centro_custo . "/logo" . $extra . "{$cc}.jpg";
        }

        if (is_file(PUBLIC_PATH . "images/clientes/" . CLIENTE . '/' . $centro_custo . "/logo" . $extra . "{$cc}.png")) {
            $imagem = "/images/clientes/" . CLIENTE . '/' . $centro_custo . "/logo" . $extra . "{$cc}.png";
        }

        if (!empty($negativa) && is_file(PUBLIC_PATH . "images/clientes/" . CLIENTE . '/' . "/logo" . $extra . $negativa . ".png")) {
            $imagem = "/images/clientes/" . CLIENTE . '/' . $centro_custo . "/logo" . $extra . $negativa . ".png";
        }

        return $imagem;
    }

}
