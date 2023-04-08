<?php

namespace Subscription\Interfaces;

interface PackageInterface 
{
    public function view();
    public function store($request);
    public function update($request);
    public function show($id);
    public function delete($id);
}