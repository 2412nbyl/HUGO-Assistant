<?php
// White Box Test Script for HUGO-Assistant

$results = [];
$base = __DIR__;

// ─── CLIENT LIST ───────────────────────────────────────────────────────────────
$clientIdx = file_get_contents($base . '/resources/views/clients/index.blade.php');
$results['CLIENT_LIST__subtitle_removed']   = strpos($clientIdx, 'Kelola semua data klien notaris') === false ? 'PASS' : 'FAIL';
$results['CLIENT_LIST__scroll_wrapper']     = strpos($clientIdx, 'cl-table-scroll') !== false             ? 'PASS' : 'FAIL';
$results['CLIENT_LIST__overflow_x_auto']    = strpos($clientIdx, 'overflow-x: auto')   !== false          ? 'PASS' : 'FAIL';
$results['CLIENT_LIST__mobile_min_width']   = strpos($clientIdx, 'min-width: 620px')   !== false          ? 'PASS' : 'FAIL';
$results['CLIENT_LIST__hapus_button']       = strpos($clientIdx, 'Hapus')              !== false          ? 'PASS' : 'FAIL';

// ─── EDIT KLIEN ────────────────────────────────────────────────────────────────
$editKlien = file_get_contents($base . '/resources/views/clients/edit.blade.php');
$results['EDIT_KLIEN__sticky_footer_class'] = strpos($editKlien, 'edit-klien-footer')    !== false ? 'PASS' : 'FAIL';
$results['EDIT_KLIEN__position_sticky']     = strpos($editKlien, 'position: sticky')     !== false ? 'PASS' : 'FAIL';
$results['EDIT_KLIEN__notes_height_limit']  = strpos($editKlien, 'edit-klien-notes-field') !== false ? 'PASS' : 'FAIL';
$results['EDIT_KLIEN__push_styles_stack']   = strpos($editKlien, "@push('styles')")       !== false ? 'PASS' : 'FAIL';
$results['EDIT_KLIEN__simpan_button']       = strpos($editKlien, 'Simpan Perubahan')      !== false ? 'PASS' : 'FAIL';
$results['EDIT_KLIEN__catatan_field']       = strpos($editKlien, 'Catatan Khusus')        !== false ? 'PASS' : 'FAIL';

// ─── ARCHIVE DOCUMENT ─────────────────────────────────────────────────────────
$archiveIdx = file_get_contents($base . '/resources/views/archives/index.blade.php');
$results['ARCHIVE__subtitle_removed']       = strpos($archiveIdx, 'Folder arsip terhubung dengan dokumen kasus') === false ? 'PASS' : 'FAIL';
$results['ARCHIVE__buat_folder_button']     = strpos($archiveIdx, 'Buat Folder Arsip') !== false ? 'PASS' : 'FAIL';

// ─── FINISHED CASES VIEW ──────────────────────────────────────────────────────
$fcViewExists = file_exists($base . '/resources/views/finished_cases/index.blade.php');
$results['FINISHED_CASES__view_file_exists']= $fcViewExists ? 'PASS' : 'FAIL';
if ($fcViewExists) {
    $fcIdx = file_get_contents($base . '/resources/views/finished_cases/index.blade.php');
    $results['FINISHED_CASES__page_title']      = strpos($fcIdx, 'Dokumen Kasus Selesai') !== false ? 'PASS' : 'FAIL';
    $results['FINISHED_CASES__storage_box']     = strpos($fcIdx, 'fc-storage-box')        !== false ? 'PASS' : 'FAIL';
    $results['FINISHED_CASES__buka_folder_btn'] = strpos($fcIdx, 'Buka Folder')            !== false ? 'PASS' : 'FAIL';
    $results['FINISHED_CASES__js_openFolder']   = strpos($fcIdx, 'openLocalFolder')        !== false ? 'PASS' : 'FAIL';
    $results['FINISHED_CASES__view_btn']        = strpos($fcIdx, 'fc-btn-view')             !== false ? 'PASS' : 'FAIL';
    $results['FINISHED_CASES__download_btn']    = strpos($fcIdx, 'Unduh')                   !== false ? 'PASS' : 'FAIL';
    $results['FINISHED_CASES__mobile_css']      = strpos($fcIdx, 'max-width: 768px')        !== false ? 'PASS' : 'FAIL';
    $results['FINISHED_CASES__csrf_token']      = strpos($fcIdx, 'csrf_token()')            !== false ? 'PASS' : 'FAIL';
}

