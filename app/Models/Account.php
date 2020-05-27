<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Account extends Model
{
    /**
     * @var string
     */
    protected $table = 'accounts';

    /**
     * @var array
     */
    protected $guarded = [];


    // account operations
    public function incrementBalance($value){
        $this->balance = $this->balance + $value;
    }

    public function decrementBalance($value){
        $this->balance = $this->balance - $value;
    }

    // handle users

    /**
     * @return BelongsTo
     *
     */
    public function user(){
        return $this->belongsTo('App\Models\User');
    }


    // handle transactions

    /**
     * @return mixed
     */
    public function transactions() {
        return $this->hasMany(Transaction::class, 'sender_account_id');
    }

    // Handle loans

    /**
     * @return bool
     */
    public function hasActiveLoan(){
        $active_loan = Loan::where('recipient_user_id', $this->id)
            ->where('is_active', true);

        if (count($active_loan) > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return HasOne
     */
    public function loan() {
        return $this->hasOne(Loan::class);
    }

    public function receivedLoans(){
        return $this->hasMany(Transaction::class, 'recipient_account_id');
    }

}

