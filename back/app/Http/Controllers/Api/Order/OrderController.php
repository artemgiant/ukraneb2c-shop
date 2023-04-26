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
use App\Models\Order\PromoCode;
use App\Models\Parcel\DeliveryCost;
use App\Models\Parcel\Parcel;
use App\Models\Product\Product;
use App\Models\Shop\Shop;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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

        $order_total_weight_kg = $order_local->products->sum('total_weight_kg');
        $standard_delivery_cost = DeliveryCost::whereRaw('? between min_weight_kg and max_weight_kg', [$order_total_weight_kg])
            ->where('delivery_type', $delivery_type)
            ->first()->cost_uah;
        $discount_delivery = null;
        if ($delivery_cost < $standard_delivery_cost) {
            $discount_delivery = $standard_delivery_cost - $delivery_cost;
            $order_discount = OrderDiscount::updateOrCreate([
                'order_id' => $order_local->id,
                'discount_value' => $discount_delivery,
                'type_discount' => 'delivery',
            ], [
                'order_id' => $order_local->id,
                'discount_value' => $discount_delivery,
                'type_discount' => 'delivery',
                'user_id' => Auth::id()
            ]);
        } else {
            OrderDiscount::where('order_id', $order_local->id)->where('type_discount', 'delivery')->whereNull('promo_code_id')->delete();
        }

        if ($city) {
            OrderAddress::where('order_id', $order_local->id)
                ->where('delivery_type', $delivery_type)
                ->where('city', $city->city)
                ->where('city_uuid', $city->uuid)
                ->delete();
            $order_address_data = array_merge([
                'order_id' => $order_local->id,
                'recipient_id' => $recipient->id,
                'delivery_type' => $delivery_type,
                'city' => $city->city,
                'city_uuid' => $city->uuid,
                'delivery_cost' => $delivery_cost,
            ], $add_data);
            $order_address = OrderAddress::create($order_address_data);
            $order_local->delivery_cost = $order_address->delivery_cost;
            $order_local->discount_delivery = $discount_delivery;
            $order_local->standard_delivery_cost = $standard_delivery_cost;
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

        $validator = Validator::make($request->all(),[
            'recipient.phone'=>array('required', "regex:%^\+38\(0[0-9]{2}\)[0-9]{3}-[0-9]{2}-[0-9]{2}$%i"),
            'recipient.email'=>array('required', "email"),
            'recipient.first_name'=>array('required','min:1'),
            'recipient.last_name'=>array('required','min:1'),
            'recipient.middle_name'=>array('required','min:1'),
            'recipient.city.id'=>array('required'),
            'recipient.street.id'=>array('required'),
            'payment_type'=>array('required'),
            'delivery_cost'=>array('required'),
            'sum_to_pay'=>array('required'),
//            'test'=>array('required'),

        ]);

        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()], 422);
        }

//        dump($validator->errors(),$request->all());
//

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

      $shop = Shop::where('alias',$request->get('shop-alias'))->first();
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

                'discount' => $request->discount,
                'discount_delivery' => $request->discount_delivery,
                'delivery_cost' => $request->delivery_cost,
                'standard_delivery_cost' => $request->standard_delivery_cost,
            ]);
        $order->save();

//        збереження купону
        $orderDiscount = new OrderDiscount($request->order_discount);
        $orderDiscount->order_id = $order->id;
        $orderDiscount->save();

        $order->order_number = $prefixOrder . $order->id . "." . ((int)$shop_order_id);
        $order->save();

        $this->addProduct($request,$order->id);
//
        $this->saveAddresses($request,$order->id);

        return response()->json( ['message' => __('voyager::generic.successfully_added_new'), 'alert-type' => 'success']);
    }

    public function prices(Request $request){

        $totalWeightKg = $request->total_weight_kg;

        $standardDeliveryCost = DeliveryCost::whereRaw('? between min_weight_kg and max_weight_kg', [$totalWeightKg])
            ->where('delivery_type', $request->address_delivery['delivery_type'])
            ->first()->cost_uah;

        $order = new Order(
            [
                'total_sum_product' => $request->total_sum_product,
                'discount' => 0,
                'standard_delivery_cost' => $standardDeliveryCost,
                'discount_delivery' => 0,
                'delivery_cost' => $standardDeliveryCost,
            ]
        );

        $message = [];
        $order_discount = null;
        if (!empty($request->promo_code)) {
            list($order_discount, $message) = $this->checkPromoCode($request, $order);

            $order = $order->calculateDiscount($order_discount);
        }

        $data = [
            'standard_delivery_cost' => $standardDeliveryCost,
            'sum_to_pay' => $order->getSumToPayAttribute(),
            'discount' => $order->discount,
            'delivery_cost' => $order->delivery_cost,
            'order_discount' => $order_discount
        ];

        return response()->json(['data'=>$data,'message'=>$message]);

    }


    public function checkPromoCode(Request $request,Order $order_local)
    {
        if ($order_local) {

            $order_discount = new OrderDiscount();

            $promo_code = PromoCode::where('code', $request->promo_code)->active()->first();

            if (!$promo_code) {
                return [$order_discount, ['alert-type' => 'error', 'message' => "Промокод не знайдено" ]];
            }

            if ($promo_code->minimum_cart && $promo_code->minimum_cart >= $order_local->total_sum_product) {
                return [$order_discount, ['alert-type' => 'error', 'message' => "Мінімальна сума покупки " . $promo_code->minimum_cart]];
            }

            if ($promo_code->suppliers->first()) {
                $promo_suppliers = $promo_code->suppliers->pluck('id')->toArray();
                $count = $order_local->products->whereIn('supplier_id', $promo_suppliers)->count();
                if (!$count) {
                    return [$order_discount, ['alert-type' => 'error', 'message' => 'Немає товарів до яких застосовується промокод']];
                }
            }

            $data = [];
            switch ($promo_code->type) {
                case "amount":
                    $data = [
                        'discount_value' => $promo_code->discount,
                        'type_discount' => 'promo_code',
                    ];
                    break;
                case "percent":
                    $data = [
                        'discount_percent' => $promo_code->discount,
                        'type_discount' => 'promo_code',
                    ];
                    break;
                case "free_delivery":
                    $data = [
                        'discount_value' => $order_local->standard_delivery_cost,
                        'type_discount' => 'delivery',
                    ];
                    break;
            }

            $promo_code->views =  $promo_code->views++;
            $promo_code->save();

            $order_discount = new OrderDiscount(array_merge([
                'order_id' => $order_local->id,
                'promo_code_id' => $promo_code->id,
            ], $data));


            return [$order_discount, ['alert-type' => 'success', 'message' => 'Промокод внесено успішно']];

        }
    }
}
