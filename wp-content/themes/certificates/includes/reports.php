<?php

/**
 * Helper: Get client meta query based on role
 */
function gmc_get_client_meta_query() {
    if (current_user_can('manage_options') || current_user_can('administrator')) {
        return [];
    }
    return [[
        'key'   => 'assigned_employee',
        'value' => get_current_user_id(),
    ]];
}

// 1B) AJAX: Assigned vs. All Clients
add_action('wp_ajax_get_assigned_clients_stats', 'ajax_get_assigned_clients_stats');
function ajax_get_assigned_clients_stats() {
    $uid = get_current_user_id();
    // my clients
    $mine = new WP_Query([
        'post_type'      => 'client',
        'post_status'    => 'publish',
        'meta_query'     => [[
            'key'   => 'assigned_employee',
            'value' => $uid,
        ]],
        'fields'         => 'ids',
        'posts_per_page' => -1,
    ]);
    // all clients
    $total = wp_count_posts('client')->publish;
    wp_send_json_success([
        'mine'  => intval($mine->found_posts),
        'total' => intval($total),
    ]);
}

// 1C) AJAX: New Clients Last 30 Days
add_action('wp_ajax_get_new_clients_30d', 'ajax_get_new_clients_30d');
function ajax_get_new_clients_30d() {
    $data = [];
    $meta_query = gmc_get_client_meta_query();
    
    for ($i = 29; $i >= 0; $i--) {
        $day   = date_i18n('M j', strtotime("-{$i} days"));
        $args = [
            'post_type'      => 'client',
            'post_status'    => 'publish',
            'date_query'     => [[
                'year'  => date('Y', strtotime("-{$i} days")),
                'month' => date('m', strtotime("-{$i} days")),
                'day'   => date('d', strtotime("-{$i} days")),
            ]],
            'fields'         => 'ids',
            'posts_per_page' => -1,
        ];
        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }
        
        $count = new WP_Query($args);
        $data[$day] = intval($count->found_posts);
    }
    wp_send_json_success($data);
}

// 1D) AJAX: Recent Client Activity
add_action('wp_ajax_get_recent_client_activity', 'ajax_get_recent_client_activity');
function ajax_get_recent_client_activity() {
    $args = [
        'post_type'      => 'client',
        'post_status'    => 'publish',
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'posts_per_page' => 5,
    ];
    $meta_query = gmc_get_client_meta_query();
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    $q = new WP_Query($args);
    $items = [];
    while ($q->have_posts()) {
        $q->the_post();
        $items[] = [
            'title' => get_the_title(),
            'link' => get_home_url() . '/create-client/?new_post_id=' . intval(get_the_ID()),
            'stage' => get_field('client_stage', get_the_ID()) ?: '—',
            'when'  => human_time_diff(get_the_modified_time('U'), current_time('timestamp')) . ' ago',
        ];
    }
    wp_reset_postdata();
    wp_send_json_success($items);
}

/**
 * NEW: Dashboard Stats KPI (Top Cards)
 */
