<?php


namespace App\Http\Adapters\Order;


class OrderRequestAdapter
{
    public static function run(array $requestData)
    {

        $params = [
            'address_delivery_type' => $requestData['address_delivery']['delivery_type'],
            'email' => $requestData['recipient']['email'],
            'phone' => $requestData['recipient']['phone'],
            'first_name' => $requestData['recipient']['first_name'],
            'last_name' => $requestData['recipient']['last_name'],
            'middle_name' => $requestData['recipient']['middle_name'],
            'comment' => $requestData['comment'],
            'address_recipient' => [
                'city' => json_encode($requestData['recipient']['city'], JSON_UNESCAPED_UNICODE),
                'street' => json_encode($requestData['recipient']['street'], JSON_UNESCAPED_UNICODE),
                'house' => $requestData['recipient']['house'],
                'flat' => $requestData['recipient']['flat'],
            ],
            'address_delivery' => [
                "{$requestData['address_delivery']['delivery_type']}" => [
                    'city' => json_encode($requestData['address_delivery']['city'], JSON_UNESCAPED_UNICODE),
                    'warehouse' => json_encode($requestData['address_delivery']['warehouse'], JSON_UNESCAPED_UNICODE),
                    'delivery_cost'=> $requestData['delivery_cost']
                ]
            ],
            'products'=>$requestData['products'],

            'order_discount'=>$requestData['order_discount'],
            'discount'=>$requestData['discount'],
            'discount_delivery'=>$requestData['discount_delivery'],
            'delivery_cost'=>$requestData['delivery_cost'],
            'standard_delivery_cost'=>$requestData['standard_delivery_cost'],
        ];

        return $params;

    }
}
