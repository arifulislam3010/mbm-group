<?php

namespace App\Interfaces\Myaccount;

interface InstitutionRepositoryInterface 
{
    public function  index();
    public function show(int $id);
    public function all();
    public function unapproved(array $request);
    public function types();
    public function approve(array $request, int $id);
    public function autoApprove(int $id);
    public function create(array $request);
    public function create_partner(array $request,int $id);
    public function update(array $request);
    public function createIns(array $request);
    public function sendPassword(int $id);
    // public function delete(int $id);
}