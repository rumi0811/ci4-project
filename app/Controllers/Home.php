<?php

namespace App\Controllers;

// use App\Controllers\BaseController;
use App\Controllers\MYController;
use App\Models\MProductCategory;
use App\Models\MUom;
use App\Models\MOutlet;
use App\Models\MSaleType;
use App\Models\MSales;
use App\Models\MCashier;

//class Home extends BaseController
class Home extends MYController
{
    protected $formName = 'form1';

    public function __construct()
    {
        // No parent constructor call needed in CI4
    }

    public function index()
    {
        // HTTPS redirect (if not localhost)
        if ((!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") && $_SERVER['SERVER_NAME'] != 'localhost') {
            $url = "https://" . $_SERVER['SERVER_NAME'] . "" . $_SERVER['REQUEST_URI'];
            return redirect()->to($url);
        }

        // Get company_id from session
        $companyId = session()->get('company_id');

        $data = [];
        $data['title'] = 'Dashboard - IKON POS';
        $data['currentPage'] = 'Dashboard';

        // ✅ GANTI BARIS INI:
        // $data["menu_generate"] = $this->generateMenu();  // ❌ HAPUS INI
        $data["menu_generate"] = $this->getTemporaryMenu();  // ✅ GANTI JADI INI

        // Create form filter
        $data['form'] = $this->createFormDashboard();
        $data['grid'] = '';

        // ✅ TAMBAH DEBUG:
        log_message('debug', '🔥 SEKAR Home: menu_generate type = ' . gettype($data['menu_generate']));
        log_message('debug', '🔥 SEKAR Home: menu_generate length = ' . strlen($data['menu_generate']));

        return view('dashboard/index', $data);
    }

    private function generateMenu()
    {
        // TODO: Implement dynamic menu from database
        // For now, return static menu
        return [];
    }

    private function createFormDashboard()
    {
        $companyId = session()->get('company_id');

        // Get outlets
        $mOutlet = new MOutlet();
        $arrOutlets = [];
        $arrOutletsValue = [];

        $cursorOutlets = $mOutlet->findAll(['company_id' => (int)$companyId, '_deleted' => ['$ne' => true]]);
        $arrDataOutlets = [];

        if ($cursorOutlets) {
            foreach ($cursorOutlets as $row) {
                $arrDataOutlets[] = $row;
                $arrOutlets[$row['outlet_id']] = $row['outlet_name'];
            }
            if (count($arrDataOutlets) == 1) {
                $arrOutletsValue[] = $arrDataOutlets[0]['outlet_id'];
            }
        }

        // Get filter params from session
        $dataParams = session()->get('filter_dashboard');

        if (isset($dataParams['outlets'])) {
            $arrOutletsValue = $dataParams['outlets'];
        }

        // Default period: today
        $period = date('Y-m-d');
        if (isset($dataParams['period'])) {
            $period = $dataParams['period'];
            if (stripos($period, 'invalid date') !== false) {
                $period = date('Y-m-d');
            }
        }

        // Build form HTML with correct IDs matching JavaScript
        $form = '<form id="form1" class="mb-3">';
        $form .= '<div class="row">';
        $form .= '<div class="col-md-8">';
        $form .= '<select name="outlets[]" id="outlets_form1" class="form-control" multiple>';
        foreach ($arrOutlets as $id => $name) {
            $selected = in_array($id, $arrOutletsValue) ? 'selected' : '';
            $form .= '<option value="' . $id . '" ' . $selected . '>' . $name . '</option>';
        }
        $form .= '</select>';
        $form .= '</div>';
        $form .= '<div class="col-md-4">';
        $form .= '<div class="input-group">';
        $form .= '<span class="input-group-text"><button type="button" class="btn btn-prev-date"><i class="fal fa-chevron-left"></i></button></span>';
        $form .= '<input type="text" name="period" id="period_form1" class="form-control" value="' . $period . '" />';
        $form .= '<span class="input-group-text"><button type="button" class="btn btn-next-date"><i class="fal fa-chevron-right"></i></button></span>';
        $form .= '</div>';
        $form .= '</div>';
        $form .= '</div>';
        $form .= '</form>';

        return $form;
    }

    public function datatable()
    {
        $companyId = session()->get('company_id');
        $userId = session()->get('user_id');

        $reportType = 'Dashboard';
        $outlets = $this->request->getPost('outlets');
        $period = $this->request->getPost('period');

        $reportParams = [
            'report_type' => $reportType,
            'outlets' => $outlets,
            'period' => $period
        ];

        // Cache check (simplified - TODO: implement proper caching)
        $cacheKey = 'dashboard_' . $userId;

        // Save filter to session
        session()->set('filter_dashboard', $reportParams);

        // Parse period
        $arrPeriod = explode(' - ', $period);
        if (count($arrPeriod) == 2) {
            $date = new \DateTime();
            $date->setTimestamp(strtotime($arrPeriod[0]));
            $date->setTimezone(new \DateTimeZone("UTC"));
            $dateFrom = $date->format('Y-m-d\TH:i\:00.000\Z');

            $date->setTimestamp(strtotime($arrPeriod[1] . ' 23:59:59'));
            $dateThru = $date->format("Y-m-d\TH:i\:59.999\Z");
        } else {
            // Single date
            $date = new \DateTime();
            $date->setTimestamp(strtotime($arrPeriod[0]));
            $date->setTimezone(new \DateTimeZone("UTC"));
            $dateFrom = $date->format('Y-m-d\TH:i\:00.000\Z');
            $date->setTimestamp(strtotime($arrPeriod[0]) + 86400 - 1);
            $dateThru = $date->format("Y-m-d\TH:i\:59.999\Z");
        }

        // Convert outlets to int
        if (is_array($outlets)) {
            foreach ($outlets as &$o) {
                $o = intval($o);
            }
            unset($o);
        }

        // Get sales data
        $mSales = new MSales();
        $arrCriteria = [];
        $arrCriteria['company_id'] = $companyId;

        if ($outlets) {
            $arrCriteria['outlet_id']['$in'] = $outlets;
        }

        $arrCriteria['_deleted']['$ne'] = true;
        $arrCriteria['paid_date']['$gte'] = $dateFrom;
        $arrCriteria['paid_date']['$lte'] = $dateThru;
        $arrCriteria['sales_status'] = 1;

        $cursorResult = $mSales->findAll($arrCriteria);

        // Convert cursor to array
        $arrResult = [];
        if ($cursorResult) {
            foreach ($cursorResult as $row) {
                $arrResult[] = $row;
            }
        }

        // Initialize arrays
        $arrCharts1 = [];
        $arrCharts2 = [];
        $arrSummary = [
            'qty_transaction' => 0,
            'grand_total' => 0,
            'discount_total' => 0,
            'sub_total' => 0
        ];

        if ($arrResult) {
            // Get outlets for display
            $mOutlet = new MOutlet();
            $arrOutlets = [];
            $cursorDataOutlets = $mOutlet->findAll(['company_id' => $companyId]);
            $arrDataOutlets = [];

            if ($cursorDataOutlets) {
                foreach ($cursorDataOutlets as $row) {
                    $arrDataOutlets[] = $row;
                    $arrOutlets[$row['outlet_id']] = $row['outlet_name'];
                }
            }

            // Get cashiers
            $mCashier = new MCashier();
            $cursorCashiers = $mCashier->findAllByCompanyId($companyId);
            $cashiersById = [];
            if ($cursorCashiers) {
                foreach ($cursorCashiers as $c) {
                    $cashiersById[$c['cashier_id']] = $c;
                }
            }

            foreach ($arrResult as &$row) {
                $row['_id'] = (string)$row['_id'];
                $ts = strtotime($row['sales_time']);
                $row['sales_date'] = date('d/m/Y', $ts);

                // Calculate duration
                $duration = strtotime($row['paid_date']) - $ts;
                $hours = floor($duration / 3600);
                $minutes = floor(($duration % 3600) / 60);
                $seconds = $duration % 60;
                $row['duration'] = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

                // Status
                if ($row['sales_status'] == 0) {
                    $row['status_text'] = 'Baru/Open';
                    $row['status_class'] = 'primary';
                } else if ($row['sales_status'] == 1) {
                    $row['status_text'] = 'Paid';
                    $row['status_class'] = 'success';
                } else if ($row['sales_status'] == -1) {
                    $row['status_text'] = 'Dibatalkan';
                    $row['status_class'] = 'danger';
                }

                $row['sales_time_only'] = date('H:i:s', $ts);

                // Summary
                $arrSummary['qty_transaction']++;
                $arrSummary['grand_total'] += $row['grand_total'];
                $arrSummary['discount_total'] += $row['discount_total'];
                $arrSummary['sub_total'] += $row['sub_total'];

                // Outlet name
                if (isset($arrOutlets[$row['outlet_id']])) {
                    $row['outlet_name'] = $arrOutlets[$row['outlet_id']];
                }

                // Cashier name
                if (isset($cashiersById[$row['cashier_id']])) {
                    $row['cashier_name'] = $cashiersById[$row['cashier_id']]['name'];
                }

                // Chart data - by day of week
                $dow = intval(date('w', $ts));
                if (!isset($arrCharts1[$dow])) {
                    $arrCharts1[$dow] = ['grand_total' => 0, 'qty_transaction' => 0];
                }
                $arrCharts1[$dow]['grand_total'] += $row['grand_total'];
                $arrCharts1[$dow]['qty_transaction']++;

                // Chart data - by hour
                $h = intval(date('H', $ts));
                if (!isset($arrCharts2[$h])) {
                    $arrCharts2[$h] = ['grand_total' => 0, 'qty_transaction' => 0];
                }
                $arrCharts2[$h]['grand_total'] += $row['grand_total'];
                $arrCharts2[$h]['qty_transaction']++;
            }
            unset($row);
        }

        $isHasData = !empty($arrResult);

        // Prepare chart data
        $dataCharts = [];

        // Day of week chart
        if ($arrCharts1) {
            $arrDOW = [0 => 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $dataCharts['dow'] = [];
            $isHasValue = false;

            foreach ($arrDOW as $dow => $dowName) {
                $grandTotal = $arrCharts1[$dow]['grand_total'] ?? 0;
                $qtyTransaction = $arrCharts1[$dow]['qty_transaction'] ?? 0;

                if ($qtyTransaction > 0) {
                    $isHasValue = true;
                }

                $dataCharts['dow']['categories'][] = $dowName;
                $dataCharts['dow']['grand_total'][] = $grandTotal;

                if ($qtyTransaction == 0) {
                    $qtyTransaction = ['y' => 0, 'marker' => ['enabled' => false]];
                } else {
                    $qtyTransaction = ['y' => $qtyTransaction];
                }
                $dataCharts['dow']['qty_transaction'][] = $qtyTransaction;
            }
        }

        // Hourly chart
        if ($arrCharts2) {
            $dataCharts['hourly'] = [];
            $isHasValue = false;

            for ($i = 0; $i < 24; $i++) {
                $grandTotal = $arrCharts2[$i]['grand_total'] ?? 0;
                $qtyTransaction = $arrCharts2[$i]['qty_transaction'] ?? 0;

                if ($qtyTransaction > 0) {
                    $isHasValue = true;
                }

                $dataCharts['hourly']['categories'] = $i;
                $dataCharts['hourly']['grand_total'][] = $grandTotal;

                if ($qtyTransaction == 0) {
                    $qtyTransaction = ['y' => 0, 'marker' => ['enabled' => false]];
                } else {
                    $qtyTransaction = ['y' => $qtyTransaction];
                }
                $dataCharts['hourly']['qty_transaction'][] = $qtyTransaction;
            }
        }

        $arrResultJson = [
            'data' => $arrResult,
            'summary' => $arrSummary,
            'charts' => $dataCharts,
            'has_data' => $isHasData
        ];

        // TODO: Implement caching

        return $this->response->setJSON($arrResultJson);
    }
}
