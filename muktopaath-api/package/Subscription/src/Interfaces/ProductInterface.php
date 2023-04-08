<?php

namespace Subscription\Interfaces;

interface ProductInterface 
{
    public function view();
    public function store($request);
    public function update($request);
    public function delete($id);
}