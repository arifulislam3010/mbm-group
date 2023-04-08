<?php
namespace App\Exports;
  
use App\Models\Myaccount\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
  
class AttendanceExport implements FromCollection,WithHeadings
{

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return ["Student name", "attend time", "out time"];
    }
   
    public function collection()
    {
        return $this->data;
    }
}