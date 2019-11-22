<?php

/**
 * Created by PhpStorm.
 * User: MY PC
 * Date: 11/21/2019
* Time: 10:35 AM
*/
namespace App\Helpers;

class CalculatePrice
{
    public function __construct($type,$margin,$rate){
        $this->type =$type;
        $this->margin = $margin;
        $this->rate =$rate;

    }

    public function getRateStr(){

        if(strpos($this->rate,'USD')!==false){
            $result=str_replace("USD","",$this->rate);
            if (strpos($result, '/') !== false) {
                $result = str_replace("/","",$result);
            }
            return $result;
        }
        else{
            return $this->rate;
        }

    }

    public function retrievePrice(){
        $data = file_get_contents('https://api.coinbase.com/v2/prices/spot?currency=USD');
        $price = json_decode($data,true);
        return $price['data']['amount'];
    }

    public function retrieveExchangeRate(){

        $data = file_get_contents('https://api.coinbase.com/v2/exchange-rates');
        $rate = json_decode($data,true);
        return $rate['data']['rates'][$this->getRateStr()];
    }

    public function percent(){
        $percent = $this->margin / 100;
        $fraction = $percent * $this->retrievePrice();
        return $fraction;
    }

    public function buy(){
        $price = $this->retrievePrice() - $this->percent();
        return $price;
    }

    public function sell(){
        $price = $this->retrievePrice() + $this->percent();
        return $price;
    }

    public function returnRate(){
        if($this->type=='sell'){
            $rate = $this->retrieveExchangeRate() * $this->sell();
        }
        elseif ($this->type=='buy'){
            $rate = $this->retrieveExchangeRate() * $this->buy();
        }
        else{
            $rate ='Something went wrong. Try again';
        }

        return $rate;
    }
}