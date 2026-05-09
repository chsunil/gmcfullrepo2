<?php
/**
 * ACF Field Type: epme_dynamic
 *
 * Replicates the Excel F-01 "Employee, Process, Machinery or Equipment Details" logic:
 *
 *  Row 53  → User enters up to 8 dept names for each site type (Office / Main Site / Temp Sites)
 *  B57     → TEXTJOIN: auto-computed comma list of non-empty, non-N/a names
 *  Rows 61+→ Employee headcount matrix where row labels = dept names (dynamic)
 *
 * Data stored as one serialised PHP array in wp_postmeta:
 * [
 *   'office_depts'   => ['Top Management','Marketing','Purchase','HR','','','',''],
 *   'main_depts'     => ['Production','QC','QA','Stores','Maintenance','IQA','MRM',''],
 *   'temp_depts'     => ['','','','','','','',''],
 *   'matrix'         => [
 *       'Top Management' => ['Off1st'=>'2','Off2nd'=>'','Off3rd'=>'','mos1st'=>'', …,'ts3rd'=>''],
 *       …
 *       'Part time'      => […],
 *       'Sub Total'      => […],   // read-only computed on display
 *       'Total Employees'=> […],   // read-only computed on display
 *   ],
 * ]
 *
 * On save, this field also syncs the three process-group textareas:
 *   process_Process_Operations_office
 *   process_Process_Operations_Main_Operative_Site_
 *   process_Process_Operations_Temporary_Sites
 * so the rest of the site (F-02 template, etc.) can still read them.
 */

if ( ! defined('ABSPATH') ) exit;

class ACF_Field_EpmeDynamic extends acf_field {

    // ── fixed rows always appended after dynamic dept rows ─────────────────────
    const FIXED_ROWS = [
        'Part time',
        'Temporary',
        'Contract',
        'Others@@',
        'Out of above how many are working away from Organization',
    ];

    // ── the 9 column keys (3 site-types × 3 shifts) ───────────────────────────
    const COLUMNS = [
        'Off1st' => 'Office 1st',
        'Off2nd' => 'Office 2nd',
        'Off3rd' => 'Office 3rd',
        'mos1st' => 'Main Site 1st',
        'mos2nd' => 'Main Site 2nd',
        'mos3rd' => 'Main Site 3rd',
        'ts1st'  => 'Temp Site 1st',
        'ts2nd'  => 'Temp Site 2nd',
        'ts3rd'  => 'Temp Site 3rd',
    ];

    // ── column groups for sub-total header rendering ───────────────────────────
    const COL_GROUPS = [
        ['label' => 'Office',         'cols' => ['Off1st','Off2nd','Off3rd']],
        ['label' => 'Main Operative Site', 'cols' => ['mos1st','mos2nd','mos3rd']],
        ['label' => 'Temporary Sites','cols' => ['ts1st','ts2nd','ts3rd']],
    ];

    function __construct() {
        $this->name     = 'epme_dynamic';
        $this->label    = __('EPME Dynamic Matrix', 'acf');
        $this->category = 'layout';
        $this->defaults = [];
        parent::__construct();
    }

    // ── field settings (none required — self-contained) ────────────────────────
    function render_field_settings( $field ) {
        acf_render_field_setting( $field, [
            'label'        => __('Max Department Slots per Site', 'acf'),
            'instructions' => __('Number of department name slots (Excel cols C–J = 8)', 'acf'),
            'type'         => 'number',
            'name'         => 'max_depts',
            'default_value'=> 8,
        ]);
    }

