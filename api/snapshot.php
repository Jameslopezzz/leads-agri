<?php
// Sends the logged-in user everything they are allowed to see.
require __DIR__ . '/helpers.php';
require __DIR__ . '/kpi_lib.php';
$u = require_login();
$kpi = kpi_allowed($u); // the IT KPI Matrix is only for the admin and immediate heads / supervisors
$missing = false;
try { if ($kpi) kpi_annual_check(); } // creates the yearly reminder (once) before the notifications are read below
catch (Throwable $e) { $missing = true; } // the KPI tables are not installed yet: the rest of the site keeps working
$data = $u['role'] === 'admin' ? snapshot_admin($u) : snapshot_employee($u);
if ($kpi && !$missing) {
    try { $data['kpi'] = kpi_out($u); } catch (Throwable $e) { $missing = true; }
}
if ($missing && $u['role'] === 'admin') $data['kpiSetup'] = true;
out($data);
