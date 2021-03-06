<?php

use App\Events\CategoriesExportFinishEvents;
use App\Models\Category as ModelsCategory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Inspiring;
use App\Http\Controllers\Category;
use App\Models\Address;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/
Artisan::command('CreateRoleUsers', function() {
    // collect(['Admin', 'Manager', 'Customer'])->each(function ($role, $idx){
    //     $role = new Role([
    //         'name' => $role
    //     ]);
    //     $role->save();
    // });
    $user = User::find(1);
    // collect(['Admin', 'Manager', 'Customer'])->each(function ($name, $idx) use ($user)
    //     {
    //        $role = Role::where('name', $name)->first();
    //        $user->roles()->attach($role);
    //     });
    $user->roles->each(function ($role) {
        dump($role->pivot->toArray());
    });
   
});

Artisan::command('parseEkatalog', function() {
    
    function Curl ($url) {
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
        $data = curl_exec($curl_handle);
        curl_close($curl_handle);
        return $data;
    }
    

    $url = 'https://www.e-katalog.ru/ek-list.php?katalog_=902&search_=%D1%88%D0%BA%D0%B0%D1%84';

    $data = Curl($url);

    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $data);
    $xpath = new DOMXPath($dom);

    $h1 = $xpath->query("//h1[@class ='t2']");
    if ($h1->length == 1) {
        $title = explode(' ',$h1[0]->nodeValue);
        $products_count = $title[count($title) - 2];
    }
    
    $divs = $xpath->query("//div[@class='tile-wrapper']");

    $pages = ceil($products_count / $divs->length);
    // foreach ($divs as $div) {
    //    $as = $xpath->query("descendant::div[@class = 'tile-name']", $div);
    //    if ($as->length == 1) {
    //        $name = $as[0]->nodeValue;
    //     //    dump($name);
    //    }
    // }
    $products = [];

    for ($i=0; $i < $pages; $i++) { 
        $page = "$url&page_=$i";
        $data = Curl($page);
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $data);
        $xpath = new DOMXPath($dom);
        $divs = $xpath->query("//div[@class='tile-wrapper']");
        foreach ($divs as $div) {
             
            $as = $xpath->query("descendant::div[@class = 'tile-name']", $div);
            if ($as->length == 1) {
                $name = $as[0]->nodeValue;
            }

            $divs_price = $xpath->query("descendant::div[@class='tile-price']", $div);
           

            if ($divs_price->length == 1) {
                foreach ($divs_price[0]->childNodes as $child) {
                    if ($child->nodeName == 'a') {
                       $price = $child->nodeValue;
                    }
                }
            }
            
            $products[] = [
                'name' => $name,
                'price' => $price
            ];
        }
        
    }
    dd($products);
   
});

Artisan::command('import_categories', function(){
    $file_name ='categories.csv';
    $file = fopen($file_name, 'r');

    $carbon = new Carbon();

    $time = $carbon->now()->toDateTimeString();

    $i = 0;
    $insert = [];
    while($data = fgetcsv($file, 1000, ';'))
    {
       if($i++ == 0) continue;
       $insert[] = [
           'name' => $data[0],
           'description' => $data[1],
           'picture' => $data[2],
           'created_at' => $time,
           'updated_at' => $time
       ];
    }
     ModelsCategory::insert($insert);
});

Artisan::command('del_cat', function() {
    ModelsCategory::where('id', '>', '2')->delete();
});

Artisan::command('test', function(){
    //  ???????????????? ???????????? ?? ??????????????
    //  $kitchen = new Product();
    //  $kitchen->name = '????????????';
    //  $kitchen->description = '?????????????????? ??????????';
    //  $kitchen->picture = '1.jpeg';
    //  $kitchen->price = 15000;
    //  $kitchen->category_id = 1;
    //  $kitchen->save();
 
     //$kitchen = ModelsCategory::find(1); //?????????? ?????????????????? ???? id
    //  $categories =  ModelsCategory::get(); // ?????????????????? ???????? ?????????????? ???? ??????????????
    //  dd($categories);
     //$categories = ModelsCategory::where('name', 'like', '??????%')->first();
     
     // ModelsCategory::create([//???????????????? ????????????
     //     'name' => '??????????',
     //     'description' => '?????????? ???? ?????????? ???????? ?? ????????',
     //     'picture' => '1.jpeg',
     // ]);
 
     // $cupboard = ModelsCategory::firstOrNew([
     //     'name' => '??????????2',
     //     'description' => '???????????? ??????????',
     //     'picture' => '1.jpeg',
     // ]);
    //  ModelsCategory::where('id','=', '23')->delete(); // ???????????????? ???????????? ???? ????
    //  ModelsCategory::where('id', '=', '2')->update([
    //      'picture' => '2.jpeg'
    //  ]);
    // User::factory('10')->create();//?????????????????? ?????????????????? ???????????? ?? ????
    // $numbers = collect([1, 2, 3]); //?????????????? ?????????????????? ???? ??????????????
    // $has_two = $numbers->contains(function ($number) {
    //     return $number == 22;
    // });
    // dd($has_two);
    // $users = User::get();

    // $users->transform(function ($user) { // ???????????? ?????????????????? ?? ???????????? ????????
    //     return 
    //     [
    //         'id' => $user->id,
    //         'name' => $user->name
    //     ];
    // });

    //  $user = User::find(1);
    //  $user->password = Hash::make('123');
    // $user->save();

    // $mapped_users = $users->map(function ($user) //???? ???????????????? ?????????????????? ???????????????? ???????????????? ???????????? ????????????
    // {
    //     return [
    //         'id' => $user->id,
    //         'name' => $user->name
    //     ];
    // });

    // dd($mapped_users->toArray(), $users->toArray());
    // $ids = $users->pluck('name');
    // dd($ids);
});

Artisan::command('test', function () {
    
    //     $file_name ='Products.csv';
    //     $file = fopen($file_name, 'r');

    //     $carbon = new Carbon();

    //     $time = $carbon->now()->toDateTimeString();

    //     $i = 0;
    //     $insert = [];
    //     while($data = fgetcsv($file, 1000, ';'))
    //     {
    //     if($i++ == 0) continue;
    //     $id = $data[0];
    //         if (empty($data[0])) {
    //             $id = null;
    //         }
    //     $insert[] = [
    //         'id' => $id,
    //         'name' => $data[1],
    //         'description' => $data[2],
    //         'price' => $data[3],
    //         'picture' => $data[4],
    //         'category_id' => $data[5],
    //         'created_at' => $time,
    //         'updated_at' => $time
    //     ];
    //     }
        
    //     Product::upsert($insert,['id'],['name','description','price','picture','category_id']);
    // }


    // $names_category = $products->transform(function($product) {
    //     return ['name' => $product->category()->value('name')];
    // });
    // $products = Product::get();
    //         $productsExport = $products->map( function ($product) {
    //             $product['name_category'] = $product->category()->value('name');
    //             return $product;
    //           })->toArray();
            
    // $products2 =$products->merge($names_category);
    // $products = $products->map(function($product) {
    //     $product->category_id = $product->category()->value('name');
    //     return $product;
    // });
    //  dd( $productsExport);
    event(new CategoriesExportFinishEvents('test'));
   
   
});

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
