<?php
/**
 * Template Name: QMS F-02 Full Technical Review PDF
 */
if (!defined('ABSPATH')) exit;

$post_id = get_the_ID();

// Basic ACF helper
function f02val($key, $label = '', $color = 'black') {
    $val = get_field($key);
    $value = (!empty($val) && !is_array($val)) ? $val : '-';
    return "<div class='line'><span class='label' style='color:blue'>{$label}</span> <span style='color:{$color}'>{$value}</span></div>";
}

function f02group($group, $key, $label, $color = 'black') {
    $value = (!empty($group[$key]) && !is_array($group[$key])) ? $group[$key] : '-';
    return "<div class='line'><span class='label' style='color:blue'>{$label}</span> <span style='color:{$color}'>{$value}</span></div>";
}
?>
<style>
    body { font-family: sans-serif; font-size: 11px; line-height: 1.6; }
    .section { margin-top: 20px; font-weight: bold; color: navy; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
    .line { margin: 4px 0; }
    .label { font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; margin-bottom: 12px; font-size: 10px; }
    td, th { border: 1px solid #333; padding: 4px; }
    th { background: #eef; }
</style>

<h1 style='text-align:center;'>Review of Application ( QMS)</h1>

<!-- SECTION: Application Review Summary -->
<small>Note to user: The requirements must not be altered. The text in blue italics is only the guidance and should be deleted on completion of the review. Information should be taken from duly filled in Form      F-01 received from client. Conclusion in the last column refers whether or not the requirement have been met. </small>
<?php
echo f02val('applicant_information_sufficient', 'Is information provided by applicant sufficient?');
echo f02val('interpreter_required', 'Interpreter Required?');
echo f02val('language_requirement', 'Language Requirements:');
echo f02val('scope_clear', 'Is Scope of Certification clearly defined?');
echo f02val('legal_requirements_reviewed', 'Legal/Statutory/Regulatory Requirements reviewed?');
echo f02val('certification_feasible', 'Certification Feasible?');
?>

<!-- SECTION: Review Requirements Table -->
<div class="section">Review of Requirements</div>
<table>
    <thead>
        <tr><th>S.No</th><th>Requirement</th><th>OK / Not OK</th><th>Remarks</th></tr>
    </thead>
    <tbody>
    <?php
    $requirements = get_field('review_requirements_list');
    if ($requirements) {
        $i = 1;
        foreach ($requirements as $req) {
            echo "<tr>
                <td>{$i}</td>
                <td>{$req['requirement']}</td>
                <td>{$req['status']}</td>
                <td>{$req['remarks']}</td>
            </tr>";
            $i++;
        }
    } else {
        echo "<tr><td colspan='4'>-</td></tr>";
    }
    ?>
    </tbody>
</table>

<!-- SECTION: Audit Time Calculation -->
<div class="section">Audit Time Calculation</div>
<?php
echo f02val('mandays_p07', 'Mandays as per P-07:');
echo f02val('additional_md_justification', 'Adjustments Justification:');
echo f02val('final_mandays', 'Final Mandays Allotted:');
?>

<!-- SECTION: Employee / Shift Info -->
<div class="section">Manpower / Shifts</div>
<?php
echo f02val('total_employees', 'Total Effective Employees:');
echo f02val('no_of_shifts', 'No. of Shifts:');
echo f02val('working_hours', 'Working Hours per shift:');
?>

<!-- SECTION: Stage-1 Review -->
<div class="section">Review after Stage-1</div>
<?php
$stage1 = get_field('review_after_stage_1');
echo f02group($stage1, 'changes_identified', 'Changes Identified:');
echo f02group($stage1, 'audit_team_conclusion', 'Audit Team Conclusion:');
echo f02group($stage1, 'stage1_remarks', 'Remarks:');
?>

<!-- SECTION: Surveillance Review -->
<div class="section">Review after Surveillance</div>
<?php
$surv = get_field('review_after_surveillance');
echo f02group($surv, 'changes_identified', 'Changes Identified:');
echo f02group($surv, 'audit_team_conclusion', 'Audit Team Conclusion:');
echo f02group($surv, 'stage1_remarks', 'Remarks:');
?>

<!-- SECTION: Annexure - Risk Factors -->
<div class="section">Annexure - Risk Factors for Man-day Adjustments</div>
<table>
    <thead>
        <tr><th>Risk Area</th><th>Yes</th><th>No</th><th>Remarks</th></tr>
    </thead>
    <tbody>
    <?php
    $risks = get_field('annexure_risk_factors');
    if ($risks) {
        foreach ($risks as $r) {
            $yes = ($r['choice'] == 'Yes') ? '✔' : '';
            $no  = ($r['choice'] == 'No')  ? '✔' : '';
            echo "<tr>
                <td>{$r['risk_area']}</td>
                <td style='text-align:center'>{$yes}</td>
                <td style='text-align:center'>{$no}</td>
                <td>{$r['remarks']}</td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>-</td></tr>";
    }
    ?>
    </tbody>
</table>
