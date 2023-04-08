<?php

namespace App\Interfaces\Promotion;

interface PromotionInterface 
{
    public function  list();
    public function  show($request);
    public function  showsingle($id);
    public function  store($request);
    public function  update($request);
    public function  approve($id);
    public function  delete($id);
}