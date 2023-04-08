<?php

namespace Muktopaath\Course\Exports;
  
use App\Models\Myaccount\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
  
class TransactionReport implements FromCollection,WithHeadings
{

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return ["user id", "amount","created at"];
    }
   
    public function collection()
    {
        return $this->data;
    }
}