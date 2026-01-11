<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MSales extends MyMongoModel
{
    public $fieldStructure = [
        'sales_id' => 'int', //PK
        'client_id' => 'string', //ID dari RxDb client
        'company_id' => 'int', //FK
        'currency_id' => 'int', //FK
        'outlet_id' => 'int', //FK
        'customer_id' => 'int', //FK
        'cashier_id' => 'int', //FK
        'table_id' => 'int', //FK
        'sales_number' => 'string',
        'sales_time' => 'datetime',
        'paid_date' => 'datetime',
        'invoice_number' => 'string',
        'sales_note' => 'string',
        'sales_status' => 'int', // 0 = new, 1 = paid, -1 = cancel
        'sales_price' => 'float',
        'paid_amount' => 'float',
        'change_amount' => 'float',
        'discount_percentage' => 'float',
        'discount_amount' => 'float',
        'discount_total' => 'float',
        'sub_total' => 'float',
        'tax_total' => 'float',
        'rounding_amount' => 'float',
        'grand_total' => 'float',

        'sale_type_id' => 'int',
        'sale_type' => 'string',

        //array of payment_type_id, payment_type and total_amount
        'payment_types' => 'array',

        // array of:
        // product_id, picture, barcode, product_code, product_name, description, note, 
        // qty, cogs_price, sale_price, discount_amount, discount_percentage, discount_total, total_amount
        'products' => 'array',

        // array of:
        // tax_id, tax_code, tax_percentage, sequence_no, total_amount
        'taxes' => 'array',

        'customer_name' => 'string', //manual entry
        'table_number' => 'string', //manual entry

        'trans_number' => 'string',
        'journal_number' => 'string',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',

        'cashier_code' => 'string', //ini referensi dari table m_cashier, di client mobile tidak perlu ada
        'cashier_name' => 'string', //ini referensi dari table m_cashier, di client mobile tidak perlu ada
        'outlet_name' => 'string', //ini referensi dari table m_outlet, di client mobile tidak perlu ada
        'currency_code' => 'string', //ini referensi dari table m_currency, di client mobile tidak perlu ada

        'updatedAt' => 'float', //timestamp
        '_deleted' => 'boolean',
    ];

    public function __construct()
    {
        parent::__construct("m_sales", "sales_id");
    }
}
