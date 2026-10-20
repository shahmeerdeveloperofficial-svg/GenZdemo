
(function ($) {
    "use strict";

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function () {
            // Show success message
            toastr.success('{{ __("Affiliate key copied to clipboard!") }}');
        }, function (err) {
            // Fallback for older browsers
            var textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                toastr.success('{{ __("Affiliate key copied to clipboard!") }}');
            } catch (err) {
                toastr.error('{{ __("Failed to copy affiliate key") }}');
            }
            document.body.removeChild(textArea);
        });
    }

    if ($('#affiliateDashboardTable').length) {
        var earningTable = $('#affiliateDashboardTable').DataTable({
            pageLength: 10,
            ordering: true,
            serverSide: true,
            processing: true,
            searching: true,
            responsive: true,
            ajax: window.affiliateRoutes.myAffiliations,
            language: {
                paginate: {
                    previous: "<i class='fa-solid fa-angles-left'></i>",
                    next: "<i class='fa-solid fa-angles-right'></i>"
                },
            },
            dom: '<"row"<"col-md-12 d-flex justify-content-end mb-2"B>>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-outline-secondary btn-sm',
                    text: '<i class="fa fa-copy"></i> Copy'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-outline-success btn-sm',
                    text: '<i class="fa fa-file-excel"></i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-outline-danger btn-sm',
                    text: '<i class="fa fa-file-pdf"></i> PDF'
                }
            ],
            columns: [
                { "data": "date" },
                { "data": "item_name" },
                { "data": "actual_price" },
                { "data": "commission" },
                { "data": "commission_percentage" }
            ]
        });

        $('#earning_search_key').on('keyup', function () {
            earningTable.search(this.value).draw();
        });
    }

    // Affiliate Commission Area Chart
    var commissionData = $('#commission-chart-data').val();
    var commissionCategories = $('#commission-month-category-list').val();

    var commissionOptions = {
        series: [{
            name: "Commission",
            data: commissionData ? JSON.parse(commissionData) : [],
        }],
        chart: {
            toolbar: {
                show: false,
            },
            type: 'area',
            height: 350,
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: commissionCategories ? JSON.parse(commissionCategories) : [],
            type: 'category',
        },
        yaxis: {
            opposite: true
        },
        legend: {
            horizontalAlign: 'left'
        },
        noData: {
            text: 'No commission data available'
        }
    };

    var commissionChart = new ApexCharts(document.querySelector("#affiliate-commission-chart"), commissionOptions);
    commissionChart.render();

    // Affiliate Referral Area Chart
    var referralData = $('#referral-chart-data').val();
    var referralCategories = $('#referral-month-category-list').val();

    var referralOptions = {
        series: [{
            name: "Referrals",
            data: referralData ? JSON.parse(referralData) : [],
        }],
        chart: {
            toolbar: {
                show: false,
            },
            type: 'area',
            height: 350,
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: referralCategories ? JSON.parse(referralCategories) : [],
            type: 'category',
        },
        yaxis: {
            opposite: true
        },
        legend: {
            horizontalAlign: 'left'
        }
    };

    var referralChart = new ApexCharts(document.querySelector("#affiliate-referral-chart"), referralOptions);
    referralChart.render();

    // Affiliate Top Referred Bar Chart
    var topReferredCounts = $('#top-referred-counts').val();
    var topReferredNames = $('#top-referred-names').val();

    var topReferredOptions = {
        series: [{
            name: "Referrals",
            data: topReferredCounts ? JSON.parse(topReferredCounts) : [],
        }],
        chart: {
            height: 370,
            type: 'bar',
            redrawOnWindowResize: false,
            redrawOnParentResize: false,
        },
        plotOptions: {
            bar: {
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: topReferredNames ? JSON.parse(topReferredNames) : [],
        },
        yaxis: {
            opposite: true
        },
        noData: {
            text: 'No referral data available'
        }
    };

    var topReferredChart = new ApexCharts(document.querySelector("#affiliate-top-referred-chart"), topReferredOptions);
    topReferredChart.render();

    // Affiliate Commission by Type Pie Chart
    var commissionTypeLabels = $('#commission-type-labels').val();
    var commissionTypeData = $('#commission-type-data').val();

    if (commissionTypeLabels && commissionTypeData) {
        var commissionTypeOptions = {
            series: JSON.parse(commissionTypeData),
            chart: {
                height: 370,
                type: 'pie',
                redrawOnWindowResize: false,
                redrawOnParentResize: false,
            },
            labels: JSON.parse(commissionTypeLabels),
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            noData: {
                text: 'No commission data available'
            }
        };

        var commissionTypeChart = new ApexCharts(document.querySelector("#affiliate-commission-type-chart"), commissionTypeOptions);
        commissionTypeChart.render();
    }

})(jQuery)