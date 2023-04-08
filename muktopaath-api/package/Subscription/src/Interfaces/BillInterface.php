<?php

namespace Subscription\Interfaces;

interface BillInterface 
{
    public function view();
    public function store($request);
    public function update($request);
    public function show($id);
    public function approve($request);
    public function delete($id);
}