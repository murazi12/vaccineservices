<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helper\ResponseBuilder;

class City_Model extends Model
{
    protected $table = 'tbl_city';
    protected $primaryKey = 'cit_id';
    protected $dateFormat = 'Y-m-d H:i:s.uO';

    const CREATED_AT = 'cit_addon';
    const UPDATED_AT = 'cit_chgon';

    function __construct() {
        parent::__construct();
    }
}
