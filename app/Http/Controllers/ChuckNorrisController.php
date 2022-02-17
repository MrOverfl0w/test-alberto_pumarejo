<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Async\Pool;

class ChuckNorrisController extends Controller
{
    public function index(){
        $array = [];
        $async_proc = Pool::create();
        for ($i = 0; $i < 15; $i++){
            $async_proc->add(function() use ($array){
                return $this->chucknorrisjokes($array);
            })->then(function(array $output) use (&$array){
                $array[] = $output;
            });
        }

        $async_proc->wait();
        $result = json_encode($array);
        return $result;
    }

    private function chucknorrisjokes(array $array){
        $data = $this->broma();
        if ( in_array( $data, $array ) ){
            return chucknorrisjokes($array);
        }
        return $data;
    }

    private function broma(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.chucknorris.io/jokes/random');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($data);
        $result = [];
        $result['icon_url'] = $json->icon_url;
        $result['id'] = $json->id;
        $result['value'] = $json->value;

        return $result;
    }
}
