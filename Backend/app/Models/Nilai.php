<?php

namespace App\Models;

use App\Models\Traits\HasUuid;

class Nilai extends ExtendedModel
{
    use HasUuid;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nilai';

    const RULES = [
        'nama' => ['required', 'string', 'max:255'],
        'nisn' => ['required', 'string', 'max:255'],
        'type' => ['required', 'string', 'in:RT,ST'],
        'verbal' => ['nullable', 'numeric'],
        'kuantitatif' => ['nullable', 'numeric'],
        'penalaran' => ['nullable', 'numeric'],
        'figural' => ['nullable', 'numeric'],
        'total' => ['nullable', 'numeric'],
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'verbal' => 'float',
        'kuantitatif' => 'float',
        'penalaran' => 'float',
        'figural' => 'float',
        'total' => 'float',
    ];
}
