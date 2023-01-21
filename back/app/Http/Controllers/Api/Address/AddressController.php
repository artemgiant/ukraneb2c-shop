<?php

namespace App\Http\Controllers\Api\Address;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    public function getDeliveryType($delivery_type)
    {
        $format = ["meest_courier" => "meest_courrier"];
        if (!empty($format[$delivery_type])) {
            return $format[$delivery_type];
        }
        return $delivery_type;
    }

    public function searchCity(Request $request)
    {
        $req_arr = [
            'method' => 'POST',
            'uri' => "https://pre_meest.shipoko.com/api/addresses/search/cities",
            'options' => [
                'query' => [
                    'q' => $request->q,
                    'uuid' => $request->uuid,
                    'delivery_type' => $this->getDeliveryType($request->delivery_type),
                ]
            ]
        ];

        $client = new Client();
        $res = $client->request($req_arr['method'], $req_arr['uri'], $req_arr['options']);
        $response = $res->getBody()->getContents();
        return $response;
    }

    public function searchStreet(Request $request)
    {
        $req_arr = [
            'method' => 'POST',
            'uri' => "https://pre_meest.shipoko.com/api/addresses/search/streets",
            'options' => [
                'query' => [
                    'q' => $request->q,
                    'uuid' => $request->uuid,
                    'delivery_type' => $this->getDeliveryType($request->delivery_type),
                    'id' => $request->id,
                ]
            ]
        ];

        $client = new Client();
        $res = $client->request($req_arr['method'], $req_arr['uri'], $req_arr['options']);
        $response = $res->getBody()->getContents();
        return $response;
    }

    public function searchWarehouse(Request $request)
    {
        $req_arr = [
            'method' => 'POST',
            'uri' => "https://pre_meest.shipoko.com/api/addresses/search/warehouses",
            'options' => [
                'query' => [
                    'q' => $request->q,
                    'uuid' => $request->uuid,
                    'delivery_type' => $this->getDeliveryType($request->delivery_type),
                    'id' => $request->id,
                ]
            ]
        ];

        $client = new Client();
        $res = $client->request($req_arr['method'], $req_arr['uri'], $req_arr['options']);
        $response = $res->getBody()->getContents();
        return $response;

//        'warehouse_category' => Address::warehouseCategory($dataRequest['delivery_type']),
//                    'warehouse_number'   => $warehouse->number_of_warehouse,
//                    'warehouse_short'    => $warehouse->translations->first()->value,
//                    'working_time'       => $warehouse->schedule,
//                    'is_pos_terminal'    => $warehouse->is_pos_teminal,
//                    'is_work'            =>  $warehouse->warehouse_work,
//                    'max_weight'         => $warehouse->total_max_weight_allowed,
    }
}
