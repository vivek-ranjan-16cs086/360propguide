<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
	use SoftDeletes;
	
	public function getTypologyTextAttribute()
    {
        if (empty($this->typology)) {
            return 'N/A';
        }

        if (is_array($this->typology)) {
            return implode(', ', $this->typology);
        }

        $decoded = json_decode($this->typology, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return implode(', ', $decoded);
        }

        return implode(', ', array_filter(array_map('trim', explode(',', $this->typology))));
    }
	
	
	
	
}
