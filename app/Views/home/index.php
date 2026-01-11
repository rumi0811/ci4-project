<style>
    span.input-group-text:has(button) {
        padding: 0px !important;
    }

    .card-summary {
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 20px;
        height: 100%;
    }

    .card-summary small {
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 600;
    }

    .card-summary b {
        display: block;
        font-size: 1.25rem;
        margin-top: 5px;
    }

    .highcharts-credits {
        display: none;
    }
</style>
<?php $this->load->view('includes/layout_breadcrumb'); ?>
<!-- 
<div class="row">
  <div class="col-sm-12">

    <div class="well">
      <fieldset>
        <legend>
          <h1>
            <small class="text-success slideInRight fast animated">
              <strong>Selamat datang <?php echo $this->session->userdata('name'); ?>!</strong></small>
          </h1>
        </legend>
      </fieldset>
      <div class="media">
        <div class="media-body">
          <p></p>
        </div>
      </div>
    </div>

  </div>
</div> -->

<?php echo $form; ?>
<?php echo $grid; ?>

<section id="DashboardPage">
    <h6 class="text-uppercase mb-2">Sales Summary</h6>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card-summary">
                <small>Transaction</small>
                <b v-html="data.summary.qty_transaction"></b>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card-summary">
                <small>Gross sales</small>
                <b v-html="formatCurrency(data.summary.sub_total)"></b>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card-summary">
                <small>Diskon</small>
                <b v-html="formatCurrency(data.summary.discount_total)"></b>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card-summary">
                <small>Net sales</small>
                <b v-html="formatCurrency(data.summary.grand_total)"></b>
            </div>
        </div>

        <!-- <div class="col-md-4 mb-3">
      <div class="card-summary">
        <small>Gross profit</small>
        <b v-html="formatCurrency(data.summary.gross_profit)"></b>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card-summary">
        <small>Transactions</small>
        <b v-html="data.summary.qty_transaction"></b>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card-summary">
        <small>Average Sale per Transaction</small>
        <b v-html="formatCurrency(data.summary.avg_sale)"></b>
      </div>
    </div>
    <div class="col-md-4 mb-3">
      <div class="card-summary">
        <small>Gross margin</small>
        <b v-html="data.summary.gross_margin"></b>
      </div>
    </div> -->
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card-summary">
                <div v-pre id="dayOfWeekChart" class="chart-container" aria-label="Bar chart representing gross sales by day of the week" role="img"></div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card-summary">
                <div v-pre id="hourlySalesChart" class="chart-container" aria-label="Line chart representing hourly gross sales amount" role="img"></div>
            </div>
        </div>
    </div>
</section>


<?php $this->load->view('includes/layout_modal_progress'); ?>

<script src="<?php echo base_url(); ?>assets/js/highcharts@12.3.0/highcharts.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue.global.js"></script>
<script type="text/javascript">
    const {
        createApp
    } = Vue;
    const reportApp = createApp({
        data() {
            return {
                data: {
                    report_no_data: false,
                    tabs: ['Dashboard'],
                    active_tab: 'Dashboard',
                    report: [],
                    report_summary: [],
                    selected_row: [],
                    summary: [],
                    detail: null,
                    report_parameter: [],
                }
            }
        },
        methods: {
            formatCurrency(value, currencyCode = 'IDR') {
                if (value == null) value = 0;
                return value.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: currencyCode,
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }
        }
    }).mount('#DashboardPage');
</script>


