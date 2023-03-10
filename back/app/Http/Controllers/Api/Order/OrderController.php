<?php

namespace App\Http\Controllers\Api\Order;

use App\Facades\Market;
use App\Http\Adapters\Order\OrderRequestAdapter;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FormFields\ExtendedBreadFormFieldsController;
use App\Http\Requests\Order\SaveAddressesRequest;
use App\Http\Requests\Order\ToProcessedRequest;
use App\Models\Order\Order;
use App\Models\Order\OrderAddress;
use App\Models\Order\OrderDiscount;
use App\Models\Order\OrderProduct;
use App\Models\Order\OrderRecipient;
use App\Models\Order\OrderRecipientAddress;
use App\Models\Order\OrderStatus;
use App\Models\Parcel\Parcel;
use App\Models\Product\Product;
use App\Models\Shop\Shop;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use TCG\Voyager\Facades\Voyager;

class OrderController extends Controller
{


    private function addProduct(Request $request, $order_id)
    {
        foreach ($request->products as $productOrder) {

            $product_local = Product::where('id', $productOrder['id'])->first();
            $order_local = Order::where('id', $order_id)->first();
            if ($product_local) {
                $order_product = OrderProduct::where('order_id', $order_local->id)
                    ->where('product_id', $product_local->id)
                    ->where('local_code', $product_local->local_code)->first();
                if ($order_product) {

                } else {
                    $order_product = OrderProduct::updateOrCreate(
                        ['order_id' => $order_local->id, 'product_id' => $product_local->id, 'local_code' => $product_local->local_code],
                        array_merge([
                            'order_id' => $order_local->id,
                            'quantity' => $productOrder['quantity'],
                            'price_from_market' => null,
                        ], OrderProduct::copyFromProduct($product_local))
                    );
                }
                if ($order_product) {
                    $order_local->calculateSum()->save();
                }
            }
        }
        return 'ok';

    }

