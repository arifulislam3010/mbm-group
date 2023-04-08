<?php

namespace Muktopaath\Course\Interfaces;

interface TransactionInterface 
{
    public function create(Request $request);
    public function delete($id);
}