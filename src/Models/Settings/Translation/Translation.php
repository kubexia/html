<?php namespace App\Models\Settings\Translation;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model{
    
    protected $table = 'settings_translations';
    
    protected $fillable = ['parameter','value'];
    
}