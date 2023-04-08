<?php

namespace App\Interfaces\Finance;

interface BalanceRepositoryInterface 
{
    public function allBalance();
    public function addBalance(array $request);
    public function updateBalance(array $request);
    public function deleteBalance(int $id);
}