<script type="text/javascript">
    var tableDatagrid = null;
    $(document).ready(function() {

        $('.btn-prev-date').click(function() {
            var picker = $('#period_form1').data('daterangepicker');
            var format = picker.locale.format;
            picker.setStartDate(picker.startDate.clone().subtract(1, 'days'));
            picker.setEndDate(picker.endDate.clone().subtract(1, 'days'));
            if (picker.startDate.isSame(picker.endDate, 'day')) {
                $('#period_form1').val(picker.startDate.format(format));
            } else {
                $('#period_form1').val(picker.startDate.format(format) + ' - ' + picker.endDate.format(format));
            }
            SearchReport();
        });

        $('.btn-next-date').click(function() {
            var picker = $('#period_form1').data('daterangepicker');
            var format = picker.locale.format;
            picker.setStartDate(picker.startDate.clone().add(1, 'days'));
            picker.setEndDate(picker.endDate.clone().add(1, 'days'));
            if (picker.startDate.isSame(picker.endDate, 'day')) {
                $('#period_form1').val(picker.startDate.format(format));
            } else {
                $('#period_form1').val(picker.startDate.format(format) + ' - ' + picker.endDate.format(format));
            }
            SearchReport();
        });

        $('#period_form1').on('apply.daterangepicker', function(ev, picker) {
            console.log("Date changed!");
            console.log("Start:", picker.startDate.format(picker.locale.format));
            console.log("End:", picker.endDate.format(picker.locale.format));

            // Call your function
            SearchReport();
        });

        $('#outlets_form1').on('change', function() {
            SearchReport();
        });

        function myCustomFunction(start, end) {
            alert("New range: " + start.format('YYYY-MM-DD') + " to " + end.format('YYYY-MM-DD'));
        }

        SearchReport();
        // $('#btnSearch_form1').click();
    });

    var SearchReport = function() {
        var url = '<?php echo base_url(); ?>home/datatable';
        var params = $('#form1').serialize();
        reportApp.data.report_parameter[reportApp.data.active_tab] = params;
        ShowLoadingIndicator();
        var jqxhr = $.post(url, params, function(obj) {
                //console.log(data);
                if (typeof(obj.error_message) != 'undefined') {
                    toastr['error'](obj.error_message);
                } else {
                    reportApp.data.report_summary[reportApp.data.active_tab] = obj.summary;
                    reportApp.data.summary = obj.summary;
                    reportApp.data.selected_row[reportApp.data.active_tab] = -1;
                    if (obj.data && obj.hasOwnProperty('has_data')) {
                        if (obj.has_data) {
                            reportApp.data.report_no_data = false;
                            reportApp.data.report[reportApp.data.active_tab] = obj.data;
                            reloadGridData(reportApp.data.report[reportApp.data.active_tab]);
                            LoadDataCharts(obj.charts);
                            return;
                        }
                    }
                    console.log('No data');
                    reportApp.data.report_no_data = true;
                    reportApp.data.selected_row[reportApp.data.active_tab] = -1;
                    reportApp.data.detail = null;
                    delete reportApp.data.report[reportApp.data.active_tab];
                    reloadGridData([]);
                    LoadDataCharts(obj.charts);
                }

            })
            .fail(function() {

            })
            .always(function() {
                HideLoadingIndicator();
                // $("#modalLoadingInfo").css("display", "none");
            });
    };

    var reloadGridData = function(data) {
        if (tableDatagrid != null) {
            console.log('reload grid', data);
            tableDatagrid.clear();
            if (data != null) {
                tableDatagrid.rows.add(data);
                tableDatagrid.draw();
            } else {
                tableDatagrid.draw();
            }
            if (reportApp.data.selected_row[reportApp.data.active_tab] >= 0) {
                tableDatagrid.row(reportApp.data.selected_row[reportApp.data.active_tab]).select();
            } else {
                //   tableDatagrid.row(':eq(0)', {
                //       page: 'current'
                //   }).select(); // if using Select extension
            }
            tableDatagrid.columns.adjust().responsive.recalc();
        }
    }

    var initGrid = function(arrData) {
        //toastr['error']('Init grid');
        if (tableDatagrid == null) {
            //   var tableEl = $('#datagrid');

            //   var colCount = 6;
            //   var headerRow = '<thead><tr>';
            //   var footerRow = '<tfoot><tr>';
            //   for (var i = 0; i < colCount; i++) {
            //       headerRow += '<th></th>';
            //       footerRow += '<th></th>';
            //   }
            //   headerRow += '</tr></thead>';
            //   footerRow += '</tr></tfoot>';
            //   tableEl.append(headerRow);
            //   tableEl.append('<tbody></tbody>');
            //   tableEl.append(footerRow);
            //   console.log(tableEl);

            tableDatagrid = $('#datagrid').DataTable({
                fixedColumns: false,
                columnDefs: [{
                        targets: [0, 1],
                        visible: false
                    }, // hide grouped columns
                    {
                        responsivePriority: 10000,
                        targets: 4
                    },
                    {
                        responsivePriority: 10001,
                        targets: 5
                    }
                ],
                data: arrData, // or omit this and use HTML
                columns: [{
                        title: 'Nama Outlet',
                        data: "outlet_name"
                    },
                    {
                        title: 'Tanggal',
                        data: "sales_date"
                    },
                    {
                        title: 'Waktu',
                        data: "sales_time_only",
                        class: "text-center"
                    },
                    {
                        title: 'No Struk',
                        data: "sales_number",
                        class: "text-center",
                        render: function(data, type, row, meta) {
                            if (data == null) data = 0;
                            return String(data).padStart(4, '0');
                        }
                    },
                    {
                        title: 'Nama Kasir',
                        data: "cashier_name"
                    },
                    {
                        title: 'Produk',
                        data: "sales_keyword",
                        class: 'truncate'
                    },
                    {
                        title: 'Total',
                        data: "grand_total",
                        class: "text-right",
                        render: function(data, type, row, meta) {
                            if (data == null) data = 0;
                            return data.toLocaleString('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        }
                    }
                ],
                responsive: true,
                fixedHeader: true,
                order: [
                    [0, 'asc'],
                    [1, 'asc'],
                    [2, 'asc'],
                    [3, 'asc'],
                ],
                serverSide: false,
                ajax: false,
                autoWidth: true,
                select: true,
                dom: "<'row mb-3'<'col-sm-12 col-md-3 mt-1 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-9 d-flex align-items-center justify-content-end'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [{
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        titleAttr: 'Generate PDF',
                        className: 'btn-outline-danger btn-sm mr-1'
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        titleAttr: 'Generate Excel',
                        className: 'btn-outline-success btn-sm mr-1'
                    },
                    {
                        extend: 'copyHtml5',
                        text: 'Copy',
                        titleAttr: 'Copy to clipboard',
                        className: 'btn-outline-primary btn-sm mr-1'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        titleAttr: 'Print Table',
                        className: 'btn-outline-primary btn-sm'
                    }
                ],
                paging: false,

                drawCallback: function(settings) {
                    var api = this.api();
                    var rows = api.rows({
                        page: 'current'
                    }).nodes();
                    var lastOutlet = null;
                    var lastDate = null;

                    api.column(0, {
                        page: 'current'
                    }).data().each(function(curOutlet, i) {
                        var curDate = api.column(1, {
                            page: 'current'
                        }).data()[i];

                        if (lastOutlet !== curOutlet || lastDate !== curDate) {
                            $(rows).eq(i).before(
                                `<tr class="group-row"><td colspan="5" style="background:#ddd;font-weight:bold;">Outlet: ${curOutlet} - Tanggal: ${curDate}</td></tr>`
                            );
                            lastOutlet = curOutlet;
                            lastDate = curDate;
                        }
                    });
                },

            });

            tableDatagrid.on('select', function(e, dt, type, indexes) {
                if (type === 'row') {
                    reportApp.data.selected_row[reportApp.data.active_tab] = indexes[0];
                    var data = tableDatagrid.row(indexes[0]).data();
                    reportApp.data.detail = data;
                }
            });
        }
    };




    var LoadDataCharts = function(dataCharts) {
        if (dataCharts.hasOwnProperty('dow')) {
            LoadChartDow(dataCharts.dow);
        } else {
            LoadChartDow([]);
        }
        if (dataCharts.hasOwnProperty('hourly')) {
            LoadChartHourly(dataCharts.hourly);
        } else {
            LoadChartHourly([]);
        }
    }