    // ── render the field in the WP Admin / frontend form ──────────────────────
    function render_field( $field ) {
        $fname    = $field['name'];
        $value    = is_array( $field['value'] ) ? $field['value'] : [];
        $max      = max( 1, (int)( $field['max_depts'] ?? 8 ) );

        $off_depts  = array_pad( (array)( $value['office_depts']  ?? [] ), $max, '' );
        $main_depts = array_pad( (array)( $value['main_depts']    ?? [] ), $max, '' );
        $temp_depts = array_pad( (array)( $value['temp_depts']    ?? [] ), $max, '' );
        $matrix     = (array)( $value['matrix'] ?? [] );

        $uid = 'epme_' . esc_attr( $field['key'] );
        ?>

        <div class="acf-epme-wrap" id="<?= $uid ?>" data-field-name="<?= esc_attr($fname) ?>" data-max="<?= (int)$max ?>">

            <!-- ── Section 1: Department Configuration (Row 53 equivalent) ── -->
            <h4 style="margin:8px 0 4px;border-bottom:1px solid #ccc;padding-bottom:4px;">
                Department Configuration
                <small style="font-weight:normal;color:#666;">(enter up to <?= $max ?> names per site; leave unused cells blank)</small>
            </h4>

            <table class="acf-epme-dept-table widefat striped" style="margin-bottom:12px;">
                <thead>
                    <tr>
                        <th style="width:140px;">Site Type</th>
                        <?php for ( $i = 1; $i <= $max; $i++ ): ?>
                            <th style="min-width:90px;">Dept <?= $i ?></th>
                        <?php endfor; ?>
                        <th style="width:200px;">Auto-joined list (B57)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $site_types = [
                        'office_depts' => [ 'label' => 'Office',              'depts' => $off_depts  ],
                        'main_depts'   => [ 'label' => 'Main Operative Site', 'depts' => $main_depts ],
                        'temp_depts'   => [ 'label' => 'Temporary Sites',     'depts' => $temp_depts ],
                    ];
                    foreach ( $site_types as $skey => $sinfo ):
                        $joined = implode(', ', array_filter(
                            $sinfo['depts'],
                            fn($d) => $d !== '' && strtolower($d) !== 'n/a'
                        ));
                    ?>
                    <tr data-site-key="<?= esc_attr($skey) ?>">
                        <th><?= esc_html($sinfo['label']) ?></th>
                        <?php foreach ( $sinfo['depts'] as $idx => $dval ): ?>
                        <td>
                            <input
                                type="text"
                                class="acf-epme-dept-input"
                                name="<?= esc_attr("{$fname}[{$skey}][{$idx}]") ?>"
                                value="<?= esc_attr($dval) ?>"
                                placeholder="N/a"
                                style="width:100%;"
                                data-site="<?= esc_attr($skey) ?>"
                                data-idx="<?= (int)$idx ?>"
                            />
                        </td>
                        <?php endforeach; ?>
                        <td>
                            <span class="acf-epme-joined" data-site="<?= esc_attr($skey) ?>">
                                <?= esc_html($joined) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- ── Section 2: Employee Headcount Matrix (rows 60-75 equivalent) ── -->
            <h4 style="margin:8px 0 4px;border-bottom:1px solid #ccc;padding-bottom:4px;">
                3.0: Employees — Give breakup as below
                <small style="font-weight:normal;color:#666;">(rows update automatically as you type dept names above)</small>
            </h4>

            <div class="acf-epme-matrix-wrap" style="overflow-x:auto;">
                <table class="acf-epme-matrix widefat" style="min-width:700px;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width:200px;vertical-align:bottom;border:1px solid #ccc;">Department / Category</th>
                            <?php foreach ( self::COL_GROUPS as $grp ): ?>
                                <th colspan="<?= count($grp['cols']) ?>" style="text-align:center;border:1px solid #ccc;background:#f0f4f8;">
                                    <?= esc_html($grp['label']) ?>
                                </th>
                            <?php endforeach; ?>
                            <th rowspan="2" style="width:70px;vertical-align:bottom;border:1px solid #ccc;background:#f9f9f9;">Total</th>
                        </tr>
                        <tr>
                            <?php foreach ( self::COL_GROUPS as $grp ):
                                foreach ( $grp['cols'] as $colkey ): ?>
                                <th style="text-align:center;border:1px solid #ccc;font-weight:normal;font-size:11px;">
                                    <?= esc_html( self::COLUMNS[$colkey] ) ?>
                                </th>
                            <?php endforeach; endforeach; ?>
                        </tr>
                    </thead>
                    <tbody id="<?= $uid ?>_tbody">
                        <?php
                        // Build full ordered row list: dynamic depts → fixed rows
                        $all_depts = array_unique( array_filter(
                            array_merge( $off_depts, $main_depts, $temp_depts ),
                            fn($d) => $d !== '' && strtolower($d) !== 'n/a'
                        ));
                        $all_rows = array_merge( array_values($all_depts), self::FIXED_ROWS );

                        foreach ( $all_rows as $rowlabel ):
                            $row_data = $matrix[$rowlabel] ?? [];
                            $is_fixed = in_array( $rowlabel, self::FIXED_ROWS );
                            $row_total = 0;
                            echo '<tr data-row="' . esc_attr($rowlabel) . '" ' .
                                 ( ! $is_fixed ? 'class="acf-epme-dept-row"' : 'class="acf-epme-fixed-row"' ) . '>';
                            echo '<th style="border:1px solid #ccc;padding:3px 5px;">' . esc_html($rowlabel) . '</th>';

                            foreach ( self::COLUMNS as $colkey => $collabel ):
                                $v = isset($row_data[$colkey]) ? $row_data[$colkey] : '';
                                $row_total += (int)$v;
                                echo '<td style="border:1px solid #ccc;padding:2px;">';
                                printf(
                                    '<input type="number" min="0"
                                        class="acf-epme-cell"
                                        name="%s"
                                        value="%s"
                                        data-row="%s"
                                        data-col="%s"
                                        style="width:100%%;text-align:center;" />',
                                    esc_attr("{$fname}[matrix][{$rowlabel}][{$colkey}]"),
                                    esc_attr($v),
                                    esc_attr($rowlabel),
                                    esc_attr($colkey)
                                );
                                echo '</td>';
                            endforeach;

                            echo '<td style="border:1px solid #ccc;text-align:center;font-weight:bold;background:#f9f9f9;" class="acf-epme-row-total">';
                            echo $row_total > 0 ? esc_html($row_total) : '';
                            echo '</td></tr>';
                        endforeach;
                        ?>
                    </tbody>
                    <tfoot>
                        <!-- Sub Total row -->
                        <tr style="background:#e8f0e8;font-weight:bold;">
                            <th style="border:1px solid #ccc;padding:3px 5px;">Sub Total</th>
                            <?php foreach ( self::COLUMNS as $colkey => $collabel ):
                                $col_sum = 0;
                                foreach ( $all_rows as $rowlabel ) {
                                    $col_sum += (int)( $matrix[$rowlabel][$colkey] ?? 0 );
                                }
                            ?>
                            <td style="border:1px solid #ccc;text-align:center;" class="acf-epme-subtotal" data-col="<?= esc_attr($colkey) ?>">
                                <?= $col_sum > 0 ? esc_html($col_sum) : '' ?>
                            </td>
                            <?php endforeach; ?>
                            <td style="border:1px solid #ccc;text-align:center;background:#d4e8d4;" class="acf-epme-grand-total"></td>
                        </tr>
                        <!-- Total Employees = sum of all Sub Totals -->
                        <tr style="background:#d4e8d4;font-weight:bold;">
                            <th style="border:1px solid #ccc;padding:3px 5px;">Total Employees</th>
                            <?php
                            $grand = 0;
                            foreach ( self::COLUMNS as $colkey => $_ ) {
                                $col_sum = 0;
                                foreach ( $all_rows as $rowlabel ) {
                                    $col_sum += (int)( $matrix[$rowlabel][$colkey] ?? 0 );
                                }
                                $grand += $col_sum;
                                echo '<td style="border:1px solid #ccc;text-align:center;" class="acf-epme-total-col" data-col="' . esc_attr($colkey) . '">' .
                                     ( $col_sum > 0 ? esc_html($col_sum) : '' ) .
                                     '</td>';
                            }
                            ?>
                            <td style="border:1px solid #ccc;text-align:center;background:#b8dab8;font-size:13px;" class="acf-epme-grand-total-cell">
                                <?= $grand > 0 ? esc_html($grand) : '' ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div><!-- /.acf-epme-matrix-wrap -->

        </div><!-- /.acf-epme-wrap -->

        <style>
        .acf-epme-dept-table td, .acf-epme-dept-table th { border: 1px solid #ddd; padding: 4px 5px; }
        .acf-epme-dept-input { font-size: 12px; }
        .acf-epme-joined { font-style: italic; color: #444; font-size: 12px; }
        .acf-epme-dept-row th { background: #f9faff; }
        .acf-epme-fixed-row th { background: #fffdf0; }
        </style>

        <script>
        (function($){
            var uid = <?= json_encode($uid) ?>;
            var wrap = document.getElementById(uid);
            if (!wrap) return;

            // ── 1. Live TEXTJOIN display ────────────────────────────────────────
            function updateJoined(siteKey) {
                var inputs = wrap.querySelectorAll('.acf-epme-dept-input[data-site="' + siteKey + '"]');
                var vals   = [];
                inputs.forEach(function(inp) {
                    var v = inp.value.trim();
                    if (v && v.toLowerCase() !== 'n/a') vals.push(v);
                });
                var span = wrap.querySelector('.acf-epme-joined[data-site="' + siteKey + '"]');
                if (span) span.textContent = vals.join(', ');
            }

            // ── 2. Rebuild matrix rows from current dept inputs ─────────────────
            function rebuildMatrixRows() {
                var tbody   = document.getElementById(uid + '_tbody');
                if (!tbody) return;

                var fieldName = wrap.dataset.fieldName;

                // Collect all dept names (all site types, de-duped, non-empty, non-N/a)
                var allDepts = [];
                var seen     = {};
                var sites    = ['office_depts','main_depts','temp_depts'];
                sites.forEach(function(skey){
                    wrap.querySelectorAll('.acf-epme-dept-input[data-site="' + skey + '"]')
                        .forEach(function(inp){
                            var v = inp.value.trim();
                            if (v && v.toLowerCase() !== 'n/a' && !seen[v]) {
                                seen[v] = true;
                                allDepts.push(v);
                            }
                        });
                });

                // Fixed rows always at the bottom
                var fixedRows = <?= json_encode(self::FIXED_ROWS) ?>;
                var allRows   = allDepts.concat(fixedRows);
                var cols      = <?= json_encode(array_keys(self::COLUMNS)) ?>;

                // Snapshot existing values from current inputs
                var snapshot = {};
                tbody.querySelectorAll('.acf-epme-cell').forEach(function(inp){
                    var r = inp.dataset.row;
                    var c = inp.dataset.col;
                    if (!snapshot[r]) snapshot[r] = {};
                    snapshot[r][c] = inp.value;
                });

                // Rebuild rows
                tbody.innerHTML = '';
                allRows.forEach(function(rowlabel){
                    var isFixed  = fixedRows.indexOf(rowlabel) !== -1;
                    var tr       = document.createElement('tr');
                    tr.dataset.row = rowlabel;
                    tr.className   = isFixed ? 'acf-epme-fixed-row' : 'acf-epme-dept-row';

                    var th = document.createElement('th');
                    th.style.cssText = 'border:1px solid #ccc;padding:3px 5px;';
                    th.textContent   = rowlabel;
                    tr.appendChild(th);

                    var rowTotal = 0;
                    cols.forEach(function(colkey){
                        var v   = (snapshot[rowlabel] && snapshot[rowlabel][colkey]) ? snapshot[rowlabel][colkey] : '';
                        rowTotal += parseInt(v) || 0;

                        var td  = document.createElement('td');
                        td.style.cssText = 'border:1px solid #ccc;padding:2px;';

                        var inp = document.createElement('input');
                        inp.type        = 'number';
                        inp.min         = '0';
                        inp.className   = 'acf-epme-cell';
                        inp.name        = fieldName + '[matrix][' + rowlabel + '][' + colkey + ']';
                        inp.value       = v;
                        inp.dataset.row = rowlabel;
                        inp.dataset.col = colkey;
                        inp.style.cssText = 'width:100%;text-align:center;';
                        inp.addEventListener('input', updateTotals);

                        td.appendChild(inp);
                        tr.appendChild(td);
                    });

                    // Row total cell
                    var tdTotal = document.createElement('td');
                    tdTotal.style.cssText = 'border:1px solid #ccc;text-align:center;font-weight:bold;background:#f9f9f9;';
                    tdTotal.className     = 'acf-epme-row-total';
                    tdTotal.textContent   = rowTotal > 0 ? rowTotal : '';
                    tr.appendChild(tdTotal);

                    tbody.appendChild(tr);
                });

                updateTotals();
            }

            // ── 3. Update column sub-totals and grand total ─────────────────────
            function updateTotals() {
                var cols     = <?= json_encode(array_keys(self::COLUMNS)) ?>;
                var table    = wrap.querySelector('.acf-epme-matrix');
                var grandTotal = 0;

                cols.forEach(function(colkey){
                    var colSum = 0;
                    wrap.querySelectorAll('.acf-epme-cell[data-col="' + colkey + '"]').forEach(function(inp){
                        colSum += parseInt(inp.value) || 0;
                    });
                    grandTotal += colSum;

                    // Update sub-total cell
                    var stCell = table.querySelector('.acf-epme-subtotal[data-col="' + colkey + '"]');
                    if (stCell) stCell.textContent = colSum > 0 ? colSum : '';

                    // Update total-employees cell
                    var totCell = table.querySelector('.acf-epme-total-col[data-col="' + colkey + '"]');
                    if (totCell) totCell.textContent = colSum > 0 ? colSum : '';

                    // Update row totals
                    wrap.querySelectorAll('.acf-epme-cell[data-col="' + colkey + '"]').forEach(function(inp){
                        var tr        = inp.closest('tr');
                        var rowTotTd  = tr ? tr.querySelector('.acf-epme-row-total') : null;
                        if (rowTotTd) {
                            var rowSum = 0;
                            tr.querySelectorAll('.acf-epme-cell').forEach(function(c){ rowSum += parseInt(c.value)||0; });
                            rowTotTd.textContent = rowSum > 0 ? rowSum : '';
                        }
                    });
                });

                // Grand total
                var gcell = table.querySelector('.acf-epme-grand-total-cell');
                if (gcell) gcell.textContent = grandTotal > 0 ? grandTotal : '';
            }

            // ── Event listeners ─────────────────────────────────────────────────
            wrap.querySelectorAll('.acf-epme-dept-input').forEach(function(inp){
                inp.addEventListener('input', function(){
                    updateJoined(this.dataset.site);
                    rebuildMatrixRows();
                });
            });

            wrap.querySelectorAll('.acf-epme-cell').forEach(function(inp){
                inp.addEventListener('input', updateTotals);
            });

            // Initial totals render
            updateTotals();

        })(jQuery || { fn: {} });
        </script>

        <?php
    }

    // ── format_value: what get_field() returns ─────────────────────────────────
    function format_value( $value, $post_id, $field ) {
        if ( ! is_array($value) ) return $value;

        // Add computed properties for convenience
        $off  = array_filter( $value['office_depts']  ?? [], fn($d) => $d && strtolower($d) !== 'n/a' );
        $main = array_filter( $value['main_depts']    ?? [], fn($d) => $d && strtolower($d) !== 'n/a' );
        $temp = array_filter( $value['temp_depts']    ?? [], fn($d) => $d && strtolower($d) !== 'n/a' );

        $value['office_text']   = implode(', ', $off);   // B57 equivalent
        $value['main_text']     = implode(', ', $main);  // E57 equivalent
        $value['temp_text']     = implode(', ', $temp);  // H57 equivalent

        // Compute totals
        $matrix = $value['matrix'] ?? [];
        $total  = 0;
        foreach ( $matrix as $row ) {
            foreach ( $row as $v ) $total += (int)$v;
        }
        $value['total_employees'] = $total;

        return $value;
    }

    // ── update_value: runs when the post is saved ──────────────────────────────
    function update_value( $value, $post_id, $field ) {
        if ( ! is_array($value) ) return $value;

        // Sync the three process-group textareas for backward compatibility
        $off  = array_filter( $value['office_depts']  ?? [], fn($d) => $d && strtolower($d) !== 'n/a' );
        $main = array_filter( $value['main_depts']    ?? [], fn($d) => $d && strtolower($d) !== 'n/a' );
        $temp = array_filter( $value['temp_depts']    ?? [], fn($d) => $d && strtolower($d) !== 'n/a' );

        update_field('process_Process_Operations_office',             implode(', ', $off),  $post_id);
        update_field('process_Process_Operations_Main_Operative_Site_', implode(', ', $main), $post_id);
        update_field('process_Process_Operations_Temporary_Sites',    implode(', ', $temp), $post_id);

        // Also sync the legacy epme_matrix format so existing PDF templates still work
        $cols     = array_keys( self::COLUMNS );
        $empty_r  = array_fill_keys( $cols, '' );
        $legacy   = [];

        $all_depts = array_unique( array_filter(
            array_merge( array_values($off), array_values($main), array_values($temp) ),
            fn($d) => $d !== ''
        ));

        foreach ( $all_depts as $dept ) {
            $legacy[$dept] = array_merge( $empty_r, (array)( $value['matrix'][$dept] ?? [] ) );
        }
        foreach ( self::FIXED_ROWS as $fr ) {
            $legacy[$fr] = array_merge( $empty_r, (array)( $value['matrix'][$fr] ?? [] ) );
        }

        // Compute sub-total & total rows
        $subtotal = $empty_r;
        foreach ( $legacy as $row ) {
            foreach ( $cols as $c ) {
                $subtotal[$c] = (string)( (int)$subtotal[$c] + (int)( $row[$c] ?? 0 ) );
            }
        }
        $legacy['Sub Total']      = $subtotal;
        $legacy['Total Employees'] = $subtotal;  // same as sub-total for single-cycle

        update_post_meta( $post_id, 'epme_matrix', $legacy );

        return $value;
    }
}

new ACF_Field_EpmeDynamic();
