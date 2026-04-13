<?php
/**
 * IMS – F-13 Attendance Sheet
 * Reuses QMS field mapping but with IMS branding.
 */

if ( ! defined('ABSPATH') ) exit;

$post_id = $args['post_id'] ?? get_the_ID();

// ── Helpers ───────────────────────────────────────────────────────────────────
if ( ! function_exists('ims_f13_val') ) {
    function ims_f13_val( $v, $fallback = '-' ) {
        if ( $v === null || $v === '' || $v === false ) return $fallback;
        if ( is_array($v) ) {
            if ( isset($v['display_name']) ) return esc_html( $v['display_name'] );
            if ( isset($v['label']) )        return esc_html( $v['label'] );
            $flat = array_filter( array_map( fn($i) => is_string($i) ? $i : '', $v ) );
            return esc_html( implode(', ', $flat) ) ?: $fallback;
        }
        return esc_html( (string) $v );
    }
}

// ── Fields ────────────────────────────────────────────────────────────────────
$org_raw = get_field( 'organization_name', $post_id );
$org     = ( $org_raw && ! is_array($org_raw) ) ? esc_html( $org_raw )
         : ( is_array($org_raw) ? ims_f13_val($org_raw) : esc_html( get_post_field('post_title', $post_id) ) );

$ref_no  = ims_f13_val( get_field( 'f03proposal_ref_no', $post_id ) );

$date_raw = get_field( 'stage1_audit_initial', $post_id );
$date     = $date_raw ? date( 'd/m/Y', strtotime($date_raw) ) : '-';

$rows = get_field( 'ATTENDANCE_SHEET', $post_id ) ?: [];

function ims_f13_cb( $checked = false ) {
    return $checked ? '[X]' : '[  ]';
}

