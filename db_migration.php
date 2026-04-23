<?php
// Database Update Script for Checklist Modifications

$conn = new mysqli("localhost", "root", "Hbl@1234", "loco_info");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = [
    "pneumatic_fittings_and_ep_valve_cocks_fixing",
    "loco_antenna_and_gps_gsm_antenna",
    "Rfid_ps_unit",
    "dmi_lp_ocip",
    "rib_cab_input_box",
    "emi_filter_box",
    "loco_kavach",
    "verify_serial_numbers_of_equipment_as_per_ic",
    "document_verification_table",
    "radio_power",
    "earthing",
    "rfid_reader_assembly",
    "pgs_and_speedo_meter_units_fixing",
    "sifa_valve_fixing_for_ccb_type_loco",
    "psjb_tpm_units_fixing_for_ccb_type_loco",
    "iru_faviely_units_fixing_for_e70_type_loco",
    "pressure_sensors_installation_in_loco"
];

// ==========================================
// 1. DELETE POINTS
// ==========================================
$deleted_points = [""];

if (!empty($deleted_points) && count($deleted_points) > 0 && $deleted_points[0] !== "") {
    $placeholders = implode(',', array_fill(0, count($deleted_points), '?'));
    $types = str_repeat("s", count($deleted_points));
    
    $total_deleted = 0;
    foreach ($tables as $table) {
        $stmt = $conn->prepare("DELETE FROM $table WHERE S_no IN ($placeholders)");
        if ($stmt) {
            $stmt->bind_param($types, ...$deleted_points);
            $stmt->execute();
            $total_deleted += $stmt->affected_rows;
            $stmt->close();
        }
    }
    echo "Deleted " . $total_deleted . " obsolete observations.<br>";
}

// ==========================================
// 2. RENAME / UPDATE TEXT
// ==========================================
$renamed_points = [
    // "1.39" => "SMOCIP Unit",
];

$total_updated = 0;
if (!empty($renamed_points)) {
    foreach ($renamed_points as $s_no => $new_text) {
        foreach ($tables as $table) {
            $stmt = $conn->prepare("UPDATE $table SET observation_text = ? WHERE S_no = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $new_text, $s_no);
                $stmt->execute();
                $total_updated += $stmt->affected_rows;
                $stmt->close();
            }
        }
    }
}
echo "Updated text for " . $total_updated . " observations.<br>";

// ==========================================
// 3. UPDATE REQUIREMENT TEXT
// ==========================================
$updated_requirements = [
    // "4.1.8" => "Functional testing shall be performed as per the PDU test procedure..."
];

$total_req_updated = 0;
if (!empty($updated_requirements)) {
    foreach ($updated_requirements as $s_no => $new_req) {
        foreach ($tables as $table) {
            $stmt = $conn->prepare("UPDATE $table SET requirement_text = ? WHERE S_no = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $new_req, $s_no);
                $stmt->execute();
                $total_req_updated += $stmt->affected_rows;
                $stmt->close();
            }
        }
    }
}
echo "Updated requirement text for " . $total_req_updated . " observations.<br>";

$conn->close();
echo "<br><b>Database Migration Completed Successfully!</b><br>";
?>
