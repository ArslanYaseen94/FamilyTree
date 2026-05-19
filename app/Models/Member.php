<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use Notifiable;

    protected $table = 'tbl_members';
    
    protected $fillable = [
        'family_id',
        'parent_id',
        'firstname',
        'lastname',
        'type',
        'generation',
        'gender',
        'death',
        'village',
        'birthdate',
        'marriagedate',
        'deathdate',
        'user',
        'photo',
        'avatar',
        'facebook',
        'twitter',
        'instagram',
        'email',
        'tel',
        'mobile',
        'site',
        'birthplace',
        'deathplace',
        'profession',
        'company',
        'interests',
        'bio',
        'images',
        'home_town',
        'school',
        'background',
        'business_info',
    ];
    
    // Define relationships based on 'type'
    public function children()
    {
        return $this->hasMany(Member::class, 'parent_id')->where('type', 1);
    }

    public function partners()
    {
        return $this->hasMany(Member::class, 'parent_id')->where('type', 2);
    }

    public function exPartners()
    {
        return $this->hasMany(Member::class, 'parent_id')->where('type', 3);
    }

    public function parent()
    {
        return $this->belongsTo(Member::class, 'parent_id')->where('type', 4);
    }
        public function family()
    {
        return $this->belongsTo(FamilyTree::class, 'family_id');
    }

    /**
     * Age at death: full years, else full months, else total days between dates.
     */
    public function getAgeAtDeathFormatted(): ?string
    {
        if (!$this->birthdate || !$this->deathdate) {
            return null;
        }

        try {
            $birth = Carbon::parse($this->birthdate)->startOfDay();
            $death = Carbon::parse($this->deathdate)->startOfDay();

            if ($death->lt($birth)) {
                return null;
            }

            $years = (int) $birth->diffInYears($death);
            if ($years >= 1) {
                $label = $years === 1 ? __('messages.year') : __('messages.years');
                return $years . ' ' . $label;
            }

            $months = (int) $birth->diffInMonths($death);
            if ($months >= 1) {
                $label = $months === 1 ? __('messages.month') : __('messages.months');
                return $months . ' ' . $label;
            }

            $days = (int) $birth->diffInDays($death);
            $label = $days === 1 ? __('messages.day') : __('messages.days');

            return $days . ' ' . $label;
        } catch (\Exception $e) {
            return null;
        }
    }
}
