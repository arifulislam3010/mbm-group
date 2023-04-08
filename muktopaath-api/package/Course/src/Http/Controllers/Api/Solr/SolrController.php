<?php

namespace Muktopaath\Course\Http\Controllers\Api\Solr;
use App\Http\Controllers\Controller;
use Google\Service\Blogger\User;
use http\Env\Response;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SolrController extends Controller
{

    public function course(Request $request)
    {
        
        $search     = ($request->has('search'))?$request['search']:null;
        $id         = ($request->has('id'))?$request['id']:null;
        $start      = ($request->has('start'))?$request['start']:0;
        $rows       = ($request->has('rows'))?$request['rows']:10;
        $type       = ($request->has('type'))?$request['type']:'all';
        $owner_id   = ($request->has('owner_id'))?$request['owner_id']:null;
        $today_date = Carbon::now()->format('Y-m-d');

        /*
        course_alias_name ^100
        course_alias_name ^90
        tags  ^85
        course_motto ^80
        institution_name ^75
        institution_name_bn ^72
        description ^ 65
        objective ^ 60
        */

        if($id!=null)
        {
            if($search!=null){
                if($type=='favorite'){
                    if($owner_id){
                        $sd = array(
                            "q"=>'course_alias_name:*'.$search.'*^100'.' OR course_alias_name_en:*'.$search.'*^90 OR institution_name:*'.$search.'*^75 OR institution_name_bn:*'.$search.'*^72 OR objective:*'.$search.'*^60 OR course_motto:*'.$search.'*^80 OR description:*'.$search.'*^65',
                            "q.op"=>"AND",
                            "fq"=>'cat_id:('.str_replace("-", " ", $id).')',"rows"=>$rows,
                            "fq"=>'owner_id:'.$owner_id,
                            "hl"=>'true',
                            "indent"=>"true",
                            "hl.fl"=>"details OR course_alias_name OR course_alias_name_en",
                            "sort"=>"enroll desc", 
                            "start"=>$start);
                    }else{
                        $sd = array(
                            "q"=>'course_alias_name:*'.$search.'*^100'.' OR course_alias_name_en:*'.$search.'*^90 OR institution_name:*'.$search.'*^75 OR institution_name_bn:*'.$search.'*^72 OR objective:*'.$search.'*^60 OR course_motto:*'.$search.'*^80 OR description:*'.$search.'*^65',
                            "q.op"=>"AND",
                            "fq"=>'cat_id:('.str_replace("-", " ", $id).')',"rows"=>$rows,
                            "hl"=>'true',
                            "indent"=>"true",
                            "hl.fl"=>"details OR course_alias_name OR course_alias_name_en",
                            "rows"=>$rows,"sort"=>"enroll desc", "start"=>$start);
                    }
                }else if($type=='feature'){
                    // return 1;
                    if($owner_id){
                        $sd = array(
                            "q"=>'course_alias_name:*'.$search.'*^100'.' OR course_alias_name_en:*'.$search.'*^90 OR institution_name:*'.$search.'*^75 OR institution_name_bn:*'.$search.'*^72 OR objective:*'.$search.'*^60 OR course_motto:*'.$search.'*^80 OR description:*'.$search.'*^65',
                            "q.op"=>"AND",
                            "fq"=>'cat_id:('.str_replace("-", " ", $id).')',
                            "hl"=>'true',
                            "indent"=>"true",
                            "hl.fl"=>"details OR course_alias_name OR course_alias_name_en",
                            "rows"=>$rows,
                            "rows"=>$rows,"fq"=>'owner_id:'.$owner_id,"fq"=>'featured:true',"sort"=>"enroll desc", "start"=>$start);
                    }else{
                        $sd = array(
                            "q"=>'course_alias_name:*'.$search.'*^100'.' OR course_alias_name_en:*'.$search.'*^90 OR institution_name:*'.$search.'*^75 OR institution_name_bn:*'.$search.'*^72 OR objective:*'.$search.'*^60 OR course_motto:*'.$search.'*^80 OR description:*'.$search.'*^65',
                            "q.op"=>"AND",
                            "fq"=>'cat_id:('.str_replace("-", " ", $id).')',
                            "hl"=>'true',
                            "indent"=>"true",
                            "hl.fl"=>"details OR course_alias_name OR course_alias_name_en",
                            "rows"=>$rows,
                            "rows"=>$rows,"fq"=>'admin_featured:true',"sort"=>"feature_order asc", "start"=>$start);
                    }
                    
                }else{
                    if($owner_id){
                        $sd = array(
                            "q"=>'course_alias_name:*'.$search.'*^100'.' OR course_alias_name_en:*'.$search.'*^90 OR institution_name:*'.$search.'*^75 OR institution_name_bn:*'.$search.'*^72 OR objective:*'.$search.'*^60 OR course_motto:*'.$search.'*^80 OR description:*'.$search.'*^65',
                            "q.op"=>"AND",
                            "fq"=>'cat_id:('.str_replace("-", " ", $id).')',
                            "hl"=>'true',
                            "indent"=>"true",
                            "hl.fl"=>"details OR course_alias_name OR course_alias_name_en",
                            "rows"=>$rows,
                            "fq"=>'owner_id:'.$owner_id,"sort"=>"id desc","rows"=>$rows,"start"=>$start);
                    }else{
                        $sd = array(
                            "q"=>'course_alias_name:*'.$search.'*^100'.' OR course_alias_name_en:*'.$search.'*^90 OR institution_name:*'.$search.'*^75 OR institution_name_bn:*'.$search.'*^72 OR objective:*'.$search.'*^60 OR course_motto:*'.$search.'*^80 OR description:*'.$search.'*^65',
                            "q.op"=>"AND",
                            "fq"=>'cat_id:('.str_replace("-", " ", $id).')',
                            "hl"=>'true',
                            "indent"=>"true",
                            "hl.fl"=>"details OR course_alias_name OR course_alias_name_en",
                            "rows"=>$rows,
                            "sort"=>"id desc","rows"=>$rows,"start"=>$start);
                    }
                }
            }else{
                if($type=='favorite'){
                    if($owner_id){
                        $sd = array(
                            "q"=>'cat_id:('.str_replace("-", " ", $id).')',"rows"=>$rows,
                            "q.op"=>"AND",
                            "fq"=>'owner_id:'.$owner_id,
                            "sort"=>"enroll desc", 
                            "start"=>$start);
                    }else{
                        $sd = array(
                            "q"=>'cat_id:('.str_replace("-", " ", $id).')',
                            "rows"=>$rows,"sort"=>"enroll desc", "start"=>$start);
                    }
                }else if($type=='feature'){
                    // return 1;
                    if($owner_id){
                        $sd = array(
                            "q"=>'cat_id:('.str_replace("-", " ", $id).')',
                            "q.op"=>"AND",
                            "fq"=>'owner_id:'.$owner_id,
                            "rows"=>$rows,
                            "fq"=>'featured:true',"sort"=>"enroll desc", "start"=>$start);
                    }else{
                        $sd = array(
                            "q"=>'cat_id:('.str_replace("-", " ", $id).')',
                            "q.op"=>"AND",
                            "fq"=>'admin_featured:true',
                            "rows"=>$rows,"sort"=>"feature_order asc", "start"=>$start);
                    }
                    
                }else{
                    if($owner_id){
                        $sd = array(
                            "q"=>'cat_id:('.str_replace("-", " ", $id).')',
                            "q.op"=>"AND",
                            "fq"=>'owner_id:'.$owner_id,"sort"=>"id desc","rows"=>$rows,"start"=>$start);
                    }else{
                        $sd = array(
                            "q"=>'cat_id:('.str_replace("-", " ", $id).')',
                            "sort"=>"id desc","rows"=>$rows,"start"=>$start);
                    }
                }
            }
            
        }else{

            if($search!=null){

                //$search = str_replace(' ', '\\ ', $search);
                $sd = array(
                    "q"=>'course_alias_name:*'.$search.'*^100'.' OR course_alias_name_en:*'.$search.'*^90 OR institution_name:*'.$search.'*^75 OR institution_name_bn:*'.$search.'*^72 OR objective:*'.$search.'*^60 OR course_motto:*'.$search.'*^80 OR description:*'.$search.'*^65',
                    "hl"=>'true',
                    "indent"=>"true",
                    "hl.fl"=>"details OR course_alias_name OR course_alias_name_en",
                    "sort"=>"enroll desc",
                    "rows"=>$rows,
                    "start"=>$start,
                );
            }else if($type=='favorite'){
                if($owner_id){
                    $sd = array(
                        "q"=>'*:*',"rows"=>$rows,
                        "fq"=>'owner_id:'.$owner_id,
                        "sort"=>"enroll desc", 
                        "start"=>$start);
                }else{
                    $sd = array("q"=>'*:*',"rows"=>$rows,"sort"=>"enroll desc", "start"=>$start);
                }
            }else if($type=='feature'){
                // return 1;
                if($owner_id){
                    $sd = array("q"=>'*:*',"rows"=>$rows,"fq"=>'owner_id:'.$owner_id,"fq"=>'featured:true',"sort"=>"enroll desc", "start"=>$start);
                }else{
                    $sd = array("q"=>'*:*',"rows"=>$rows,"fq"=>'admin_featured:true',"sort"=>"feature_order asc", "start"=>$start);
                }
                
            }else{
                if($owner_id){
                    $sd = array("q"=>'*:*',"fq"=>'owner_id:'.$owner_id,"rows"=>$rows,"sort"=>"id desc", "start"=>$start);
                }else{
                    $sd = array("q"=>'*:*',"rows"=>$rows,"sort"=>"id desc", "start"=>$start);
                }
            }
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
//          CURLOPT_URL => 'http://searchapi.muktopaath.gov.bd/solr/demo/select',
            CURLOPT_URL => env('APACHE_SOLR').'/select',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POSTFIELDS => $sd,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resultdata = json_decode($response,true);
        if(isset($resultdata['response']) && $resultdata['response']['numFound']>0){


            $qryStr = 'INSERT INTO `search_key` (search_key,sc) VALUES';
            $qryStr .= '("'.trim($request['search']).'","1")';
            $qryStr .= ' ON DUPLICATE KEY UPDATE sc=sc+1';

            DB::select($qryStr);
        }
        return $resultdata;

    }

    public function searchkey(Request $request){
        $search       = ($request->has('search'))?$request['search']:null;
        return $data = DB::table('search_key')->where('search_key','like', '%' . $search . '%')->orderBy('sc','DESC')->take(10)->get();
    }

    public function solrSuggester(Request $request){
        $search       = ($request->has('search'))?$request['search']:null;
        $search_conv = urlencode($search);
        $url = env('APACHE_SOLR').'/suggest?suggest=true&suggest.build=true&suggest.dictionary=suggest&wt=json&suggest.q='.$search_conv;

        //$url = 'https://searchapi.muktopaath.gov.bd/solr/demo/suggest?suggest=true&suggest.build=true&suggest.dictionary=suggest&wt=json&suggest.q=%E0%A6%A8%E0%A6%BF';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec ($ch);
        $err = curl_error($ch);
        curl_close ($ch);

        return json_decode($response, true);
    }

//    public function courseSearch(Request $request){
//        $search       = ($request->has('search'))?$request['search']:null;
//        $id       = ($request->has('id'))?$request['id']:null;
//        $start       = ($request->has('start'))?$request['start']:0;
//        $rows       = ($request->has('rows'))?$request['rows']:20;
//        $today_date = Carbon::now()->format('Y-m-d');
//
//        if($id!=null)
//        {
//            if($search!=null){
//                $search = str_replace(' ', '\\ ', $search);
//                $sd = array("q"=>'course_alias_name:*'.$search.'*'.' or course_alias_name_en:*'.$search.'* or institution_name:*'.$search.'* or institution_name_bn:'.'* or objective:*'.$search.'* or course_motto:*'.$search.'* or details:*'.$search.'*',"fq"=>'cat_id:('.str_replace("-", " ",$id).')',"rows"=>$rows,"start"=>$start);
//                // return $sd;
//            }else{
//
//                $sd = array("q"=>'cat_id:('.str_replace("-", " ", $id).')',"rows"=>$rows,"start"=>$start);
//            }
//            // $sd = array("q"=>'cat_id:'.str_replace("-", " ", $id).'',"rows"=>$rows,"start"=>$start);
//        }else{
//
//            if($search!=null){
//
//                $search = str_replace(' ', '\\ ', $search);
//                $sd = array("q"=>'course_alias_name:*'.$search.'^100*'.' OR course_alias_name_en:*'.$search.'^90* OR institution_name:*'.$search.'^75* OR institution_name_bn:*'.$search.'^72* OR objective:*'.$search.'* OR course_motto:*'.$search.'^80* OR description:*'.$search.'^65*',"rows"=>$rows,"start"=>$start);
//            }else{
//                $sd = array("q"=>'*:*',"rows"=>$rows,"start"=>$start);
//            }
//        }
//
//        /*
//         course_alias_name ^100
//        course_alias_name ^90
//        tags  ^85
//        course_motto ^80
//        institution_name ^75
//        institution_name_bn ^72
//        description ^ 65
//
//          */
//
//        return $sd;
//
//        $curl = curl_init();
//
//        curl_setopt_array($curl, array(
////          CURLOPT_URL => 'http://searchapi.muktopaath.gov.bd/solr/demo/select',
//            CURLOPT_URL => 'http://127.0.0.1:8983/solr/mukto1/select',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_POSTFIELDS => $sd,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'GET',
//        ));
//
//        $response = curl_exec($curl);
//
//        curl_close($curl);
//
//        $resultdata = json_decode($response,true);
//        if(isset($resultdata['response']) && $resultdata['response']['numFound']>0){
//
//
//            $qryStr = 'INSERT INTO `search_key` (search_key,sc) VALUES';
//            $qryStr .= '("'.trim($request['search']).'","1")';
//            $qryStr .= ' ON DUPLICATE KEY UPDATE sc=sc+1';
//
//            DB::select($qryStr);
//        }
//        return $resultdata;
//    }


    public function searchFilter(Request $request){
        $price_start  = ($request->has('price_start'))?$request['price_start']:1;
        $price_gap  = ($request->has('price_gap'))?$request['price_gap']:100;
        $price_end  = ($request->has('price_end'))?$request['price_end']:500;

        $url = env('APACHE_SOLR').'/select?f.payment_point_amount.facet.range.end='.$price_end.'&f.payment_point_amount.facet.range.gap='.$price_gap.'&f.payment_point_amount.facet.range.start='.$price_start.'&facet.contains.ignoreCase=true&facet.field=payment_point_amount&facet.field=tags&facet.mincount=1&facet.missing=false&facet.range=payment_point_amount&facet.sort=count&facet=true&indent=true&q=*%3A*&rows=2000000&sort=rating_count%20desc';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec ($ch);
        $err = curl_error($ch);
        curl_close ($ch);

        return json_decode($response, true);
    }

    public function recommendation(Request $request){
        $user_id = Auth::user()->id;
        $myaccount = config()->get('database.connections.my-account.database');
        $type       = ($request->has('type'))?$request['type']:null;
        $type_encode = urlencode('(type:'.$type.'%5E25%20OR%20type%3A('.$type.')%5E10)');
        $advertisement_category_id = 2;
        $adc_encode = urlencode('(advertisement_category_id%3A'.$advertisement_category_id.')');
        $division_id = DB::table($myaccount.'.users as users')
            ->select($myaccount.'.user_infos.division_id')
            ->leftJoin($myaccount.'.user_infos','users.id','=',$myaccount.'.user_infos.user_id')
            ->where('users.id', '=', $user_id)
            ->value('division_id');
        $district_id = DB::table($myaccount.'.users as users')
            ->select($myaccount.'.user_infos.district_id')
            ->leftJoin($myaccount.'.user_infos','users.id','=',$myaccount.'.user_infos.user_id')
            ->where('users.id', '=', $user_id)
            ->value('district_id');

        $sub_district_id = DB::table($myaccount.'.users as users')
            ->select($myaccount.'.user_infos.sub_district_id')
            ->leftJoin($myaccount.'.user_infos','users.id','=',$myaccount.'.user_infos.user_id')
            ->where('users.id', '=', $user_id)
            ->value('sub_district_id');

        if($type!=null and $advertisement_category_id!=null){
            //  return $district_id;
            if($district_id==null && $division_id!=null ){
                
                $url = 'http://172.17.129.24:8983/solr/Recom/select?q.op=AND&q='.$type_encode.'%20AND%20'.$adc_encode.'%0AAND%20division_id%3A'.$division_id.'%5E15&rows=5&sort=updated_at%20desc&start=0';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec ($ch);
                $err = curl_error($ch);
                curl_close ($ch);

                return json_decode($response, true);
            }
            elseif ($division_id!=null and $district_id!=null){
                $div_dis_encode = urlencode('(division_id%3A1%20AND%20district_id%3A1)');
                $url = 'http://172.17.129.24:8983/solr/Recom/select?q.op=AND&q='.$type_encode.'%20AND%20'.$adc_encode.'%0AAND%20'.$div_dis_encode.'%5E15&rows=5&sort=updated_at%20desc&start=0';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec ($ch);
                $err = curl_error($ch);
                curl_close ($ch);

                return json_decode($response, true);
            }

            elseif ($division_id!=null and $district_id!=null and $sub_district_id!=null){
                $div_dis_sub_encode = urlencode('((division_id%3A1%20AND%20district_id%3A1)%5E15%20OR%20sub_district_id%3A1)%0AAND%20(division_id%3A1%20AND%20district_id%3A1)');
                $url = 'http://172.17.129.24:8983/solr/Recom/select?q.op=AND&q='.$type_encode.'%20AND%20'.$adc_encode.'%0AAND%20'.$div_dis_sub_encode.'%5E15&rows=5&sort=updated_at%20desc&start=0';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec ($ch);
                $err = curl_error($ch);
                curl_close ($ch);

                return json_decode($response, true);
            }
        }

    }
    
    public function emisMerchantSignIn(Request $request){
        $pdsid       = ($request->has('pdsid'))?$request['pdsid']:null;
        $date_of_birth       = ($request->has('date_of_birth'))?$request['date_of_birth']:null;
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://103.69.149.41/sso/Services/Security/PublicUser/MerchantSignIn',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('UserName' => 'MyGov','Password' => '1234567856'),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
        // if($response)
        $rd = json_decode($response);
        if(isset($rd->Code) && $rd->Code==200){
            
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://103.69.149.41/sso/Services/Security/PublicUser/GetMpoInfo?MerchantId=000003&token='.$rd->_token.'&PDSID='.$pdsid,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            // return $response;
            $rud = json_decode($response);
            // return $rud->DateOfBirth;
            if($rud->PDSID==$pdsid && $rud->DateOfBirth==$date_of_birth){
                return $response;
            }else{
                return 1;
            }
            

        }
        
        
        
        
    
        
    }

}
