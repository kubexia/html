<?php namespace App\Models\Settings\Translation;

use Illuminate\Database\Eloquent\Model;

class Section extends Model{
    
    protected $table = 'settings_translations_sections';
    
    protected $fillable = ['name','filename'];
    
}