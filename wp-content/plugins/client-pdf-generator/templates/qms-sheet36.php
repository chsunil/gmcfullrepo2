<?php
/**
 * QMS – Sheet36 Assessment Check List (Surveillance Year 2)
 * ACF Group: group_bff8a27d39dd
 * Field:
 *   s2_assessment_check_list — matrix_flexible (REQUIREMENTS: ISO 9001)
 */
if ( ! defined('ABSPATH') ) exit;

$LOGO = '';
require __DIR__ . '/_logo.inc.php';

$matrix = get_field( 's2_assessment_check_list', $post_id );
if ( ! is_array($matrix) ) $matrix = [];
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 landscape; margin: 12mm; }
body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 8px; }
h1 { text-align: center; font-size: 14px; font-weight: bold; margin: 0 0 2px 0; text-transform: uppercase; }
h2 { text-align: center; font-size: 10px; margin: 0 0 12px 0; color: #444; }
table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
th, td { border: 1px solid #555; padding: 4px 5px; vertical-align: top; text-align: left; }
th { background: #d9d9d9; font-weight: bold; text-align: center; font-size: 9px; text-transform: uppercase; }
.h-logo { border: none; text-align: center; }
.no-data { text-align: center; color: #888; font-style: italic; padding: 12px; }
</style>
</head>
<body>
<?php if ( $LOGO ) : ?>
<table style="margin-bottom:4px;"><tr><td class="h-logo" style="border:none;"><img alt="GMCSPL Logo" src="<?= $LOGO ?>" style="max-height:70px; width:auto;" /></td></tr></table>
<?php endif; ?>
<h1>Assessment Check List</h1>
<h2>Sheet 36 &nbsp;|&nbsp; QMS Surveillance Year 2 &nbsp;|&nbsp; Version 1.00</h2>
<h2 style="font-size:10px; font-weight:bold; margin-bottom:8px;">REQUIREMENTS: ISO 9001</h2>

<?php if ( ! empty($matrix) ) :
    $first = reset($matrix); $cols = is_array($first) ? array_keys($first) : [];
?>
<table>
    <?php if ( $cols ) : ?><thead><tr><?php foreach ($cols as $c) : ?><th><?= esc_html($c) ?></th><?php endforeach; ?></tr></thead><?php endif; ?>
    <tbody>
    <?php foreach ( $matrix as $mrow ) : ?>
        <tr><?php foreach ( (array) $mrow as $cell ) : ?><td><?= esc_html( (string) $cell ) ?></td><?php endforeach; ?></tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else : ?>
<p class="no-data">Assessment checklist matrix data not available.</p>
<?php endif; ?>

<table style="margin-top:20px;">
    <tr>
        <td style="border:none; padding-top:25px; width:50%;">Lead Auditor: ___________________________</td>
        <td style="border:none; padding-top:25px; width:50%; text-align:right;">Date: ___________________________</td>
    </tr>
</table>
</body>
</html>
