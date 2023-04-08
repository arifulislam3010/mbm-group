<?php 
namespace App\Lib;
use Illuminate\Http\Request;
use App\Models\ContentBank\LearningContent;
use Carbon\Carbon;
use DB;
use App\Models\Question\Question;
trait ContentBank{
    public function insertContent($datum){
        if($datum['content_type']=='discussion' && $datum['content_type']=='presentation' && $datum['content_type']=='interview'){
            return '';
        }else{
            $edit = 1;
            $quiz_data = [];
            if(isset($datum['content']['questions']) && count($datum['content']['questions'])>0){
            
                foreach ($datum['content']['questions'] as $key => $qd) {
                  $qi = $this->questionAddUpdate($qd);
                   array_push($quiz_data,$qi);
                }
            }else{
                $quiz_data = null;
            }
            

            if(isset($datum['content']['id']) && $datum['content']['id']!=''){
                $content = LearningContent::find($datum['content']['id']);
                $edit = 1;
            }else{
                $edit = 0;
                $content = new LearningContent;
            }

            $user_id = config()->get('global.user_id');
            $content->more_data_info = isset($datum['content']['more_data_info'])?json_encode($datum['content']['more_data_info']):null;
            $content->content_type   = isset($datum['content']['content_type'])?$datum['content']['content_type']:null;
            $content->file_id        = isset($datum['content']['file_id'])?$datum['content']['file_id']:null;
            $content->cat_id        = isset($datum['content']['cat_id'])?$datum['content']['cat_id']:null;
            $content->level        = isset($datum['content']['level'])?$datum['content']['level']:null;
            $content->language_id        = isset($datum['content']['language_id'])?$datum['content']['language_id']:null;
            $content->content_or_url      = isset($datum['content']['content_or_url'])?$datum['content']['content_or_url']:null;
            $content->content_url         = isset($datum['content']['content_url'])?$datum['content']['content_url']:'';
            $content->title          =  isset($datum['content']['title'])?$datum['content']['title']:null;
            // $content->title          = isset($datum['content']['title'])?$datum['content']['title']:null;
           
            // $content->description    = isset($datum['content']['description'])?$datum['content']['description']:null;
            $content->description    =  isset($datum['content']['description'])?$datum['content']['description']:null;
            $content->instruction    =  isset($datum['content']['instruction'])?$datum['content']['instruction']:null;
            $content->duration       = isset($datum['content']['duration'])?$datum['content']['duration']:null;
            $content->quiz        = isset($datum['content']['quiz'])?$datum['content']['quiz']:0;
            $content->question_setup       = isset($datum['content']['question_setup'])?$datum['content']['question_setup']:null;
            $content->owner_id       = Config('global.owner_id');
            $content->created_by     = $user_id;
            $content->updated_by     = $user_id;
            if($datum['content_type']=='assignment' && $datum['content_type']=='discussion' && $datum['content_type']=='presentation' && $datum['content_type']=='interview'){
                $content->quiz_marks = null;
                $content->quiz_data = null;
            }else{
                if(isset($datum['content']['question_setup']) && $datum['content']['question_setup'] == 1){
                    $content->folder_id      = isset($datum['content']['folder_id'])?json_encode($datum['content']['folder_id']):null;
                    $content->folder_marks   = isset($datum['content']['folder_marks'])?json_encode($datum['content']['folder_marks']):null;
                }
                elseif(isset($datum['content']['question_setup']) && $datum['content']['question_setup'] == 0){
                    if($quiz_data!=null){
                        $content->quiz_data  = json_encode($quiz_data);
                    }else{
                        $content->quiz_data = null;
                    }
                    if(isset($datum['content']['quiz_marks'])){
                        if(!empty($datum['content']['quiz_marks'])){
                            $content->quiz_marks      = json_encode($datum['content']['quiz_marks']);
                        }else{
                            $content->quiz_marks = null;
                        }
                    }
                
                }
            }
            

            if($edit==1){
                if($content->update()){
                    return $content;
                }
            }else{
                if($content->save()){
                    return $content;
                }
            }
        }
    }
    public function questionAddUpdate($data){
        if(isset($data['id']))
        {
            $Question = Question::findOrfail($data['id']);
            
        }else{
            $Question = new Question();
        }
       
        
        $Question->id                          = isset($data['id'])?$data['id']:null;
        //$Question->partner_category            = $data['partner_category'];
        if(isset($data['folder'])){
            $Question->partner_category            = $data['folder'];
        }
        $Question->title                       = $data['title'];
        $Question->category_id                 = isset($data['category_id'])?$data['category_id']:null;
        // $Question->title_content_type          = $data['title_content_type'];
        // $Question->title_content_id            = $data['title_content_id'];
        // $Question->title_content_url           = $data['title_content_url'];
        $Question->description                  = isset($data['description'])?$data['description']:null;
        $Question->type                        = isset($data['type'])?$data['type']:null;
        $Question->file_id                     = isset($data['file_id'])?$data['file_id']:null;
        $Question->mark                        = isset($data['marks'])?$data['marks']:1;
        $Question->dif_level                   = isset($data['dif_level'])?$data['dif_level']:null;
        if($data['type']=='sequence' || $data['type']=='matching'){
            $Question->options                     = json_encode($data['sequence']);
            $Question->answer                     = json_encode($data['body']);
        }
        else if($data['type']=='likert-scale'){
            $Question->options                     = json_encode($data['multiplebody']);
        }else if($data['type']=='essay'){
            $Question->options                     = json_encode($data['rubric_criteria']);
            if($data['rubric_grading']=='true'){
                $Question->rubric_grading = 1;
            }else{
                $Question->rubric_grading = 0;
            }
        }
        else{
            $opt = $data['body'];
            foreach ($opt as $key => $value) {
                 $opt[$key]['answer'] = '';
            }
            $Question->options                     = json_encode($opt);
            $Question->answer                      = json_encode($data['body']);
        }
        
        $Question->submission_criteria         = json_encode($data['submission_criteria']);
        $Question->details                     = isset($data['details'])?$data['details']:null;
        $Question->feedback                    = json_encode($data['feedback']);
        // $Question->time                        = $data['time'];
        // $Question->date                        = $data['date'];
        // $Question->status                      = $data['status'];
       

        if(isset($data['id'])){
            $Question->updated_by              = config()->get('global.user_id');
        }else
        {
            $Question->created_by              = config()->get('global.user_id');
            $Question->updated_by              = config()->get('global.user_id');
        }
        $Question->owner_id                    = config()->get('global.owner_id');
      
        if(isset($data['id'])){
            if($Question->update())
            {
                return $Question->id;
            }
        }else{
            if($Question->save())
            {
                return $Question->id;
            }
        }
    }
}