</script>



<script>
    var chartDaily = null;
    var chartHourly = null;
    var LoadChartDow = function(data) {
        if (chartDaily) {
            chartDaily.series[0].setData(data.grand_total, true);
            chartDaily.series[1].setData(data.qty_transaction, true);
        } else {
            chartDaily = Highcharts.chart('dayOfWeekChart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Grafik Penjualan Mingguan'
                },
                xAxis: [{
                    categories: data.categories,
                    tickInterval: 1,
                    crosshair: true
                }],
                yAxis: [{ // Y Axis Kiri
                    labels: {
                        format: 'Rp {value:,.0f}'
                    },
                    title: {
                        text: 'Total Amount (Rp)'
                    }
                }, { // Y Axis Kanan
                    title: {
                        text: 'Total Transaksi'
                    },
                    labels: {
                        format: '{value}'
                    },
                    opposite: true
                }],
                tooltip: {
                    shared: true,
                    formatter: function() {
                        let hari = this.series.chart.xAxis[0].categories[this.x];
                        let s = `Hari <b>${hari}</b><br/>`;
                        this.points.forEach(function(point) {
                            if (point.series.name === 'Total Amount') {
                                s += `${point.series.name}: <b>Rp ${Highcharts.numberFormat(point.y, 0, ',', '.')}</b><br/>`;
                            } else {
                                s += `${point.series.name}: <b>${point.y} transaksi</b><br/>`;
                            }
                        });
                        return s;
                    }
                },
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                plotOptions: {
                    series: {
                        marker: {
                            enabled: true,
                            radius: 3 // default radius
                        }
                    }
                },
                series: [{
                    name: 'Total Amount',
                    type: 'column',
                    yAxis: 0,
                    color: '#4CAF50',
                    data: data.grand_total,
                }, {
                    name: 'Total Transaksi',
                    type: 'spline',
                    yAxis: 1,
                    color: '#2196F3',
                    data: data.qty_transaction,
                }]
            });
        }
    };

    var LoadChartHourly = function(data) {
        if (chartHourly) {
            chartHourly.series[0].setData(data.grand_total, true);
            chartHourly.series[1].setData(data.qty_transaction, true);
        } else {
            chartHourly = Highcharts.chart('hourlySalesChart', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: 'Grafik Penjualan Per Jam (0–23)'
                },
                xAxis: [{
                    categories: data.categories,
                    tickInterval: 1,
                    crosshair: true
                }],
                yAxis: [{
                    title: {
                        text: 'Total Amount (Rp)'
                    },
                    labels: {
                        format: 'Rp {value:,.0f}'
                    }
                }, {
                    title: {
                        text: 'Total Transaksi'
                    },
                    labels: {
                        format: '{value}'
                    },
                    opposite: true
                }],
                tooltip: {
                    shared: true,
                    formatter: function() {
                        let jam = this.x; // ambil nilai categories (0–23)
                        let s = `Jam <b>${jam}:00</b><br/>`;
                        this.points.forEach(function(point) {
                            if (point.series.name === 'Total Amount') {
                                s += `${point.series.name}: <b>Rp ${Highcharts.numberFormat(point.y, 0, ',', '.')}</b><br/>`;
                            } else {
                                s += `${point.series.name}: <b>${point.y} transaksi</b><br/>`;
                            }
                        });
                        return s;
                    }
                },
                legend: {
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                plotOptions: {
                    series: {
                        marker: {
                            enabled: true,
                            radius: 3 // default radius
                        }
                    }
                },
                series: [{
                    name: 'Total Amount',
                    type: 'column',
                    yAxis: 0,
                    color: '#4CAF50',
                    fillOpacity: 0.4,
                    data: data.grand_total,
                }, {
                    name: 'Total Transaksi',
                    type: 'spline',
                    yAxis: 1,
                    color: '#2196F3',
                    data: data.qty_transaction,
                }]
            });
        }
    };
</script>