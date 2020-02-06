<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use KubAT\PhpSimple\HtmlDomParser;
use Fadion\Fixerio\Exchange;
use Fadion\Fixerio\Currency;


class DivisaController extends Controller
{


    function index()
    {

        return response()->json([
            'rates' => [
                'provider_1' => $this->getDiaryOficialFederation(),
                'provider_2' => $this->getFixer(),
                'provider_3' => $this->getBanxico(),
            ]
        ]);


    }

    /*
     *
     */
    function getDiaryOficialFederation()
    {

        $url = 'http://www.banxico.org.mx/tipcamb/tipCamMIAction.do';

        $html = file_get_contents($url);

        $document = HtmlDomParser::str_get_html("<html><body>Hello!</body></html>");
        //$document->find('td');
        $document = HtmlDomParser::str_get_html($html);


        $data = null;
      //  dd($document->find('tr[class=renglonNon]'));
        foreach ($document->find('tr[class=renglonNon]') as $input) {
            $explode = explode(' ', trim($input->plaintext));
            $data = array_pop($explode);
            break;
        }


       // dd($data);
        if(!empty($data)){
            return $this->tranformdDataValues($data, $this->lastUpdate());
        }else{
            return  ['error' => 'Ocurrio un error por favor intentelo mas tarde'];
        }

    }

    /*
     * "provider_1": {
         "value": 20.4722,
         "last_updated": "2018-04-22T18:25:43.511Z"
         },
    */
    function tranformdDataValues($value, $lastUpdate, $params=[]) {
        return [
            'value' => $value,
            'last_updated' => $lastUpdate,
        ];
    }

    function lastUpdate(){
        return str_replace('+00:00', 'Z', gmdate('c'));
    }


/*
 * d197d029c915bdc591a23612542db54b
 */
    function getFixer() {
        // set API Endpoint and API key
        $endpoint = 'latest';
        $access_key = env('BAXICO_KEY');

        // Initialize CURL:
        $ch = curl_init('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        if (curl_errno($ch)) { return ['error' => 'Ocurrio un error por favor intentelo mas tarde']; };
        curl_close($ch);


        // Decode JSON response:
        $exchangeRates = json_decode($json, true);

        // Access the exchange rate values, e.g. GBP:
        //echo $exchangeRates['rates']['MXN'];
        return $this->tranformdDataValues( $exchangeRates['rates']['MXN'], $this->lastUpdate());

    }

    /*
     * e8cfd0f93c0548f8cc1c9bcdd1365d7ddcf78132276925b235f298513c48414b
     *  //https://www.banxico.org.mx/SieAPIRest/service/v1/series/SP68257,SF43718,SF61745
     */
    function getBanxico() {
        // See token info at: https://www.banxico.org.mx/SieAPIRest/service/v1/token
        $token = 'b7d94bad0a979a8c2d7eb80498215d6f61b8a8d992c0af8c24580553f04968f5';

        // Get catalog series from https://www.banxico.org.mx/SieAPIRest/service/v1/doc/catalogoSeries#
        $catalogs = [
            'SF43718', // Tipo de cambio pesos por dólar E.U.A. Interbancario a 48 horas Apertura compra
        ];

        $series = implode($catalogs, ',');

        // Store value in Cache
        $query = 'https://www.banxico.org.mx/SieAPIRest/service/v1/series/'.$series.'/datos/oportuno?token='.$token;


       $json = json_decode(file_get_contents($query), true);
       $exchangeRates = $json['bmx']['series'][0]['datos'][0];

       if(is_array($exchangeRates)){
           return $this->tranformdDataValues($exchangeRates['dato'], $this->lastUpdate());
       }else{
           return  ['error' => 'Ocurrio un error por favor intentelo mas tarde'];
       }

    }

}