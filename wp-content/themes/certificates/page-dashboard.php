<?php
/* Template Name: Dashboard */
get_header();

$current_user = wp_get_current_user();
$is_admin = current_user_can('manage_options');
?>

<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Sidebar -->
        <?php get_sidebar('custom'); ?>

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Content wrapper -->
            <div class="content-wrapper">
                <!-- Content -->
                <div class="container-xxl flex-grow-1 container-p-y">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold py-3 mb-0">
                            <span class="text-muted fw-light">Dashboard /</span> Welcome, <?php echo esc_html($current_user->display_name); ?>!
                        </h4>
                        <div class="d-flex gap-2">
                             <a href="<?php echo get_home_url(); ?>/create-client/" class="btn btn-primary btn-sm">
                                <i class="bx bx-plus me-1"></i> Add Client
                             </a>
                             <a href="<?php echo get_home_url(); ?>/client-list-pdf/" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-list-ul me-1"></i> Client List
                             </a>
                        </div>
                    </div>

                    <!-- 1. KPI Cards Row -->
                    <div class="row">
                        <!-- Total Clients -->
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <span class="badge bg-label-primary p-2"><i class="bx bx-user text-primary"></i></span>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1">Total Portfolio</span>
                                    <h3 class="card-title mb-2" id="kpiTotalClients">...</h3>
                                    <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> All Tracks</small>
                                </div>
                            </div>
                        </div>

                        <!-- Active Audits -->
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <span class="badge bg-label-success p-2"><i class="bx bx-check-double text-success"></i></span>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1">Active Audits</span>
                                    <h3 class="card-title mb-2" id="kpiActiveAudits">...</h3>
                                    <small class="text-muted">In Progress</small>
                                </div>
                            </div>
                        </div>

                        <!-- Expiring 30d -->
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <span class="badge bg-label-warning p-2"><i class="bx bx-time text-warning"></i></span>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1">Expiring (30d)</span>
                                    <h3 class="card-title mb-2" id="kpiExpiringCerts">...</h3>
                                    <small class="text-danger fw-semibold">Requires Action</small>
                                </div>
                            </div>
                        </div>

                        <!-- Total Certificates -->
                        <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                        <div class="avatar flex-shrink-0">
                                            <span class="badge bg-label-info p-2"><i class="bx bx-certification text-info"></i></span>
                                        </div>
                                    </div>
                                    <span class="fw-semibold d-block mb-1">Total Certificates</span>
                                    <h3 class="card-title mb-2" id="kpiTotalCerts">...</h3>
                                    <small class="text-muted">Issued to-date</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Main Row: Pipeline & Track Mix -->
                    <div class="row">
                        <!-- Upcoming Pipeline -->
                        <div class="col-lg-8 col-md-12 col-12 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h5 class="card-title m-0 me-2">Upcoming Audit Pipeline</h5>
                                    <small class="text-muted">Next 6 Scheduled Audits</small>
                                </div>
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Client</th>
                                                <th>Audit Type</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="upcomingAuditTableBody">
                                            <tr><td colspan="4" class="text-center">Loading pipeline...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Track Mix Donut -->
                        <div class="col-lg-4 col-md-12 col-12 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                                    <div class="card-title mb-0">
                                        <h5 class="m-0 me-2">Certification Mix</h5>
                                        <small class="text-muted">QMS vs IMS vs EMS</small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex flex-column align-items-center gap-1">
                                            <h2 class="mb-2" id="donutTotalCount">...</h2>
                                            <span>Total Clients</span>
                                        </div>
                                        <div id="trackMixChartWrapper" style="min-height: 150px; width: 150px;">
                                            <canvas id="trackMixChart"></canvas>
                                        </div>
                                    </div>
                                    <ul class="p-0 m-0" id="trackMixLegend">
                                        <!-- Dynamic legend -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Bottom Row: Activity & Trends/Workload -->
                    <div class="row">
                        <!-- Recent Activity -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h5 class="card-title m-0 me-2">Recent Client Activity</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="p-0 m-0" id="recentActivityListV2">
                                        <!-- Dynamic activity -->
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Growth Trend Line (Visible to All) -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Onboarding Trend</h5>
                                    <small class="text-muted">New Clients (Last 30 Days)</small>
                                </div>
                                <div class="card-body">
                                    <canvas id="newClientsTrendChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Workload Distribution (Admin Only) -->
                        <?php if ($is_admin) : ?>
                        <div class="col-md-12 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <h5 class="card-title m-0 me-2">Workload Distribution</h5>
                                    <small class="text-muted">Clients per Employee</small>
                                </div>
                                <div class="card-body">
                                    <canvas id="clientsPerEmpChartV2"></canvas>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
                <!-- / Content -->