    private function saveAddresses(Request $request, $order_id)
    {
        $order_local = Order::where('id', $order_id)->first();
        $recipient = $order_local->recipient;
        $request_prone = phone_numeral_format($request->phone);
        if (empty($request->first_name) || empty($request->last_name)) {
            return response("Введіть прізвище та імʼя", 404);
        }



        if ($request->email == '_@_._') {
            return response("Введіть email", 404);
        }
        $name = trim(implode(" ", [$request->last_name, $request->first_name, $request->middle_name]));
        if (!$recipient ||
            ($recipient && $request_prone != $recipient->phone) ||
            ($recipient && $name != $recipient->name) ||
            ($recipient && $request->email != $recipient->email)) {
            $recipient = OrderRecipient::updateOrCreate(
                ['phone' => phone_numeral_format($request->phone)],
                [
                    'name' => $name,
                    'email' => $request->email,
                    'phone' => phone_numeral_format($request->phone),
                ]
            );
            $order_local->phone = $recipient->phone;
            $order_local->email = $recipient->email;
            $order_local->save();
            $order_local->setRelation('recipient', $recipient);
        }
        $recipient_city = json_decode($request->address_recipient['city']);
        if (empty($recipient_city)) {
            return response("Введите название города получателя", 404);
        }
        $recipient_street = json_decode($request->address_recipient['street']);
        if (empty($recipient_street)) {
            return response("Введите название улицы получателя", 404);
        }
        $recipient_address = OrderRecipientAddress::updateOrCreate([
            'phone' => phone_numeral_format($recipient->phone),
            'street_uuid' => $recipient_street->uuid,
            'house' => $request->address_recipient['house'],
            'flat' => $request->address_recipient['flat'],
            'recipient_id' => $recipient->id,
            'city' => $recipient_city->city,
            'city_uuid' => $recipient_city->uuid,
            'street' => $recipient_street->street,
        ], [
            'recipient_id' => $recipient->id,
            'phone' => $recipient->phone,
            'city' => $recipient_city->city,
            'city_uuid' => $recipient_city->uuid,
            'street' => $recipient_street->street,
            'house' => $request->address_recipient['house'],
            'flat' => $request->address_recipient['flat'],
            'street_uuid' => $recipient_street->uuid,
        ]);

        $delivery_type = $request->address_delivery_type;
        $warehouse_uuid = null;
        $street_uuid = null;
        $delivery_cost = $request->address_delivery[$delivery_type]['delivery_cost'];
        if ($delivery_type == "meest_courier") {
            $city = $recipient_city;
            $street = $recipient_street;
            if ($street) {
                $street_uuid = $street->uuid;
                $add_data = [
                    'street' => $street->street,
                    'street_uuid' => $street_uuid,
                    'house' => $request->address_recipient['house'],
                    'flat' => $request->address_recipient['flat'],
                ];
            } else {
                return response("Введите название улицы для доставки", 404);
            }
        } else {
            $city = json_decode($request->address_delivery[$delivery_type]['city']);
            $warehouse = json_decode($request->address_delivery[$delivery_type]['warehouse']);
            if ($warehouse) {
                $warehouse_uuid = $warehouse->uuid;
                $add_data = [
                    'warehouse_category' => $warehouse->warehouse_category,
                    'warehouse_uuid' => $warehouse_uuid,
                    'warehouse_number' => $warehouse->warehouse_number,
                    'warehouse_short' => $warehouse->warehouse_short,
                    'working_time' => $warehouse->schedule,
                    'is_pos_terminal' => $warehouse->is_pos_terminal,
                    'is_work' => $warehouse->is_work,
                    'max_weight' => $warehouse->max_weight
                ];
            } else {
                return response("Введите отделение для доставки", 404);
            }
        }

        $discount_delivery = null;
        $standard_delivery_cost =  343;
        if ($delivery_cost < $standard_delivery_cost) {
            $discount_delivery = $standard_delivery_cost - $delivery_cost;
            $order_discount = OrderDiscount::updateOrCreate([
                'order_id' => $order_local->id,
                'discount_value' => $discount_delivery,
                'type_discount' => 'delivery',
                'user_id' => Auth::id()
            ], [
                'order_id' => $order_local->id,
                'discount_value' => $discount_delivery,
                'type_discount' => 'delivery',
                'user_id' => Auth::id()
            ]);
        }

        if ($city) {
            $order_address_data = array_merge([
                'order_id' => $order_local->id,
                'recipient_id' => $recipient->id,
                'delivery_type' => $delivery_type,
                'city' => $city->city,
                'city_uuid' => $city->uuid,
                'delivery_cost' => $delivery_cost,
            ], $add_data);
            $order_address = OrderAddress::updateOrCreate($order_address_data, $order_address_data);
            $order_local->delivery_cost = $order_address->delivery_cost;
            $order_local->discount_delivery = $discount_delivery;
            $order_local->save();
        } else {
            return response("Введите название города для доставки", 404);
        }

//        $order_local = Order::with('recipient','address','products')->where('id', $order_id)->first();
////
//        dump($order_local);
        return $order_local;
    }


