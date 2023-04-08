<?php

namespace App\Interfaces\Finance;

interface PaymentRequestRepositoryInterface 
{
    public function allPaymentRequest();
    public function addPaymentRequest(array $request);
    public function updatePaymentRequest(array $request);
    public function deletePaymentRequest(int $id);
}