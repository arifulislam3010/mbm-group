<?php
namespace Muktopaath\Course\Lib;

trait CourseEnrollment
{
	public function getCourseBatch($url,$header)
	{
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
		$res = curl_exec($ch);
		if($e=curl_error($ch)){
			echo $e;
		}else{
			$decoded = json_decode($res);
		}
		curl_close($ch);
		var_dump($res);
		return $decoded;
	}
//get access or course batch enroll
	public function addCourseEnroll($url,$data)
	{
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$res = curl_exec($ch);
		if($e=curl_error($ch)){
			echo $e;
		}else{
			$decoded = json_decode($res);
		}
		curl_close($ch);
		return $decoded;

	}


	public function CourseSolrDataUpdate(){

        $ch = curl_init();

        //$body = Request()->all();

        curl_setopt($ch, CURLOPT_URL,"https://searchapi.muktopaath.gov.bd/solr/demo/dataimport");
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $body['command'] = 'full-import';
        $body['verbose'] = 'false';
        $body['clean'] = 'true';
        $body['commit'] = 'true';
        $body['core'] = 'demo';
        $body['name'] = 'dataimport';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        // In real life you should use something like:
        // curl_setopt($ch, CURLOPT_POSTFIELDS, 
        //          http_build_query(array('postvar1' => 'value1')));

        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        return $server_output;
		return true;

	}
}
