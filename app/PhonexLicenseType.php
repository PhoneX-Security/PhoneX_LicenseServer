<?php namespace Phonex;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Database\Eloquent\Model;

class PhonexLicenseType extends Model{
    protected $table = "phonex_license_types";
    
    public function licenses(){
        return $this->hasMany('PhonexLicenses', 'license_type_id');
    }    
}
