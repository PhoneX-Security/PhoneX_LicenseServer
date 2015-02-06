<?php namespace Phonex;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Illuminate\Database\Eloquent\Model;

class PhonexLicense extends Model{
    protected $table = "licenses";
    
    public function users(){
        return $this->belongsTo('PhonexUser', 'user_id');        
    }
    
    public function licenseTypes() {
        return $this->belongsTo('PhonexLicenseType', 'license_type_id');
    }

}
