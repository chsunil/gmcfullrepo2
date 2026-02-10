<?php
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class GMC_Client_List_Table extends WP_List_Table {

    private $is_admin_or_manager;
    private $user_id;

    public function __construct() {
        parent::__construct([
            'singular' => 'client',
            'plural'   => 'clients',
            'ajax'     => false,
        ]);

        $this->user_id = get_current_user_id();
        $user = wp_get_current_user();
        $this->is_admin_or_manager = in_array('administrator', $user->roles) || in_array('manager', $user->roles);
    }

    /** =======================
     *  Columns
     * ======================= */
    public function get_columns() {
        return [
            'client_name'        => 'Client Name',
            'certification_type'=> 'Certification Type',
            'assigned_employee' => 'Assigned Employee',
            'client_stage'      => 'Client Status',
            'created_date'      => 'Created Date',
            'audit_dates'       => 'Audit Dates',
        ];
    }

    public function get_sortable_columns() {
        return [
            'created_date' => ['date', true],
            'client_stage' => ['client_stage', false],
        ];
    }

    /** =======================
     *  Filters (Assigned Employee)
     * ======================= */
    protected function extra_tablenav($which) {
        if ($which !== 'top') return;

        $selected = $_GET['assigned_employee'] ?? '';
        ?>
        <div class="alignleft actions">
            <select name="assigned_employee">
                <option value="">All Employees</option>
                <?php
                foreach (get_users(['fields' => ['ID', 'display_name']]) as $user) {
                    printf(
                        '<option value="%d" %s>%s</option>',
                        $user->ID,
                        selected($selected, $user->ID, false),
                        esc_html($user->display_name)
                    );
                }
                ?>
            </select>
            <?php submit_button('Filter', '', 'filter_action', false); ?>
        </div>
        <?php
    }

    /** =======================
     *  Data Query
     * ======================= */
    public function prepare_items() {

        $per_page = 8;
        $paged    = $this->get_pagenum();
        $orderby  = $_GET['orderby'] ?? 'date';
        $order    = $_GET['order'] ?? 'DESC';

        $meta_query = [];

        if (!$this->is_admin_or_manager) {
            $meta_query[] = [
                'key'   => 'assigned_employee',
                'value' => $this->user_id,
            ];
        }

        if (!empty($_GET['assigned_employee'])) {
            $meta_query[] = [
                'key'   => 'assigned_employee',
                'value' => intval($_GET['assigned_employee']),
            ];
        }

        $args = [
            'post_type'      => 'client',
            'posts_per_page' => $per_page,
            'paged'          => $paged,
            'orderby'        => $orderby,
            'order'          => $order,
            'meta_query'     => $meta_query,
            's'              => $_GET['s'] ?? '',
        ];

        $query = new WP_Query($args);

        $this->items = $query->posts;

        $this->set_pagination_args([
            'total_items' => $query->found_posts,
            'per_page'    => $per_page,
        ]);
    }

    /** =======================
     *  Column Rendering
     * ======================= */
    protected function column_client_name($post) {
        $stage = get_field('client_stage', $post->ID);

        return sprintf(
            '<a href="%s">%s</a>',
            esc_url("/create-client/?new_post_id={$post->ID}&stage={$stage}"),
            esc_html($post->post_title)
        );
    }

    protected function column_certification_type($post) {
        return esc_html(get_field('certification_type', $post->ID));
    }

    protected function column_assigned_employee($post) {
        $uid = get_field('assigned_employee', $post->ID);
        if (!$uid) return '—';

        $user = get_userdata($uid);
        return $user ? esc_html($user->display_name) : '—';
    }

    protected function column_client_stage($post) {
        return strtoupper(esc_html(get_field('client_stage', $post->ID)));
    }

    protected function column_created_date($post) {
        return get_the_date('d M Y', $post->ID);
    }

    protected function column_audit_dates($post) {
        $audit_page = get_page_by_path('dates');
        $base = $audit_page ? get_permalink($audit_page) : site_url('/dates/');

        return sprintf(
            '<a class="button button-primary" href="%s">View Dates</a>',
            esc_url(add_query_arg('id', $post->ID, $base))
        );
    }
}