// ─── SIDEBAR ──────────────────────────────────────────────────────────────────
$sidebar = file_get_contents($base . '/resources/views/partials/sidebar.blade.php');
$results['SIDEBAR__finished_cases_label']   = strpos($sidebar, 'Finished Cases')               !== false ? 'PASS' : 'FAIL';
$results['SIDEBAR__finished_cases_route']   = strpos($sidebar, "route('finished-cases.index')") !== false ? 'PASS' : 'FAIL';
$results['SIDEBAR__archive_doc_still']      = strpos($sidebar, 'Archive Document')             !== false ? 'PASS' : 'FAIL';
$results['SIDEBAR__active_class_logic']     = strpos($sidebar, "request()->is('finished-cases*')") !== false ? 'PASS' : 'FAIL';

// ─── CONTROLLER ───────────────────────────────────────────────────────────────
$ctrlExists = file_exists($base . '/app/Http/Controllers/FinishedCaseController.php');
$results['CONTROLLER__file_exists']         = $ctrlExists ? 'PASS' : 'FAIL';
if ($ctrlExists) {
    $ctrl = file_get_contents($base . '/app/Http/Controllers/FinishedCaseController.php');
    $results['CONTROLLER__index_method']        = strpos($ctrl, 'public function index')      !== false ? 'PASS' : 'FAIL';
    $results['CONTROLLER__openFolder_method']   = strpos($ctrl, 'public function openFolder') !== false ? 'PASS' : 'FAIL';
    $results['CONTROLLER__windows_explorer']    = strpos($ctrl, 'start explorer')             !== false ? 'PASS' : 'FAIL';
    $results['CONTROLLER__archive_lookup']      = strpos($ctrl, 'Archive::where')             !== false ? 'PASS' : 'FAIL';
    $results['CONTROLLER__status_selesai']      = strpos($ctrl, "'selesai'")                  !== false ? 'PASS' : 'FAIL';
    $results['CONTROLLER__freelancer_check']    = strpos($ctrl, "'freelancer'")               !== false ? 'PASS' : 'FAIL';
}

// ─── ROUTES ───────────────────────────────────────────────────────────────────
$routes = file_get_contents($base . '/routes/web.php');
$results['ROUTES__finished_cases_index']    = strpos($routes, 'finished-cases.index')              !== false ? 'PASS' : 'FAIL';
$results['ROUTES__finished_cases_folder']   = strpos($routes, 'finished-cases.open-folder')        !== false ? 'PASS' : 'FAIL';
$results['ROUTES__archives_restricted']     = strpos($routes, "role:admin,notaris,staff")           !== false ? 'PASS' : 'FAIL';
$results['ROUTES__reports_restricted']      = strpos($routes, "'reports.index'")                   !== false ? 'PASS' : 'FAIL';
$results['ROUTES__fc_in_operational_group'] = strpos($routes, 'FinishedCaseController')            !== false ? 'PASS' : 'FAIL';

// ─── OUTPUT ───────────────────────────────────────────────────────────────────
$pass = 0; $fail = 0;
$separator = str_repeat('─', 60);

echo "\n$separator\n";
echo "  HUGO-ASSISTANT — WHITE BOX TEST REPORT\n";
echo "$separator\n\n";

$section = '';
foreach ($results as $key => $val) {
    $parts   = explode('__', $key, 2);
    $newSect = $parts[0] ?? '';
    if ($newSect !== $section) {
        $section = $newSect;
        echo "\n  [" . str_replace('_', ' ', $section) . "]\n";
    }
    $label = str_pad(str_replace('_', ' ', $parts[1] ?? $key), 40);
    $icon  = $val === 'PASS' ? '✓' : '✗';
    echo "    $icon  $label  $val\n";
    $val === 'PASS' ? $pass++ : $fail++;
}

echo "\n$separator\n";
echo "  RESULT: $pass PASSED | $fail FAILED\n";
echo $separator . "\n\n";
