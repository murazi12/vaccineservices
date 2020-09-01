<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helper\ResponseBuilder;

class Hospital_Model extends Model
{
    protected $table = 'tbl_hospital';
    protected $primaryKey = 'hos_id';
    protected $dateFormat = 'Y-m-d H:i:s.uO';

    const CREATED_AT = 'hos_addon';
    const UPDATED_AT = 'hos_chgon';

    function __construct() {
        parent::__construct();
    }
}