    public function create(Request $request)
    {

//                $test = <<<TEXT
//        {"recipient":{"phone":"+38(067)978-67-44","email":"artemgiant@gmail.com","first_name":"Артем","last_name":"Поточний","middle_name":"Олександрович","city":{"id":15244,"text":"Житомир, Житомир, Житомирська","uuid":"56bdd203-749b-11df-b112-00215aee3ebe","city":"Житомир"},"street":{"id":29547,"text":"10-Й Дачний (провулок)","uuid":"f5ed462d-e0d2-11df-9b37-00215aee3ebe","street":"10-Й Дачний"},"house":"1","flat":"1"},"address_delivery":{"delivery_type":"nova_poshta","city":{"id":3545,"text":"Житомир, Житомир, Житомирська","uuid":"e717a3d0-4b33-11e4-ab6d-005056801329","city":"Житомир"},"warehouse":{"id":16552,"text":"Відділення № 3, Житомир, Перемоги, 10","max_weight":"0.00","warehouse_short":"Житомир, Перемоги, 10","is_pos_terminal":"1","is_work":"1","warehouse_category":"warehouse","uuid":"40498331-e1c2-11e3-8c4a-0050568002cf","warehouse_number":"3","schedule":"{\"reception\":{\"Monday\":\"11:30-20:00\",\"Tuesday\":\"11:30-20:00\",\"Wednesday\":\"11:30-20:00\",\"Thursday\":\"11:30-20:00\",\"Friday\":\"11:30-20:00\",\"Saturday\":\"10:00-17:00\",\"Sunday\":\"12:00-17:00\"},\"delivery\":{\"Monday\":\"09:00-18:00\",\"Tuesday\":\"09:00-18:00\",\"Wednesday\":\"09:00-18:00\",\"Thursday\":\"09:00-18:00\",\"Friday\":\"09:00-18:00\",\"Saturday\":\"09:00-15:00\",\"Sunday\":\"11:00-15:00\"},\"schedule\":{\"Monday\":\"10:00-18:00\",\"Tuesday\":\"08:00-20:00\",\"Wednesday\":\"08:00-20:00\",\"Thursday\":\"08:00-20:00\",\"Friday\":\"08:00-20:00\",\"Saturday\":\"09:00-18:00\",\"Sunday\":\"10:00-18:00\"}}"},"street":null,"house":null,"flat":null},"products":[{"id":302,"ean":"8595683501705","name":"ARMODD Candywatch Premium 2 stříbrná s růžovým řemínkem","price":"2850","description_short":null,"description_long":null,"in_stock":"100","weight_kg":0.175,"images":[{"alt":"","name":"products/November2021/VGEpFOjtgTJC01EBMGFZ.png","title":""},{"alt":"","name":"products/November2021/DrniusoZEBEk6OO4j2YJ.png","title":""},{"alt":"","name":"products/November2021/QkFcRe4KwZeeFROzA9Ux.png","title":""},{"alt":"","name":"products/November2021/D4nMQJ0lDCpVMpdK1fs4.png","title":""},{"alt":"","name":"products/November2021/138Vzr811lNHrAnxe1c5.png","title":""},{"alt":"","name":"products/November2021/Q7QhiQtm8FnMTsb3IfC7.png","title":""},{"alt":"","name":"products/November2021/dS6cIcqmIyr6XrN3UuPZ.png","title":""},{"alt":"","name":"products/November2021/GD57opiOm6xnMrCXiqNa.png","title":""},{"alt":"","name":"products/November2021/uv278oX18LDruELeI9Op.png","title":""}],"image_main":{"alt":"","name":"products/November2021/VGEpFOjtgTJC01EBMGFZ.png","title":""},"quantity":3}],"payment_type":"postpaid","comment":"Це тестове замовлення ","deliveryConst":0,"sum_to_pay":8550,"warehouse":{}}
//TEXT;
//        $request->replace(json_decode($test,true));


//обвления количества товара на складе
foreach($request->all()['products'] as $product){
    $productModel = Product::find($product['id']);
    $productModel->in_stock = $productModel->in_stock - $product['quantity'];
    $productModel->save();
}


      $paymentType =  DB::connection('ub2c')
                        ->table('payment_type')
                        ->where('slug',$request->payment_type)
                        ->first();
      $params =  OrderRequestAdapter::run($request->all());
      $request->replace($params);



        $shop = Shop::where('alias','shop')->first();
        $prefixOrder = json_decode($shop->config)->order_prefix;



        $shop_order_id = Order::where('shop_id', $shop->id)->max('shop_order_id');
        $shop_order_id += 1;

      $order = new Order([
                'shop_id' => $shop->id,
                'shop_order_id' => $shop_order_id,
                'order_date' => Carbon::now(),
                'status_id' => '1',
                'payment_type' => $paymentType->id,
                'comment' => $request->comment,

            ]);
        $order->save();


        $order->order_number = $prefixOrder . $order->id . "." . ((int)$shop_order_id);

        $order->save();

        $this->addProduct($request,$order->id);

        $this->saveAddresses($request,$order->id);

//return 'ok';

//        return redirect()->route('voyager.'.$dataType->slug.'.show', ['id'=>$order->id]);
    }



}