$stage = get_field( 'f05audit_stage', $post_id );
$is_stage1 = ( $stage === 'stage1' );
$is_stage2 = ( $stage === 'stage2' );
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 10px; line-height: 1.4; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    th, td { border: 1px solid #555; padding: 6px; vertical-align: top; }
    th { background: #2c3e50; color: #fff; font-weight: bold; text-align: center; text-transform: uppercase; font-size: 9px; }
    
    .h-logo  { border: none; text-align: center; vertical-align: middle; width: 15%; }
    .h-title { border: none; text-align: center; vertical-align: middle; }
    .h-title h1 { margin: 0; font-size: 16px; color: #1a252f; }
    .h-title span { font-size: 10px; color: #7f8c8d; font-weight: normal; }
    .h-form  { border: none; text-align: right; font-size: 9px; vertical-align: bottom; width: 20%; color: #7f8c8d; }

    .lbl { background: #f8f9fa; font-weight: bold; width: 22%; color: #2c3e50; }
    .section-title { background: #ecf0f1; font-weight: bold; padding: 6px; margin: 10px 0 4px 0; border: 1px solid #555; border-left: 4px solid #34495e; font-size: 10px; text-transform: uppercase; }
    .cb-group { font-size: 9px; line-height: 1.6; }
    .center { text-align: center; }
</style>
</head>
<body>

<table>
    <tr>
        <td class="h-logo"><img src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2ODApLCBxdWFsaXR5ID0gODIK/9sAQwAGBAQFBAQGBQUFBgYGBwkOCQkICAkSDQ0KDhUSFhYVEhQUFxohHBcYHxkUFB0nHR8iIyUlJRYcKSwoJCshJCUk/9sAQwEGBgYJCAkRCQkRJBgUGCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQkJCQk/8AAEQgAXABkAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A+qaKKKACiiigAooooAKKRXViQrA4ODg9DS0AFFFFABRRRQQUUUUAFFFFABRRRQAVkeK/EVv4X0O51KfB8tcRpnmRz91fz/TNa54FeD/FDxBeeMPEB0rSYLi7tNPJXECF98nRm49Og/H1rnxNb2cLrfocOYYv6vSco/E9F6ln4VePbiLxJcWeqzl49VlMgdjws5/kD0/AV7eOa+WJ/C/iGwUTy6NqUCqd2827jbjvnHFe+/DnxaPFfh+OWVh9tt8RXK99w6N+I5+ua5cDWl/DnueZkuLm06Fa991c6qiiivSPoQooooAKKKKACiiigAoooJAoA5/xx4t0rwdoc19qkjBHBjjijOJJmI+6v+Pavn0+P/HHiqRrLwnYT6fZIcLbaVAflHq8gGc++RWp47kn+Jnxhh8OCZks7Wb7KuP4Qo3St9eCPwFbviL4tD4Y61ceFNH8OWQsrDYqN5jKWyisScDk89e9e7hsMqSjywU6jV9dkj5zFYj20pSlPkpxdrrds6268XT6L/Yfh/VZ5rKa902Em9JzJBcYwd+cggkc5/H2o+H/ABlDpPi6bTPEljb2WqkiH7dbjZHcg8qXHvwQ3vjisvxj4l8O6lbaDq2vaHcXV1e6dHcKkNwURA3O31POeal1/wAbbfC+l+JdI0mxhuJHayaW4TzpYdmdoDHrwCea+Ur1HGrK71T2KqYrlm3zr3bO1r6abrv53PYQciiuQ+F/ii48UeGVnvZPMu4JWhlfAG48EHA9iPyrr666c1OKkup7lGrGrBVI7MKKKKs1CiiigAooooAK898f+OE03xBo2hwS7S13DLeMD92PeML+PU+wHrXW+J9ft/DOi3Op3JBWJflTPLueij6mvmHUtSudW1C41C6kL3E7mRm9/b2HauDG4n2aUVuzxM4zD6vFU4fE/wAv+Ca2n33/AAi/x6mnvD5aHVJlZm6BZdwUn2w4NXP2hvDUGleI7fWY55Xl1bc0iNjanlqijH1qDxtpD+O9Ec8WaYhl1OziWDVbZBlyFGFmA7jHX6exrivEPjTWPFNjptpqsy3A05WSKUr+8ZW28Me+No56+ua+4wMliXSxVJ9LNf15ni1qsY0p0mrqT5os9a1LxCmi+GPCSSeHtN1MPo8DebdwlynHQH071dvPF1va/D+wvpPDejYur1xHaGH90AoILgZ654zTIdb8Twaf4K0fw9OFN1pEDMjRK6jjBYkg4AFaGqeIbfxR4lTwymg2et2dqNktzkxGN/8AlpIGHCrn8yPpXxOKb9vUs+ttup3uTvK07NpJadbL1b+4zYvG0lj4Ni1mz0uw0txqqKkVomxZlVCW3evBxXr2jarba3pltqNo++G4QOvqPUH3B4r548d6vp9xc2+jaINukaYGSE7s+Y7HLvnvzwPp711HwW8X/Yb5/D13JiC5Je2LHhZO6/iP1HvXPh8Var7NvTb5hgcx5cT7CburJX8/60PbKKBRXqn0wUUUUAFFFFAHmnxMt9O8R6na6Rd+IJLEQFC8SWbyoryHahkcHauegz7+tc9a/BuwvL6/sYfEzGfTyi3CmzICFl3LyWwePSuy17wLq154hu9T0zUo7aO+e0eb53R08knIG3hgykgg/WpdX8CXepDxUEvIY/7aa2aLIJCeUqgq/qG2447GieCw1R80t3/wP+CeNWwMa1RzqU7v1e2tuvocrb/DVfB17Y38HjBrWW5mFvC62e5ZGYE7W+fGCFPXjitnX/gx4U8QBbi9VbK/l+/cWGIVlY99h3Lz7c1Lc/Di8m8OW2m+ZYM0WqSaiYXU+QqtvxEox0G8du3Sodd+GWp6n4f0bSodStc6fY/ZmkkjOTJ8mHVhkgfIRjI6g84xXRhqVPDv9zPl+8cMJCEXFUtO1+vzN/S9K0eKA+F7a4n+1abYwW8sqjbL5JyFG/Hfa2dv6cVznifQ9NtLm08I2Gsx6DFqKkrbwWTO1xjrvl3focZ967DTtBms/FOq6y8qNFe29vCqDO5THvyT9d4/KsPxx4Dv/E+qxahZX0NpLb2hihZgSVl81JFbjt8hB+tZulSqT/ePRrfz/wCHOitRcqWkNdvlf/I84tPh94dvNGvNZh8XObGzl8mWU2DDD/LgAbstncuMdc1ND8PNHttPGuReLLiJILkQD/iXOJknDABNm7duzjjFdvB8MJrbwlqmgxXkKm4uorm3faSE8tYgoYe5i5x61p6n4Z1zWfDk9jqF1plzczXXnGN4D5Ii3AiIEfNnA+/1BrJ5fg09O/focEcrppXdPW3nv95vaBqkGsaTb3cE5nvnl1bIyhJZThso3K8g8GtCsfwlpF9oeg21hqN819cRbszEk8FiQuTycAgZPJxWxVySUmo7HuU78q5twoooqSwooooAKKKKACiiigAooooAKKKKACiiigAooooA/9k=" alt="Logo"></td>
        <td class="h-title">
            <h1>ATTENDANCE SHEET</h1>
            <span>IMS (QMS, EMS & OH&SMS)</span>
        </td>
        <td class="h-form">F-13<br><strong>(Version 5.00, 30.10.2023)</strong></td>
    </tr>
</table>

<div class="section-title">Audit Context</div>
<table>
    <tr>
        <td class="lbl">Organization</td>
        <td><?= $org ?></td>
        <td class="lbl">Phase</td>
        <td class="cb-group">
            <?= ims_f13_cb($is_stage1) ?> Stage-1 <br>
            <?= ims_f13_cb($is_stage2) ?> Stage-2
        </td>
    </tr>
    <tr>
        <td class="lbl">Ref No.</td>
        <td><?= $ref_no ?></td>
        <td class="lbl">Date</td>
        <td><?= $date ?></td>
    </tr>
</table>

<div class="section-title">Attendance Record</div>
<table>
    <thead>
        <tr>
            <th style="width:7%">S.No</th>
            <th style="width:28%">Name</th>
            <th style="width:25%">Designation & Dept</th>
            <th style="width:20%">Opening Meeting</th>
            <th style="width:20%">Closing Meeting</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($rows)) : ?>
            <?php foreach ($rows as $row) : 
                $sno = $row['sno'] ?? '';
                $name = $row['name'] ?? '-';
                $desig = $row['designation_&_department'] ?? '-';
                $open = $row['opening_meeting'] ?? '-';
                $close = $row['closing_meeting'] ?? '-';
            ?>
            <tr>
                <td class="center"><?= esc_html($sno) ?></td>
                <td><?= esc_html($name) ?></td>
                <td><?= esc_html($desig) ?></td>
                <td class="center"><?= esc_html($open) ?></td>
                <td class="center"><?= esc_html($close) ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="5" class="center">No records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<table style="margin-top:20px; border:none;">
    <tr>
        <td style="width:50%; border:none; text-align:center;">
            <br><br>
            __________________________<br>
            <strong>Lead Auditor Signature</strong>
        </td>
        <td style="width:50%; border:none; text-align:center;">
            <br><br>
            __________________________<br>
            <strong>Client Representative Signature</strong>
        </td>
    </tr>
</table>

</body>
</html>
