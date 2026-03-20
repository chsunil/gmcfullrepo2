<?php
/**
 * F-11 Stage-1 Audit Report — acf/load_value pre-population
 *
 * Fires when the F-11 ACF form is rendered on the frontend.
 * Each filter: if the F-11 field is still empty (never saved), pull from
 * the original source field so the auditor sees a pre-filled form.
 * Once the auditor saves, their value is stored independently.
 *
 * ACF field names in group_qms_f11 (verified from acf-export-2026-03-14.json):
 *   organization_name    — clone of field_org_name (draft text field)
 *   f11address           — clone of head_office sub-field (address group)
 *   f11management_representative — clone of f01contact_person group
 *   f11top_management    — clone of top management group (field_67d5ae289f03b)
 *   f11audit_criteria_standard   — clone of cert_scheme (F01 text)
 *   f11exclusions        — clone of exclusions_only_for_iso_9001 (F01 text)
 *   f11audit_objectives  — textarea (standalone, has default in template)
 *   f11audit_scope_confirmed     — clone of scope_of_certification (F01 text)
 *   audit_sites          — clone (points to f11address key)
 *   f11dates_of_audit    — date_picker (STANDALONE — needs explicit pre-fill)
 *   technical_code       — clone of field_qms_f02_technical_area
 *   audit_team           — clone of field_audit_team
 *
 * Clone fields with prefix_name=0 share the same meta key as their source,
 * so they auto-populate if the source form was completed.
 * Only standalone fields (f11dates_of_audit) and empty-source fallbacks need filters.
 */

if ( ! defined('ABSPATH') ) exit;

// ── HELPER ────────────────────────────────────────────────────────────────────
if ( ! function_exists('f11_lv_scalar') ) {
    function f11_lv_scalar( $raw, $sub_key = null ) {
        if ( is_array($raw) ) {
            if ( $sub_key && isset($raw[ $sub_key ]) ) return (string) $raw[ $sub_key ];
            foreach ( ['name', 'contact_person_name', 'top_management', 'label', 'value'] as $k ) {
                if ( ! empty($raw[$k]) && is_string($raw[$k]) ) return $raw[$k];
            }
            foreach ( $raw as $v ) {
                if ( is_string($v) && $v !== '' ) return $v;
            }
            return '';
        }
        return is_string($raw) ? $raw : (string) $raw;
    }
}

// ── 1. Organization Name ──────────────────────────────────────────────────────
// Field name: organization_name (clone of field_org_name, draft text field)
// Clone shares meta key → auto-fills if draft was saved.
// Fallback: post_title (in case ACF field was never explicitly saved).
add_filter( 'acf/load_value/name=organization_name', function( $value, $post_id, $field ) {
    if ( $value ) return $value;
    // Only apply fallback for client post type
    if ( get_post_type( $post_id ) !== 'client' ) return $value;
    return get_post_field( 'post_title', $post_id );
}, 10, 3 );

// ── 2. Date(s) of Audit ───────────────────────────────────────────────────────
// Field name: f11dates_of_audit (standalone date_picker — NOT a clone)
// Must be explicitly pre-filled from Audit Dates page.
add_filter( 'acf/load_value/name=f11dates_of_audit', function( $value, $post_id, $field ) {
    if ( $value ) return $value;
    return get_field( 'stage1_audit_initial', $post_id ) ?: '';
}, 10, 3 );

// ── 3. Audit Criteria / Standard ─────────────────────────────────────────────
// Field name: f11audit_criteria_standard (clone of cert_scheme, F01 text)
// Clone shares meta key — but add fallback in case F01 was skipped.
add_filter( 'acf/load_value/name=f11audit_criteria_standard', function( $value, $post_id, $field ) {
    if ( $value ) return $value;
    return get_field( 'cert_scheme', $post_id ) ?: '';
}, 10, 3 );

// ── 4. Exclusions ─────────────────────────────────────────────────────────────
// Field name: f11exclusions (clone of exclusions_only_for_iso_9001, F01 text)
add_filter( 'acf/load_value/name=f11exclusions', function( $value, $post_id, $field ) {
    if ( $value ) return $value;
    return get_field( 'exclusions_only_for_iso_9001', $post_id ) ?: '';
}, 10, 3 );

// ── 5. Audit Scope ────────────────────────────────────────────────────────────
// Field name: f11audit_scope_confirmed (clone of scope_of_certification, F01 text)
add_filter( 'acf/load_value/name=f11audit_scope_confirmed', function( $value, $post_id, $field ) {
    if ( $value ) return $value;
    return get_field( 'scope_of_certification', $post_id ) ?: '';
}, 10, 3 );