add_action('wp_ajax_get_dashboard_stats_kpi', function() {
    $meta_query = gmc_get_client_meta_query();
    
    // Total Portfolio
    $total_args = ['post_type' => 'client', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids'];
    if (!empty($meta_query)) $total_args['meta_query'] = $meta_query;
    $total_clients = (new WP_Query($total_args))->found_posts;

    // Active Track
    $active_args = $total_args;
    $active_args['meta_query'][] = [
        'key' => 'client_stage',
        'value' => ['', 'draft'],
        'compare' => 'NOT IN'
    ];
    $active_audits = (new WP_Query($active_args))->found_posts;

    // Certificates Total
    $total_certs = wp_count_posts('certificates')->publish;

    // Expiring (30d)
    $today = date('Y-m-d');
    $future = date('Y-m-d', strtotime('+30 days'));
    $exp_q = new WP_Query([
        'post_type'      => 'certificates',
        'post_status'    => 'publish',
        'meta_query'     => [[
            'key'     => 'expiry_date',
            'value'   => [$today, $future],
            'compare' => 'BETWEEN',
            'type'    => 'DATE'
        ]],
        'fields'         => 'ids',
        'posts_per_page' => -1
    ]);

    wp_send_json_success([
        'total_clients' => $total_clients,
        'active_audits' => $active_audits,
        'total_certs'   => $total_certs,
        'expiring_30d'  => $exp_q->found_posts
    ]);
});

/**
 * NEW: Upcoming Audit Pipeline
 */
add_action('wp_ajax_get_upcoming_audits', function() {
    $meta_query = gmc_get_client_meta_query();
    
    // Date fields to check
    $date_fields = [
        'stage2_audit_surveillance_audit_date_initial' => 'Stage 2 Audit',
        'stage2_audit_surveillance_audit_date_surv1' => 'Surv-1 Audit',
        'stage2_audit_surveillance_audit_date_surv2' => 'Surv-2 Audit'
    ];

    $args = [
        'post_type' => 'client',
        'post_status' => 'publish',
        'posts_per_page' => -1
    ];
    if (!empty($meta_query)) $args['meta_query'] = $meta_query;

    $clients = get_posts($args);
    $pipeline = [];
    $today = time();

    foreach ($clients as $c) {
        foreach ($date_fields as $key => $label) {
            $raw_date = get_post_meta($c->ID, $key, true);
            if ($raw_date) {
                $ts = strtotime($raw_date);
                if ($ts && $ts >= $today - (24 * 3600)) { // Include today
                    $pipeline[] = [
                        'id' => $c->ID,
                        'name' => $c->post_title,
                        'type' => $label,
                        'date' => date('d M Y', $ts),
                        'timestamp' => $ts,
                        'link' => get_home_url() . '/create-client/?new_post_id=' . $c->ID
                    ];
                }
            }
        }
    }

    // Sort by date ASC
    usort($pipeline, function($a, $b) { return $a['timestamp'] - $b['timestamp']; });
    
    // Return top 6
    wp_send_json_success(array_slice($pipeline, 0, 6));
});

/**
 * NEW: Track Distribution
 */
add_action('wp_ajax_get_track_distribution', function() {
    $meta_query = gmc_get_client_meta_query();
    $tracks = ['qms' => 'QMS', 'ims' => 'IMS', 'ems' => 'EMS'];
    $data = [];
    $labels = [];

    foreach ($tracks as $key => $name) {
        $args = [
            'post_type' => 'client',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => [
                'relation' => 'AND',
                ['key' => 'certification_type', 'value' => $key]
            ]
        ];
        if (!empty($meta_query)) $args['meta_query'][] = $meta_query[0];
        
        $labels[] = $name;
        $data[] = (new WP_Query($args))->found_posts;
    }

    wp_send_json_success(['labels' => $labels, 'data' => $data]);
});

// 4) Clients by Stage
add_action('wp_ajax_get_stage_distribution', function () {
    $track = $_GET['track'] ?? 'qms';
    $stages = get_certification_stages()[$track] ?? get_certification_stages()['qms'];
    $labels = $data = [];
    $meta_query_base = gmc_get_client_meta_query();

    foreach ($stages as $key => $cfg) {
        $mq = [['key' => 'client_stage', 'value' => $key]];
        if (!empty($meta_query_base)) $mq[] = $meta_query_base[0];

        $count = new WP_Query([
            'post_type'      => 'client',
            'post_status'    => 'publish',
            'meta_query'     => $mq,
            'fields'         => 'ids',
            'posts_per_page' => -1
        ]);
        if ($count->found_posts > 0) {
            $labels[] = $cfg['title'];
            $data[]   = intval($count->found_posts);
        }
    }
    wp_send_json_success(['labels' => $labels, 'data' => $data]);
});

// 6) Clients Per Employee (Horizontal Bar) - Admin Only
add_action('wp_ajax_get_clients_per_employee', function () {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    $users = get_users(['role__in' => ['administrator', 'manager', 'employee']]);
    $labels = [];
    $data = [];
    foreach ($users as $u) {
        $count = new WP_Query([
            'post_type'      => 'client',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query'     => [[
                'key' => 'assigned_employee',
                'value' => $u->ID
            ]]
        ]);
        if ($count->found_posts > 0) {
            $labels[] = $u->display_name;
            $data[]   = intval($count->found_posts);
        }
    }
    wp_send_json_success(['labels' => $labels, 'data' => $data]);
});
