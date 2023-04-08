<?php
namespace Muktopaath\Course\Models\Course;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $connection = 'course';

    public function background()
    {
        return $this->belongsTo('App\Models\Filemanager\ContentBank', 'background_id', 'id')->select(['id','type','file_encode_path','file_main_path','is_url']);
    }
}