<?php get_footer(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const adminAjax = '<?php echo admin_url("admin-ajax.php"); ?>';

        // 1. Load KPI Stats
        fetch(`${adminAjax}?action=get_dashboard_stats_kpi`).then(r => r.json()).then(r => {
            if (!r.success) return;
            document.getElementById('kpiTotalClients').textContent = r.data.total_clients;
            document.getElementById('kpiActiveAudits').textContent = r.data.active_audits;
            document.getElementById('kpiExpiringCerts').textContent = r.data.expiring_30d;
            document.getElementById('kpiTotalCerts').textContent = r.data.total_certs;
            document.getElementById('donutTotalCount').textContent = r.data.total_clients;
        });

        // 2. Upcoming Audit Pipeline
        fetch(`${adminAjax}?action=get_upcoming_audits`).then(r => r.json()).then(r => {
            const tbody = document.getElementById('upcomingAuditTableBody');
            if (!r.success || !r.data.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No upcoming audits scheduled.</td></tr>';
                return;
            }
            tbody.innerHTML = '';
            r.data.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${item.name}</strong></td>
                    <td><span class="badge bg-label-primary">${item.type}</span></td>
                    <td>${item.date}</td>
                    <td><a href="${item.link}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bx bx-chevron-right"></i></a></td>
                `;
                tbody.appendChild(tr);
            });
        });

        // 3. Track Mix Donut
        fetch(`${adminAjax}?action=get_track_distribution`).then(r => r.json()).then(r => {
            if (!r.success) return;
            const colors = ['#696cff', '#03c3ec', '#71dd37'];
            new Chart(document.getElementById('trackMixChart'), {
                type: 'doughnut',
                data: {
                    labels: r.data.labels,
                    datasets: [{
                        data: r.data.data,
                        backgroundColor: colors,
                        hoverOffset: 4,
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: { legend: { display: false } }
                }
            });

            // Build custom legend
            const legend = document.getElementById('trackMixLegend');
            r.data.labels.forEach((label, i) => {
                const li = document.createElement('li');
                li.className = 'd-flex mb-3 pb-1';
                li.innerHTML = `
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-custom" style="background-color: ${colors[i]}22 !important; color: ${colors[i]} !important;"><i class="bx bx-circle"></i></span>
                    </div>
                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                            <h6 class="mb-0 text-uppercase" style="font-size: 0.7rem;">${label}</h6>
                        </div>
                        <div class="user-progress">
                            <small class="fw-semibold">${r.data.data[i]}</small>
                        </div>
                    </div>
                `;
                legend.appendChild(li);
            });
        });

        // 4. Onboarding Trend
        fetch(`${adminAjax}?action=get_new_clients_30d`).then(r => r.json()).then(r => {
            if (!r.success) return;
            new Chart(document.getElementById('newClientsTrendChart'), {
                type: 'line',
                data: {
                    labels: Object.keys(r.data),
                    datasets: [{
                        label: 'New Clients',
                        data: Object.values(r.data),
                        borderColor: '#696cff',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });

        // 5. Recent Activity
        fetch(`${adminAjax}?action=get_recent_client_activity`).then(r => r.json()).then(r => {
            const list = document.getElementById('recentActivityListV2');
            if (!r.success || !r.data.length) {
                list.innerHTML = '<li class="text-center text-muted">No recent activity.</li>';
                return;
            }
            list.innerHTML = '';
            r.data.forEach(item => {
                const li = document.createElement('li');
                li.className = 'd-flex mb-4 pb-1';
                li.innerHTML = `
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-secondary"><i class="bx bx-revision"></i></span>
                    </div>
                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                            <small class="text-muted d-block mb-1">${item.when}</small>
                            <h6 class="mb-0"><a href="${item.link}" class="text-body">${item.title}</a></h6>
                        </div>
                        <div class="user-progress d-flex align-items-center gap-1">
                            <span class="badge bg-label-info mt-1">${item.stage}</span>
                        </div>
                    </div>
                `;
                list.appendChild(li);
            });
        });

        // 6. Workload Distribution (Admin Only)
        if (document.getElementById('clientsPerEmpChartV2')) {
            fetch(`${adminAjax}?action=get_clients_per_employee`).then(r => r.json()).then(r => {
                if (!r.success) return;
                new Chart(document.getElementById('clientsPerEmpChartV2'), {
                    type: 'bar',
                    data: {
                        labels: r.data.labels,
                        datasets: [{
                            data: r.data.data,
                            backgroundColor: '#03c3ec',
                            borderRadius: 5
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true } }
                    }
                });
            });
        }

    });
</script>

<style>
.bg-label-custom {
    display: flex;
    align-items: center;
    justify-content: center;
}
.card-title {
    font-weight: 700;
}
</style>