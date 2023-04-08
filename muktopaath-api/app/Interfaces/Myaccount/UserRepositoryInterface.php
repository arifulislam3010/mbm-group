<?php

namespace App\Interfaces\Myaccount;

interface UserRepositoryInterface 
{
    // public function getAllOrders();
    public function createUserBasics(array $userBasics);
    public function tokenhistory(array $request);
    public function createUserDetails($userId, array $userDetails);
    public function userInfo($path);
    public function authuserinfoById($id);
    public function authuserinfo();
    public function logtrack($user);
    public function upload_profile_photo(array $request);
    public function users();
    public function verifyBymail(array $request);
    public function verifyBySms(array $request);
    public function passwordResetCode(array $request);
    public function resendcodeverify($request);
    public function passwordResetCodeVerify(array $request);
    public function updatePassword(array $request);
}