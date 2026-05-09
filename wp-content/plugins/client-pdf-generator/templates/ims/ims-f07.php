<?php
/**
 * IMS – F-07 Certification Assessment Plan
 * ACF Group: group_ims_f07
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html($org_raw)
         : esc_html( get_post_field('post_title', $post_id) );

$ref_no = get_field('refno', $post_id) ?: get_field('proposal_ref_no', $post_id) ?: '-';

$matrix = get_field( 'quality_system_requirements', $post_id ) ?: [];
$proc_cols = ! empty($matrix) ? array_keys( reset($matrix) ) : [];

$logo_b64 = 'data:image/jpeg;base64,...'; // Omitted for brevity
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { size: A4 portrait; margin: 12mm 10mm 12mm 10mm; }
    body { font-family: Arial, sans-serif; font-size: 8px; color: #333; margin: 0; padding: 0; line-height: 1.2; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    th, td { border: 1px solid #555; padding: 3px; vertical-align: middle; text-align: center; }
    
    .h-logo { border: none; width: 15%; text-align: center; }
    .h-title { border: none; text-align: center; font-size: 14pt; font-weight: bold; color: #2c3e50; }
    .h-form { border: none; text-align: right; font-size: 8px; vertical-align: bottom; width: 20%; color: #7f8c8d; }

    .lbl { background: #f8f9fa; font-weight: bold; text-align: left; width: 22%; }
    .legend { font-size: 7.5px; border: 1px solid #bbb; padding: 4px; margin-bottom: 6px; background: #fafafa; }
    
    .rot-th { height: 75px; vertical-align: bottom; padding: 0 1px 2px 1px; }
    .rot-label { display: block; white-space: nowrap; font-bold; transform: rotate(-90deg); transform-origin: left bottom; margin-left:14px; margin-bottom: 2px; }

    .clause { text-align: left; padding-left: 4px; font-weight: normal; }
    .clause-section { background: #ecf0f1; font-weight: bold; text-align: left; padding-left: 4px; }
    .val-P { font-weight: bold; color: #2c3e50; background: #e8f6f3; }
</style>
</head>
<body>

<table>
    <tr>
        <td class="h-logo"><img src="data:image/jpeg;base64,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" alt="Logo"></td>
        <td class="h-title">Certification Assessment Plan<br><span style="font-size:10px; font-weight:normal; color:#777;">IMS (9001, 14001 & 45001)</span></td>
        <td class="h-form">F-07<br><strong>(Version 5.00, 30.10.2023)</strong></td>
    </tr>
</table>

<div class="legend">
    <strong>P</strong> = Primary area / process for Integrated assessment &nbsp;&nbsp;
    <strong>R</strong> = Significantly relevant area &nbsp;&nbsp;
    <strong>Blank</strong> = Not applicable / Insignificant
</div>

<table>
    <tr>
        <td class="lbl">Organization</td>
        <td style="text-align:left; font-weight:bold; width:45%;"><?= $org ?></td>
        <td class="lbl" style="width:10%;">Ref. No.</td>
        <td style="text-align:left;"><?= $ref_no ?></td>
    </tr>
</table>

<?php if (!empty($matrix)) : ?>
<table>
    <thead>
        <tr>
            <th style="width:35%; text-align:left; font-size:9px;">Integrated Requirements</th>
            <?php foreach ($proc_cols as $col) : ?>
            <th class="rot-th"><div class="rot-label"><?= esc_html($col) ?></div></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($matrix as $clause => $vals) : 
            $is_header = preg_match('/^\d+\s+\w+/', $clause) === 0 || strlen($clause) < 5;
        ?>
        <tr>
            <td class="<?= $is_header ? 'clause-section' : 'clause' ?>"><?= esc_html($clause) ?></td>
            <?php foreach ($proc_cols as $col) : 
                $v = strtoupper(trim($vals[$col] ?? ''));
            ?>
            <td class="<?= $v === 'P' ? 'val-P' : '' ?>"><?= esc_html($v) ?></td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>
