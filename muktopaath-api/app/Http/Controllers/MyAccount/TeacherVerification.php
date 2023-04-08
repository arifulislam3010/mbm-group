<?php

namespace App\Http\Controllers\MyAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Myaccount\User;
// use App\Lib\Sms;
use App\Models\Myaccount\EmisInfo;
use App\Models\Assessment\CourseEnrollment;
use Auth;
use DateTime;
use App\Models\Assessment\Order;
// use App\Mail\VerifyEmailOtp;
// use Mail;

class TeacherVerification extends Controller
{

    // use Sms;

    public function verification(Request $request){
        // return 1;
        try{
            DB::beginTransaction();
            // return response()->json(['status'=>0,'data'=>'test'],200);
            if(Auth::user()){
                $user_id = Auth::user()->id;
            }
            
            
            $pdsid       = ($request->has('pdsid'))?$request['pdsid']:null;
            $date_of_birth_a       = ($request->has('date_of_birth'))?$request['date_of_birth']:null;
            $date_of_birth = date('Y-m-d', strtotime($date_of_birth_a));
            // return response()->json(['status'=>0,'data'=>$date_of_birth,'sd'=>$date_of_birth_a],200);
            $otp       = ($request->has('otp'))?$request['otp']:null;
            $pds_type       = ($request->has('pds_type'))?$request['pds_type']:null;
            $step       = ($request->has('step'))?$request['step']:null;
        
            if(Auth::user()){
                $user = User::find($user_id);
                $EmisInfo = EmisInfo::where('user_id',$user_id)->first();
                if(!$EmisInfo){
                    $EmisInfo = new EmisInfo();
                }
                $emis = DB::connection('course')->table('emis_info')->where('user_id',$user->id)->first();
                
            }else{
                $EmisInfo = EmisInfo::where('pdsid',$pdsid)->whereNull('user_id')->first();
                if(!$EmisInfo){
                    $EmisInfo = new EmisInfo();
                }
            
            }

        
            $pdsidCheck = User::where('pdsid',$pdsid)->first();
            // $pdsidCheck = User::where('pdsid',$pdsid)->first();
            // $pdsidCheck2 = EmisInfo::where('pdsid',$pdsid)->whereNotNull('user_id')->first();
            if($pdsidCheck){
                if($pdsidCheck->verify_status==1 || $pdsidCheck->verify_status_phone==1){
                    return response()->json(['status'=>1,'data'=>$pdsidCheck],200);
                }else{
                    $pdsidCheck->pdsid=null;
                    $pdsidCheck->update();
                    // return response()->json(['status'=>1,'data'=>$pdsidCheck],200);
                }
            }
            // return $pds_type;
           
            if($pds_type==1){
                $dsheGet = DB::connection('course')->table('dshe_info')->where('EMPID',$pdsid)->first();
                if($dsheGet){
                    // $date_dob = DateTime::createFromFormat('Y-m-d', $date_of_birth);
                    // return $date_dob; 
                    $final_date_dob =$date_of_birth;
                    if($dsheGet->DOB==$final_date_dob){
                        
                        
                            $EmisInfo->DivisionId = $dsheGet->DivisionId;
                            $EmisInfo->DistrictId = $dsheGet->DistrictId;
                            $EmisInfo->UpazillaId = $dsheGet->UpazilaId;
                            $EmisInfo->EIIN = $dsheGet->EIIN;
                            $EmisInfo->InstituteName = $dsheGet->INSTITUTENAME;
                            $EmisInfo->InstituteType = $dsheGet->InstituteCategory;
                            // $EmisInfo->Address = $insinfo->InstituteInfoDataList[0]->Address;
                            // $EmisInfo->BranchInstituteCategory = $insinfo->InstituteInfoDataList[0]->BranchInstituteCategory;
                            // $EmisInfo->InstituteCategory = $insinfo->InstituteInfoDataList[0]->InstituteCategory;
                            $EmisInfo->Name = $dsheGet->FULLNAME;
                            $EmisInfo->Designation = $dsheGet->Designation;
                            $EmisInfo->DateOfBirth = $dsheGet->DOB;
                            $EmisInfo->pdsid = $pdsid;
                            $EmisInfo->type = 1;
                            if(Auth::user()){
                                $EmisInfo->user_id = $user_id;
                            }
                            // $EmisInfo->intitute_info = json_encode($insinfo);
                            // $EmisInfo->user_info = json_encode($rud);
                            $EmisInfo->Subject = $dsheGet->Subject;
                            $EmisInfo->save();
                        // }
                        if(Auth::user()){
                            $user->pdsid = $pdsid;
                            $user->certificate_name = $dsheGet->FULLNAME;
                            $user->emis_otp = rand(10000,99999);
                            $user->update();  
                        }
                        // else{
                        //     return response()->json(['status'=>100,'data'=>$dsheGet],200);
                        // }
                        DB::commit();
                        return response()->json(['status'=>100,'data'=>$EmisInfo],200);
                    }else{
                        return response()->json(['status'=>0,'data'=>''],200);
                    }
                }  
    
                $curl = curl_init();
    
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://emis.gov.bd/sso/Services/Security/PublicUser/MerchantSignIn',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('UserName' => 'MyGov','Password' => 'MyGov258852'),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                
                // if($response)
                $rd = json_decode($response);
                if(isset($rd->Code) && $rd->Code==200){
                    
                    
                    $curl = curl_init();
    
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'http://emis.gov.bd/sso/Services/Security/PublicUser/GetMpoInfo?MerchantId=000003&token='.$rd->_token.'&PDSID='.$pdsid,
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
                    $ur = $response;
                    $rud = json_decode($response);
                    // return $rud->DateOfBirth;
                    if($rud->DateOfBirth==null){
                        return response()->json(['status'=>0,'data'=>''],200);
                    }
                    $date_pi = explode("/", $rud->DateOfBirth);
                    $final_dateof = $date_pi[2].'-'.$date_pi[1].'-'.$date_pi[0];
                    // $rud_dob_db_m = DateTime::createFromFormat('m/d/Y', $rud->DateOfBirth);
                    
                    // $rud_dob_db = date('Y-m-d', strtotime($rud_dob_db_m->date));
                    // return response()->json(['oo'=>8,'status'=>0,'data'=>$final_dateof],200);
                    if($rud->PDSID==$pdsid && $final_dateof==$date_of_birth){
                        // if($step==2){
                            $curl = curl_init();
    
                            curl_setopt_array($curl, array(
                            CURLOPT_URL => 'http://emis.gov.bd/sso/Services/Security/PublicUser/GetInstituteInfo?token='.$rd->_token.'&MerchantId=000003&EIIN='.$rud->EIIN,
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
                            $insinfo = json_decode($response);
                            if(isset($insinfo->InstituteInfoDataList[0])){
                                $EmisInfo->DivisionId = $insinfo->InstituteInfoDataList[0]->DivisionId;
                                $EmisInfo->DistrictId = $insinfo->InstituteInfoDataList[0]->DistrictId;
                                $EmisInfo->UpazillaId = $insinfo->InstituteInfoDataList[0]->UpazillaId;
                                $EmisInfo->EIIN = $insinfo->InstituteInfoDataList[0]->EIIN;
                                $EmisInfo->InstituteName = $insinfo->InstituteInfoDataList[0]->InstituteName;
                                $EmisInfo->InstituteType = $insinfo->InstituteInfoDataList[0]->InstituteType;
                                $EmisInfo->Address = $insinfo->InstituteInfoDataList[0]->Address;
                                // $EmisInfo->BranchInstituteCategory = $insinfo->InstituteInfoDataList[0]->BranchInstituteCategory;
                                // $EmisInfo->InstituteCategory = $insinfo->InstituteInfoDataList[0]->InstituteCategory;
                                $EmisInfo->Name = $rud->Name;
                                $EmisInfo->get_from = 1;
                                
                                $EmisInfo->Designation = $rud->Designation;
                                $EmisInfo->DateOfBirth = $rud->DateOfBirth;
                                $EmisInfo->pdsid = $pdsid;
                                $EmisInfo->type = 1;
                                if(Auth::user()){
                                    $EmisInfo->user_id = $user_id;
                                }
                                $EmisInfo->intitute_info = json_encode($insinfo);
                                $EmisInfo->user_info = json_encode($rud);
                                $EmisInfo->Subject = $rud->Subject;
                                $EmisInfo->save();
                            // }
                                if(Auth::user()){
                                    $user->pdsid = $pdsid;
                                    $user->certificate_name = $rud->Name;
                                    $user->emis_otp = rand(10000,99999);
                                    $user->update();
                                }
                                return response()->json(['status'=>100,'data'=>$EmisInfo],200);
                            }else{
                                return response()->json(['status'=>0,'data'=>''],200);
                            }
                        
                            
                            // return $EmisInfo;
                        
                        // }else{
                        //     return response()->json(['status'=>100,'data'=>$response],200);
                        // }
                        
                        // $this->send($rud->MobileNo,'EMIS test verification otp '.$User->emis_otp);
                        
    
                    }else{
                        // return 0;
                        return response()->json(['status'=>0,'data'=>''],200);
                    }
                }else{
                    // return 0;
                    return response()->json(['status'=>0,'data'=>''],200);
                }
            }
    
            if($pds_type==2){
                // return $pdsid;
                $dsheGet = DB::connection('course')->table('nfc_dme_data')->where('index_no',$pdsid)->first();
                if($dsheGet){
                    //  $date_dob = DateTime::createFromFormat('Y-m-d', $date_of_birth);
                     
                     $date_dob_db = DateTime::createFromFormat('m/d/Y', $dsheGet->dob);
                    // return $date_dob; 
                    $final_date_dob = $date_of_birth;
                    $final_date_dob_db = $date_dob_db->format('Y-m-d');
                    if($final_date_dob_db==$final_date_dob){
                        
                        
                            $EmisInfo->DivisionId = $dsheGet->division_id;
                            $EmisInfo->DistrictId = $dsheGet->zilla_id;
                            $EmisInfo->UpazillaId = $dsheGet->upazilla_id;
                            $EmisInfo->EIIN = $dsheGet->eiin;
                            $EmisInfo->InstituteName = $dsheGet->ins_name;
                            // $EmisInfo->InstituteType = $dsheGet->InstituteCategory;
                            // $EmisInfo->Address = $insinfo->InstituteInfoDataList[0]->Address;
                            // $EmisInfo->BranchInstituteCategory = $insinfo->InstituteInfoDataList[0]->BranchInstituteCategory;
                            // $EmisInfo->InstituteCategory = $insinfo->InstituteInfoDataList[0]->InstituteCategory;
                            $EmisInfo->Name = $dsheGet->employee_name;
                            // $EmisInfo->Designation = $dsheGet->Designation;
                            $EmisInfo->DateOfBirth = $dsheGet->dob;
                            $EmisInfo->pdsid = $pdsid;
                            $EmisInfo->type = 2;
                            if(Auth::user()){
                                $EmisInfo->user_id = $user_id;
                            }
                            // $EmisInfo->intitute_info = json_encode($insinfo);
                            // $EmisInfo->user_info = json_encode($rud);
                            // $EmisInfo->Subject = $dsheGet->Subject;
                            $EmisInfo->save();
                        // }
                        if(Auth::user()){
                            $user->pdsid = $pdsid;
                            $user->certificate_name = $dsheGet->employee_name;
                            $user->emis_otp = rand(10000,99999);
                            $user->update();  
                        }
                        // else{
                        //     return response()->json(['status'=>100,'data'=>$dsheGet],200);
                        // }
                        DB::commit();
                        return response()->json(['status'=>100,'data'=>$EmisInfo],200);
                    }else{
                        return response()->json(['status'=>0,'data'=>''],200);
                    }
                }else{
                    return response()->json(['status'=>0,'data'=>''],200);
                }
            }
            if($pds_type==3){
                // return $pdsid;
                $dsheGet = DB::connection('course')->table('dte_teacher_infos')->where('hrmis_id',$pdsid)->first();
                if($dsheGet){
                    // return $dsheGet->dob;
                    // $p_date = DateTime::createFromFormat('Y-m-d', $dsheGet->dob);
                  
                    // $p_date = date('Y-m-d', strtotime($date_of_birth));
                    $date_pi = explode("/", $dsheGet->dob);
                    if(!isset($date_pi[1])){
                        $date_pi = explode("-", $dsheGet->dob);
                        if(!isset($date_pi[1])){
                            $date_pi = explode(".", $dsheGet->dob);
                        }
                    }
                    $date_dob_db = $date_pi[2].'-'.((int)$date_pi[1]<10?'0'.(int)$date_pi[1]:$date_pi[1]).'-'.((int)$date_pi[0]<10?'0'.(int)$date_pi[0]:$date_pi[0]);
                    
                    // return response()->json(['status'=>0,'data'=>$date_of_birth,'a'=>$date_dob_db],200);
                    if($date_of_birth==$date_dob_db){
                        
                        
                            $EmisInfo->DivisionId = $dsheGet->division_id;
                            $EmisInfo->DistrictId = $dsheGet->district_id;
                            $EmisInfo->UpazillaId = $dsheGet->upazilla_id;
                            $EmisInfo->EIIN = $dsheGet->eiin;
                            $EmisInfo->InstituteName = $dsheGet->inst_name;
                            // $EmisInfo->InstituteType = $dsheGet->InstituteCategory;
                            // $EmisInfo->Address = $insinfo->InstituteInfoDataList[0]->Address;
                            // $EmisInfo->BranchInstituteCategory = $insinfo->InstituteInfoDataList[0]->BranchInstituteCategory;
                            // $EmisInfo->InstituteCategory = $insinfo->InstituteInfoDataList[0]->InstituteCategory;
                            $EmisInfo->Name = $dsheGet->name;
                            // $EmisInfo->Designation = $dsheGet->Designation;
                            $EmisInfo->DateOfBirth = $dsheGet->dob;
                            $EmisInfo->pdsid = $pdsid;
                            $EmisInfo->type = 3;
                            if(Auth::user()){
                                $EmisInfo->user_id = $user_id;
                            }
                            // $EmisInfo->intitute_info = json_encode($insinfo);
                            // $EmisInfo->user_info = json_encode($rud);
                            // $EmisInfo->Subject = $dsheGet->Subject;
                            $EmisInfo->save();
                        // }
                        if(Auth::user()){
                            $user->pdsid = $pdsid;
                            $user->certificate_name = $dsheGet->name;
                            $user->emis_otp = rand(10000,99999);
                            $user->update();  
                        }
                        // else{
                        //     return response()->json(['status'=>100,'data'=>$dsheGet],200);
                        // }
                        DB::commit();
                        return response()->json(['status'=>100,'data'=>$EmisInfo],200);
                    }else{
                        return response()->json(['status'=>0,'data'=>''],200);
                    }
                }else{
                    return response()->json(['status'=>0,'data'=>''],200);
                }
            }
            if($pds_type==4){
                // return $pdsid;
                $dsheGet = DB::connection('course')->table('primary_teaceher')->where('PIN_NO',$pdsid)->first();
    
                if($dsheGet){
                    $date_pi = explode("/", $dsheGet->DOB);
                    if(!isset($date_pi[1])){
                        $date_pi = explode("-", $dsheGet->DOB);
                    }
                    
                    $date_dob_db = (int)$date_pi[2].'-'.((int)$date_pi[1]<10?'0'.(int)$date_pi[1]:$date_pi[1]).'-'.((int)$date_pi[0]<10?'0'.(int)$date_pi[0]:$date_pi[0]);
                    //  $date_dob_db = date('Y-m-d', strtotime($dsheGet->DOB));
                    // return $date_of_birth.'---'.$date_dob_db.'---'.$date_pi[2].'--'.$date_pi[1];
                    if($date_of_birth==$date_dob_db){
                        
                        
                            $EmisInfo->DivisionId = $dsheGet->division_id;
                            $EmisInfo->DistrictId = $dsheGet->district_id;
                            // $EmisInfo->UpazillaId = $dsheGet->upazilla_id;
                            // $EmisInfo->EIIN = $dsheGet->eiin;
                            $EmisInfo->InstituteName = $dsheGet->SCHOOL;
                            // $EmisInfo->InstituteType = $dsheGet->InstituteCategory;
                            // $EmisInfo->Address = $insinfo->InstituteInfoDataList[0]->Address;
                            // $EmisInfo->BranchInstituteCategory = $insinfo->InstituteInfoDataList[0]->BranchInstituteCategory;
                            // $EmisInfo->InstituteCategory = $insinfo->InstituteInfoDataList[0]->InstituteCategory;
                            $EmisInfo->Name = $dsheGet->T_NAME;
                            // $EmisInfo->Designation = $dsheGet->Designation;
                            $EmisInfo->DateOfBirth = $dsheGet->DOB;
                            $EmisInfo->pdsid = $pdsid;
                            $EmisInfo->type = 4;
                            if(Auth::user()){
                                $EmisInfo->user_id = $user_id;
                            }
                            // $EmisInfo->intitute_info = json_encode($insinfo);
                            // $EmisInfo->user_info = json_encode($rud);
                            // $EmisInfo->Subject = $dsheGet->Subject;
                            $EmisInfo->save();
                        // }
                        if(Auth::user()){
                            $user->pdsid = $pdsid;
                            $user->certificate_name = $dsheGet->T_NAME;
                            $user->emis_otp = rand(10000,99999);
                            $user->update();  
                        }
                        // else{
                        //     return response()->json(['status'=>100,'data'=>$dsheGet],200);
                        // }
                        DB::commit();
                        return response()->json(['status'=>100,'data'=>$EmisInfo],200);
                    }else{
                        return response()->json(['status'=>0,'data'=>''],200);
                    }
                }else{
                    return response()->json(['status'=>0,'data'=>''],200);
                }
            }
        }
        catch(\Exception $e){
            DB::rollBack();
            $data = [
                'status'  => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($data, 404);
        }

    }

}