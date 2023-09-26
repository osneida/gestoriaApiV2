<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PayrollPeriod
 *
 * @property int $id
 * @property int $user_id
 * @property string $file Archivo de S3 procesado
 * @property string $period Periodo procesado nóminas
 * @property string|null $processed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod whereProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod whereUserId($value)
 * @mixin \Eloquent
 * @property int $company_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PayrollPeriod whereCompanyId($value)
 */
class PayrollPeriod extends Model
{
    protected $guarded = ["id"];
}
