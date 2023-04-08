<?php

namespace Muktopaath\Course\Exports;
  
use App\Models\Myaccount\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
  
class LearnerReport implements FromView
{

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return ["Name", "email","phone","Gender","Designation","course_completeness"];
    }

    // public function view(): View
    // {
    //     return view('exports.exports',[
    //         'data' => $this->data
    //     ]);
    // }
   
    public function collection()
    {
        return $this->data;
    }
}