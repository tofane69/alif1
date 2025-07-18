<?php
// File: api.php (FINAL GUARANTEED VERSION - 6.0 - Simple & Foolproof)

ini_set('display_errors', 1); error_reporting(E_ALL);
date_default_timezone_set('Asia/Tehran');

require_once 'config.php';
// We NO LONGER need jdf.php

header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['action'])) { send_json_response('error', 'Invalid Request'); }

$action = $input['action'];
switch ($action) {
    case 'neshan_proxy': handle_neshan_proxy($input); break;
    case 'submit_report': handle_submit_report($input); break;
    case 'find_by_phone': handle_find_by_phone($input); break;
    case 'reverse_geocode': handle_reverse_geocode($input); break;
    case 'request_delete_report': handle_request_delete($input); break;
    default: send_json_response('error', 'Invalid action'); break;
}

function handle_submit_report($data) {
    global $mysqli;
    // We now expect gregorianDate from the client
    $required_fields = ['phoneNumber', 'province', 'gregorianDate', 'reportTime', 'problemTypes', 'lat', 'lng'];
    foreach ($required_fields as $field) { if (empty($data[$field])) { send_json_response('error', "فیلد ضروری '{$field}' خالی است."); return; }}

    $phone = preg_replace('/[^0-9]/', '', $data['phoneNumber']);
    if (strlen($phone) == 10 && strpos($phone, '9') === 0) { $phone = '0' . $phone; }
    if (!preg_match('/^09\d{9}$/', $phone)) { send_json_response('error', 'فرمت شماره تماس نامعتبر است.'); return; }

    // --- FINAL GUARANTEED DATE FIX: We receive a clean 'YYYY-MM-DD' string ---
    $datetime_str = $data['gregorianDate'] . ' ' . $data['reportTime'];
    $date_obj = DateTime::createFromFormat('Y-m-d H:i', $datetime_str);

    if ($date_obj === false) {
        send_json_response('error', 'تاریخ یا ساعت وارد شده نامعتبر است.');
        return;
    }
    $mysql_datetime = $date_obj->format('Y-m-d H:i:s');
    // --- END OF FOOLPROOF FIX ---

    $problem_types_json = json_encode($data['problemTypes'], JSON_UNESCAPED_UNICODE);
    $sql = "INSERT INTO reports (phone_number, province, report_datetime, problem_types, lat, lng, status) VALUES (?, ?, ?, ?, ?, ?, 'active')";
    $stmt = $mysqli->prepare($sql);
    if ($stmt === false) { send_json_response('error', 'Database Error: ' . $mysqli->error); return; }

    $stmt->bind_param('ssssdd', $phone, $data['province'], $mysql_datetime, $problem_types_json, $data['lat'], $data['lng']);
    if ($stmt->execute()) { send_json_response('success', 'گزارش شما با موفقیت ثبت شد.'); }
    else { send_json_response('error', 'خطا در ثبت گزارش: ' . $stmt->error); }
    $stmt->close();
}

// --- ALL OTHER FUNCTIONS ARE UNCHANGED ---
function handle_neshan_proxy($data) { if (empty($data['query']) || empty($data['type'])) { send_json_response('error', 'Missing query or type.'); return; } $query = urlencode($data['query']); $type = $data['type']; if ($type === 'search') { $url = "https://api.neshan.org/v1/search?term={$query}&lat=35.6892&lng=51.3890"; } elseif ($type === 'geocode') { $url = "https://api.neshan.org/v6/geocoding?address={$query}"; } else { send_json_response('error', 'Invalid Neshan API type specified.'); return; } if (!defined('NESHAN_API_KEY')) { send_json_response('error', 'Neshan API Key is not configured.'); return; } $ch = curl_init($url); curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Api-Key: ' . NESHAN_API_KEY], CURLOPT_TIMEOUT => 15]); $result_body = curl_exec($ch); $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $curl_error = curl_error($ch); curl_close($ch); if($curl_error) { send_json_response('error', 'cURL Error: ' . $curl_error); return; } if ($http_code !== 200) { send_json_response('error', "Neshan API returned status {$http_code}."); return; } echo json_encode(['status' => 'success', 'result' => json_decode($result_body)], JSON_UNESCAPED_UNICODE); }
function handle_find_by_phone($data) { global $mysqli; if (empty($data['phone'])) { send_json_response('error', 'شماره تماس ارائه نشده است.'); return; } $phone = preg_replace('/[^0-9]/', '', $data['phone']); if (strlen($phone) == 10 && strpos($phone, '9') === 0) { $phone = '0' . $phone; } $sql = "SELECT id, province, report_datetime, problem_types, lat, lng, status FROM reports WHERE phone_number = ? AND status != 'deleted' ORDER BY report_datetime DESC"; $stmt = $mysqli->prepare($sql); $stmt->bind_param('s', $phone); $stmt->execute(); $result = $stmt->get_result(); $reports = $result->fetch_all(MYSQLI_ASSOC); $stmt->close(); send_json_response('success', 'جستجو انجام شد', ['normalized_phone' => $phone, 'results' => $reports]); }
function handle_request_delete($data) { global $mysqli; if (empty($data['report_id'])) { send_json_response('error', 'ID گزارش برای حذف مشخص نشده است.'); return; } $report_id = (int)$data['report_id']; $sql = "UPDATE reports SET status = 'pending_deletion' WHERE id = ? AND status = 'active'"; $stmt = $mysqli->prepare($sql); $stmt->bind_param('i', $report_id); if ($stmt->execute()) { if ($stmt->affected_rows > 0) { send_json_response('success', 'درخواست حذف ثبت شد.'); } else { send_json_response('error', 'گزارش یافت نشد یا قبلا درخواست حذف شده است.'); } } else { send_json_response('error', 'خطا در ثبت درخواست: ' . $stmt->error); } $stmt->close(); }
function handle_reverse_geocode($data) { if (empty($data['lat']) || empty($data['lng'])) { send_json_response('error', 'مختصات ناقص است.'); return; } $url = "https://api.neshan.org/v5/reverse?lat={$data['lat']}&lng={$data['lng']}"; $ch = curl_init($url); curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Api-Key: ' . NESHAN_API_KEY]]); $result_body = curl_exec($ch); curl_close($ch); echo json_encode(['status' => 'success', 'result' => json_decode($result_body)], JSON_UNESCAPED_UNICODE); }
function send_json_response($status, $message, $data = null) { $response = ['status' => $status, 'message' => $message]; if ($data !== null) { $response = array_merge($response, $data); } echo json_encode($response, JSON_UNESCAPED_UNICODE); exit(); }
