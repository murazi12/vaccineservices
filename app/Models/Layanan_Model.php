<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helper\ResponseBuilder;

class Layanan_Model extends Model
{
    protected $table = 'tbl_layanan';
    protected $primaryKey = 'lay_id';
    protected $dateFormat = 'Y-m-d H:i:s.uO';

    const CREATED_AT = 'lay_addon';
    const UPDATED_AT = 'lay_chgon';

    function __construct() {
        parent::__construct();
    }
}