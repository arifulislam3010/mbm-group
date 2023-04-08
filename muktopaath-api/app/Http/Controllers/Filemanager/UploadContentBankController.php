<?php

/**
* class UploadContentBankController
* @category controller
*/

namespace App\Http\Controllers\Filemanager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Filemanager\ContentBank;
use App\Models\Filemanager\UrlUpload;
use App\Models\Filemanager\Folder;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Filemanager\ContentBank as ContentBankResource;
use App\Models\User\InstitutionInfo;
use DB;
use Auth;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Intervention\Image\Facades\Image;
use App\User;

class UploadContentBankController extends Controller
{ 
    private $image_ext = ['jpg','jpeg' ,'png' ,'gif'];
    private $audio_ext = ['mp3', 'ogg', 'mpga'];
    private $video_ext = ['mp4', 'mpeg', 'avi', 'mov', 'mpeg-4', 'wmv', 'mpeg-ps', 'flv', '3gpp', 'webm'];
    private $document_ext = ['doc', 'docx', 'pdf', 'pptx', 'ppt', 'xls', 'xlsx'];

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        return Storage::disk('public')->allfiles();
        return response()->json($_FILES);
    }


    public function cropupload(Request $request){ 

        $data = $request->all();
      
            $result = substr($data['cropped'],0,10);
            if ($result=='data:image') {
            //$image_path = 'globals';

            $imageData = $data['cropped'];

            $RouteUser = config()->get('global.user_id').'/profile';

            $fileName = uniqid().'.'.explode('/',explode(':',substr($imageData,0,strpos($imageData, ';')))[1])[1];

            $base_path = config('global.content_images');

            $file = Image::make($data['cropped']);

            File::makeDirectory('storage/uploads/images/user-'.config()->get('global.user_id').'/profile/', $mode = 0777, true, true);

            $type = 'image';
            $user_id = config()->get('global.user_id');
            $file_name_setup = $type . '-' . $user_id . '-' . time();
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $file_name_for_save = $file_name_setup . '.' . $ext;

            $store = Image::make($data['cropped'])->save(public_path('storage/'.$base_path . 'user-'.$RouteUser).'/'.$file_name_for_save);

                // return $store;
                if($store){
  
                    $contentBank = new ContentBank;
                    
                    $contentBank->title = 'profile pic';
                    $contentBank->type = 'image';
                    $contentBank->content_id = $user_id . time();
                    $contentBank->file_name = $fileName;
                    $contentBank->file_encode_path = base64_encode('user-'.$RouteUser . '/' . $file_name_setup) . '&' . $ext;
                    $contentBank->file_main_path = 'user-'.$RouteUser. $file_name_setup. '.' . $ext;
                    //$contentBank->size = $size/1024;
                    $contentBank->folder_id = $request->folder_id?$request->folder_id:null;
                    $contentBank->owner_id = config()->get('global.owner_id');
                    $contentBank->created_by = $user_id;
                    $contentBank->updated_by = $user_id;
                    $contentBank->created_at = date('Y-m-d H:i:s');
                    $contentBank->save();

                    return $contentBank;
                }

            if($store){
                return $base_path.'user-'.$RouteUser.'/profile/'.$fileName;
            }else{
                return response()->json(['message' => 'Something went wrong. try again later']);
            }
        }
        

      //return $request->all();
    }



    /**
     * Submit a set of data
     * @return Response
     */
    public function update(Request $request)
    {        
        if(!empty($request->input())){
            
            $contentBank = new ContentBank();
            
            $QryFieldStr = '(';
            $QryValueStr = '';
            $QryDupUpdtStr = '';            
            
            $i=0; foreach($request->input() as $index => $data){            
                
                $co=0; if($i++>0) $QryValueStr .= ',';
                $QryValueStr .= '(';
                
                foreach($data as $field => $value){
                    if($value==null) continue;
                    if($co++>0){
                        if($i == 1){
                            $QryFieldStr .= ',';                    
                            $QryDupUpdtStr .= ',';
                        }
                        $QryValueStr .= ',';
                    }
                    if($i==1){
                        $QryFieldStr .= $field;
                        $QryDupUpdtStr .= $field . ' = VALUES(' . $field . ')';
                    }
                    $QryValueStr .= (is_numeric($value)?$value:'"'.$value.'"');
                }

                $QryValueStr .= ')';            
            }        
            
            $QryFieldStr .= ')';
            
            $QryStr = 'INSERT INTO ' . $contentBank->getTable() . $QryFieldStr . ' VALUES' . $QryValueStr . '
                        ON DUPLICATE KEY UPDATE ' . $QryDupUpdtStr;
            
            //return $QryStr;
            if(DB::statement($QryStr)) return '1';
            else return '0';

        }else return '0';
    }

    /**
     * Chunk files
     * @return Response
     */
    public function chunk(Request $request)
    {
        // return $request->all();
        // return $request->file('file');
        $file   = $request->file('file');
        $name   = $file->getClientOriginalName();
        $type   = $request['file_type'];

        if ($type === 'image') $base_path = config('global.content_images');
        elseif ($type === 'audio') $base_path = config('global.content_audios');
        elseif ($type === 'video') $base_path = config('global.content_videos');
        else $base_path = config('global.content_documents');

        // Got authenticate user id
        $user_id = Auth::id(); 
        $RouteUserName = config('global.username');

        $RouteUser = InstitutionInfo::where('username',$RouteUserName)->first();

        $file_name_setup = $user_id.'-blob-'.$request['file_index'].'-'.$request['num'];
        $file_name_for_save = $file_name_setup;

        if(Storage::disk('public')->putFileAs( $base_path . 'user-'.$RouteUser->id . '/chunk_files', $file, $file_name_for_save )){
            return '1';
        }else{
            return '0';
        }
    }

    /**
     * Chunk file marge
     * @return Response
     */
    public function chunk_merge(Request $request)
    {
        $num_chunks = $request['num_chunks'];
        $type = $request['file_type'];

        if ($type === 'image') $base_path = config('global.content_images');
        elseif ($type === 'audio') $base_path = config('global.content_audios');
        elseif ($type === 'video'){
            $base_path = config('global.content_videos');
            $ext = 'mp4';
        }
        else $base_path = config('global.content_documents');

        // Got authenticate user id
        $user_id = Auth::id();
        $RouteUserName = config('global.username');

        $RouteUser = InstitutionInfo::where('username',$RouteUserName)->first();

        $target_file = 'storage/' . $base_path . 'user-'.$user_id . '/chunk_files/' . $user_id .'-blob-'.$request['file_index'];
        $final_target_file = 'storage/' . $base_path . 'user-'.$user_id . '/' . $type . '-' . $user_id . '-' . time();
        //$file_name_for_save = $file_name_setup;

        // count ammount of uploaded chunks
        /*$chunksUploaded = 0;
        for ( $i = 1; $i <= $num_chunks; $i++ ) {
            echo $target_file.'-'.$i;
            if ( file_exists( $target_file.'-'.$i ) ) {
                ++$chunksUploaded;
            }
        }
        echo $chunksUploaded.'/'.$num_chunks;*/
        // and THAT's what you were asking for
        // when this triggers - that means your chunks are uploaded
        // if ($chunksUploaded == $num_chunks) {
            echo '<br>Full';
            /* here you can reassemble chunks together */
            for ($i = 1; $i <= $num_chunks; $i++) {
                echo $target_file.'-'.$i;
                $file = fopen($target_file.'-'.$i, 'rb');
                $buff = fread($file, 2097152);
                fclose($file);
            
                $final = fopen($final_target_file.'.'.$ext, 'ab');
                $write = fwrite($final, $buff);
                fclose($final);
            
                unlink($target_file.'-'.$i);
            }
        // } 
    }

    public function get(){
        $res = ContentBank::where('license',1)->get();
        return response()->json($res);

    }

    /**
    * method to download a file
    * @access public
    * @param string $type
    * @param string $folder
    * @return downloaded file
    */
    public function getfiles(Request $request){

                $type = '';
                $request->type=='images'?$type='image':'';
                $request->type=='videos'?$type='video':'';
                $request->type=='audios'?$type='audio':'';
                $request->type=='documents'?$type='document':'';

            if(!File::isDirectory('storage/uploads/'.$request->type.'/user-'.config()->get('global.user_id'))){
                $directories = [];
                $files = [];
             }else{
                $directories = $request->folder_id?'': Folder::where('user_id',config()->get('global.user_id'))
                    ->where('type',$request->type)
                    ->get();

                $files = ContentBank::where('created_by',config()->get('global.user_id'))
                ->when($request->folder_id,function($query) use($request) {
                    return $query->where('folder_id',$request->folder_id);
                })
                ->when(!$request->folder_id,function($query) use($request) {
                    return $query->where(function($q) {
                            $q->where('folder_id',null)
                                ->orWhere('folder_id',0);
                        });
                })
                ->where('type',$type)
                ->get();

        }

    // $urls = UrlUpload::when($request->folder_id,function($query) use($request) {
    //             return $query->where('folder_id',$request->folder_id);
    //         })->where('user_id',config()->get('global.user_id'))
    //          ->where('type',$request->type)->get();

        return response()->json([
            'directories' => $directories,
            'files' => $files
        ]);

    }
    

    /**
    * method to download a file
    * @access public
    * @param string $file
    * @return downloaded file
    */
    public function download_file(Request $request){

        try{
            $file = public_path('storage/'.'/uploads/'.$request->type.'/'.$request->file);


            $check = 'user-'.config()->get('global.user_id');

            if (strpos($file, $check) !== false) {

                $response = new BinaryFileResponse($file, 200 );
                return $response;
            }
        }catch (\Throwable $e) {
            
            return response()->json([
                'error' => [
                    'description' => $e->getMessage()
                ]
            ], 500);   
        }
        
        
    }

    public function profilefolder(Request $request){

        $check = Folder::where('name','profile')
                ->where('user_id',config()->get('global.user_id'))
                ->where('type','images')
                ->first();
        if($check){
            $res = $check;
        }else{
            $data = new Folder;
            $data->name = 'profile';
            $data->type = 'images';
            $data->user_id = config()->get('global.user_id');
            $data->save();

            $res = $data;
        }

        return response()->json($res);

    }

    /**
    * method to create a new folder in own directory
    * @access public
    * @param string $type
    * @param string $folder
    * @return collection
    */
    public function createfolder(Request $request){

        try{

            File::makeDirectory('storage/uploads/'.$request->type.'/user-'.config()->get('global.user_id').'/'.$request->folder, $mode = 0777, true, true);

            $folder = new Folder;
            $folder->name = $request->folder;
            $folder->type = $request->type;
            $folder->user_id = config()->get('global.user_id');
            $folder->parent_id = null;
            $folder->save();

        }catch (\Throwable $e) {
            
            return response()->json([
                'error' => [
                    'description' => $e->getMessage()
                ]
            ], 500);   
        }

        return response()->json(['message'=> 'Successfully created folder'],201);
        

    }

    public function savename(Request $request){

        $res = contentBank::find($request['id']);
        $res->title = $request['name'];
        $res->update();

        return response()->json($res);

    }

    /**
    * method to upload a file to own directory
    * @access public
    * @param file $file
    * @param string $folder
    * @return collection
    */
    public function store(Request $request)
    { 
        try{

            $max_size = (int)ini_get('upload_max_filesize') * 5000;
            $all_ext = implode(',', $this->allExtensions());

            $file   = $request->file('file');
            $name   = $file->getClientOriginalName();
            $size   = $file->getSize();
            $ext    = strtolower($file->getClientOriginalExtension());
            $type   = $this->getType($ext);
            
            if ($type == 'image') $base_path = config('global.content_images');
            elseif ($type == 'audio') $base_path = config('global.content_audios');
            elseif ($type == 'video') $base_path = config('global.content_videos');
            else $base_path = config('global.content_documents');
            
            // Got authenticate user id
            $user_id = config()->get('global.user_id');
            $RouteUserName = config('global.username');
            if($request->folder!=''){
                $RouteUser = config()->get('global.user_id').'/'.$request->folder;
            }else{
                $RouteUser = config()->get('global.user_id');
            }
            

            $file_name_setup = $type . '-' . $user_id . '-' . time();
            $file_name_for_save = $file_name_setup . '.' . $ext;

            $contentBank = new ContentBank;

            if (Storage::disk(config('filesystems.driver'))->putFileAs( $base_path . 'user-'.$RouteUser, $file, $file_name_for_save )) {


                $contentBank->title = pathinfo($name, PATHINFO_FILENAME);
                $contentBank->type = $type;
                $contentBank->content_id = $user_id . time();
                $contentBank->file_name = $file_name_for_save;
                $contentBank->file_encode_path = base64_encode('user-'.$RouteUser . '/' . $file_name_setup) . '&' . $ext;
                $contentBank->file_main_path = 'user-'.$RouteUser.'/'. $file_name_setup. '.' . $ext;
                $contentBank->size = $size/1024;
                $contentBank->folder_id = $request->folder_id?$request->folder_id:null;
                $contentBank->owner_id = config()->get('global.owner_id');
                $contentBank->created_by = $user_id;
                $contentBank->updated_by = $user_id;
                $contentBank->created_at = date('Y-m-d H:i:s');
                $contentBank->save();

                return $contentBank;
                // return $base_path.'user-'.$RouteUser . '/' . $file_name_setup.'.'.$ext;
                return new contentBankResource($contentBank);
            }   
        } catch (\Throwable $e) {
            
            return response()->json([
                'error' => [
                    'description' => $e->getMessage()
                ]
            ], 500);   
        }
    }

    /**
    * method to remove a file from directory
    * @access public
    * @param string $file
    * @return json response
    */
    public function removeFile(Request $request){

         try {
                $del = contentBank::find($request->id);
                $del->delete();

                Storage::disk(config('filesystems.driver'))->delete($request->file_main_path);
            } catch (\Throwable $e) {
            
            return response()->json([
                'error' => [
                    'description' => $e->getMessage()
                ]
            ], 500);
        }

        return response()->json(['message'=> 'Successfully deleted file'],200);

    }

    public function deletefolder(Request $request){
    

         try {
                Storage::disk(config('filesystems.driver'))->deleteDirectory($request->folder);
            } catch (\Throwable $e) {
            
            return response()->json([
                'error' => [
                    'description' => $e->getMessage()
                ]
            ], 500);
        }

        return response()->json(['message'=> 'Successfully deleted folder'],200);

    }

    /**
    * method to upload a url link to be stored in db
    * @access public
    * @param string $type
    * @param string $url
    * @return json response
    */
    public function url_upload(Request $request){

        try{
            $user_id = config()->get('global.user_id');

            if($request->type == 'images'){
                $type = 'image';
            }else if($request->type == 'videos'){
                $type = 'video';
            }else if ($request->type == 'documents'){
                $type = 'document';
            }

            $contentBank = new ContentBank;

            $contentBank->title = $request->type.' Url';
            $contentBank->type = $type;
            $contentBank->content_id = config()->get('global.user_id') . time();
            //$contentBank->file_name = $file_name_for_save;
            //$contentBank->file_encode_path = base64_encode('user-'.$RouteUser . '/' . $file_name_setup) . '&' . $ext;
            $contentBank->file_main_path = $request->url;
            //$contentBank->size = $size/1024;
            $contentBank->folder_id = $request->folder?$request->folder:null;
            $contentBank->owner_id = config()->get('global.owner_id');
            $contentBank->created_by = $user_id;
            $contentBank->is_url = 1;
            $contentBank->updated_by = $user_id;
            $contentBank->created_at = date('Y-m-d H:i:s');
            $contentBank->save();
            
        }catch (\Throwable $e) {
            
            return response()->json([
                'error' => [
                    'description' => $e->getMessage()
                ]
            ], 500);
        }

        return response()->json($contentBank);

    } 



    /**
     * Get type by extension
     * @param  string $ext Specific extension
     * @return string      Type
     */
    private function getType($ext)
    {
        if (in_array($ext, $this->image_ext)) {
            return 'image';
        }

        if (in_array($ext, $this->audio_ext)) {
            return 'audio';
        }

        if (in_array($ext, $this->video_ext)) {
            return 'video';
        }

        if (in_array($ext, $this->document_ext)) {
            return 'document';
        }
    }

    /**
     * Get all extensions
     * @return array Extensions of all file types
     */
    private function allExtensions()
    {
        return array_merge($this->image_ext, $this->audio_ext, $this->video_ext, $this->document_ext);
    }

    /**
     * Get directory for the specific user
     * @return string Specific user directory
     */
    private function getUserDir()
    {
        $user = InstitutionInfo::where('user_id' , Auth::id())->first()->id;
        return 'user-' . $user;
    }
}
