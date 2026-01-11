<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionReport extends Model
{
    use HasFactory;

    protected $table = 'subscription_reports';

    protected $fillable = [
        'subscription_id',
        'period',
    ];

    // Sub
    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    // Prestamos
    public function loans()
    {
        return $this->hasMany(ReportLoan::class, 'subscription_report_id');
    }

    // Otras deudas
    public function otherDebts()
    {
        return $this->hasMany(ReportOtherDebt::class, 'subscription_report_id');
    }

    // Tarjetas 
    public function creditCards()
    {
        return $this->hasMany(ReportCreditCard::class, 'subscription_report_id');
    }
}
