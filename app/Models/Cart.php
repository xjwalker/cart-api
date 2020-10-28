<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cart
 * @package App\Models
 *
 * @property int id
 * @property array products
 * @property double total
 */
class Cart extends Model
{
    use HasFactory;
}
