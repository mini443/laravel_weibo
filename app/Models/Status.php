<?php
/**
 * Created by PhpStorm.
 * User: gx
 * Date: 2019/4/5
 * Time: 17:47
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Status extends  Model
{
    protected $fillable = ['content'];
    public function user(){
        return $this->belongsTo(User::class);
    }

}