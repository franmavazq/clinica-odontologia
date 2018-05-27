<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /** Niveles de los usuarios */
    const LEVEL_ADMIN = 1;
    const LEVEL_DOCTOR = 2;
    const LEVEL_SECRETARY = 3;
    const LEVEL_ASSISTANT = 4;

    /** Comision por defecto de ganancia por servicio */
    const DEFAULT_PRODUCT_COMMISSION = 30;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'logo',
        'business_name',
        'email',
        'address',
        'phone',
        'level'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Indica si un usuario es administrador
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->level === self::LEVEL_ADMIN;
    }

    /**
     * Indica si el usuario es doctor
     *
     * @return bool
     */
    public function isDoctor()
    {
        return $this->level === self::LEVEL_DOCTOR;
    }

    /**
     * Indica si el usuario es secretaria
     *
     * @return bool
     */
    public function isSecretary()
    {
        return $this->level === self::LEVEL_SECRETARY;
    }

    /**
     * Indica si el usuario es asistente
     *
     * @return bool
     */
    public function isAssistant()
    {
        return $this->level === self::LEVEL_ASSISTANT;
    }

    /**
     * Todos los servicios brindados por un Doctor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistory()
    {
        return $this->hasMany(PatientHistory::class, 'doctor_id');
    }

    /**
     * Todos los servicios asistidos por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patientHistoryAssistant()
    {
        return $this->hasMany(PatientHistory::class, 'assistant_id');
    }

    /**
     * Todas las solicitudes de insumos generadas por el usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplyRequests()
    {
        return $this->hasMany(SupplyRequest::class, 'user_id');
    }

    /**
     * Todas las notas escritas por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    /**
     * Todos los pagos registrados por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_created_id');
    }

    /**
     * Todas las preguntas hechas a este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionsTo()
    {
        return $this->hasMany(Question::class, 'to_id');
    }

    /**
     * Todas las preguntas formuladas por este usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionsFrom()
    {
        return $this->hasMany(Question::class, 'from_id');
    }

    /**
     * Todas las comisiones de productos configuradas
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function commissionProducts()
    {
        return $this->belongsToMany(Product::class, 'product_user', 'user_id', 'product_id')
            ->withPivot(['commission']);
    }

    /**
     * Genera el id publico en base al nivel
     */
    public function generatePublicId()
    {
        if ($this->isAdmin()) {
            $this->public_id = 'ADM' . time();
        } elseif ($this->isDoctor()) {
            $this->public_id = 'DOC' . time();
        } elseif ($this->isSecretary()) {
            $this->public_id = 'SEC' . time();
        } elseif ($this->isAssistant()) {
            $this->public_id = 'ASS' . time();
        }
    }

    /**
     * Indica si un usuario tiene permiso de acceder a una zona
     *
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if ($this->level === self::LEVEL_ADMIN) {
            return true;
        }

        if ($this->level === self::LEVEL_DOCTOR && in_array($permission, [
                'product.create',
                'product.index',
                'patient.index',
                'patient.create',
                'budget.index',
                'budget.create',
                'service.search',
                'service.create',
                'supplyRequest.create',
                'question.index'
            ])) {
            return true;
        }

        if ($this->level === self::LEVEL_ASSISTANT && in_array($permission, [
                'budget.index',
                'service.search',
                'supplyRequest.create'
            ])) {
            return true;
        }

        if ($this->level === self::LEVEL_SECRETARY && in_array($permission, [
                'product.index',
                'patient.index',
                'budget.index',
                'service.search',
                'callLog.index',
                'supplyRequest.create',
                'payment.create',
                'expense.create',
                'expense.index',
                'supplier.create',
                'supplier.index'
            ])) {
            return true;
        }

        return false;
    }
}
