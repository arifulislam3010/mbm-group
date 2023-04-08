<?php

namespace Muktopaath\Course\Interfaces;

interface AccountsInterface 
{
    public function payments();
    //request function to store payment
    public function storePayment(Request $request);
    public function batch_payments();
    public function payment_status($id);
    public function approve($id);
    public function reject($id);
    public function delete($id);
    public function view_all_requests();
    public function overall_transactions();
}