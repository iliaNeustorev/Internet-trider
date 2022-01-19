<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class BasketController extends Controller
{
    public function add ()
    {
        $id = request('id');
        $products = session('products', []);

        if (isset($products[$id])) {
            $products[$id] = $products[$id] + 1;
        }else {
            $products[$id] = 1;
        }
        session()->put('products', $products);
        session()->save();
        return back();
    }

    public function remove()
    {
        $id = request('id');
        $products = session('products', []);

        try {
            if ($products[$id] == 1) {
                unset($products[$id]);
            } else {
                $products[$id] -= 1;
            }
        } catch (Exception $e) {
            Log::info("Нажали на кнопку '-' когда товара не было в корзине {$id}");
        }
        session()->put('products', $products);
        session()->save();
        return back();
    }

    public function index () 
    {
        $products = session('products');
        $main_address = null;
        $email = null;
        $name = null;
        $user = Auth::user();
        if ($user) {
            $main_address = Address::where([
                'user_id' => $user->id,
                'main' => true
                ])->first();
            $email = $user->email;
            $name = $user->name;
        }
        $basket_products = collect($products)->map( function ($quantity, $id) {
            $product = Product::find($id);
            return [
                'title' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        });
       
        $sum_order =  $basket_products->map( function ($product) {
            return $product['price'] * $product['quantity'];     
        })->sum();
       
        $date = [
            'products' => $basket_products,
            'title' => 'Корзина',
            'sum_order' =>  $sum_order,
            'main_address' =>  $main_address,
            'email' => $email,
            'name' => $name, 
        ];

        return view('basket', $date);

    }

    public function create_order()
    {
        $user = Auth::user();
        if(!$user) {
            $password = $this->generate_password(4,8);
            $name = request('name');
            $email = request('email');
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            Address::create([
                'user_id' => $user->id,
                'address' => request('address'),
                'main' => 1
            ]);
            Auth::loginUsingId($user->id);
        }

        $address = Address::where([
            'user_id' => $user->id,
            'main' => true,
            ])->first();

        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $address->id,
        ]);

        collect(session('products'))->each(function($quantity, $id) use ($order){
            $product = Product::find($id);
            $order->products()->attach($product, [
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        });
        session()->forget('products');
        return back();
    }

    protected function generate_password ($type, $lenght) 
    {   
        switch ($type) {
            case 1:
                $input = '123456790';
                break;

            case 2:
                $input =  $input = '0123456790qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
                break;

            case 3:
                $input = '0123456790qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM!@#$%';
                break;
            case 4:
                $input = '0123456790qwertyuiopasdfghjklzxcvbnm';
                break;

            default: {
                $input = null;
            }
        }

        return $input ? substr(str_shuffle($input), 0, $lenght): null;
    }
}
