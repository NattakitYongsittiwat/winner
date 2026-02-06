<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

// --- ส่วนของการ Export เป็นไฟล์ CSV ---
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=report_grades.csv');
    $output = fopen('php://output', 'w');
    // --- บรรทัดที่ต้องเพิ่มเพื่อแก้ภาษาต่างดาวใน Excel ---
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); 
    // ---------------------------------------------
    
    fputcsv($output, array('Student Name', 'Subject', 'Score')); // หัวตารางใน Excel

    $sql = "SELECT u.name as s_name, sub.name as sub_name, g.score 
            FROM grades g
            JOIN students s ON g.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN subjects sub ON g.subject_id = sub.id";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// --- ส่วนการแสดงผลหน้าเว็บ ---
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานสรุปผลการเรียน</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        h2 {
            font-size: 26px;
            font-weight: 600;
            color: #667eea;
            margin: 0;
        }

        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
        }

        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #f0ebf8;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th:first-child {
            border-top-left-radius: 8px;
        }

        th:last-child {
            border-top-right-radius: 8px;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: linear-gradient(90deg, #f0ebf8 0%, rgba(240, 235, 248, 0.3) 100%);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        td {
            color: #5a67d8;
            font-size: 14px;
        }

        .score-cell {
            font-weight: 600;
        }

        .score-badge {
            display: inline-block;
            padding: 6px 14px;
            background: linear-gradient(135deg, #a8b5ff 0%, #c8b5ff 100%);
            color: #5a67d8;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .student-name {
            font-weight: 500;
            color: #764ba2;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            font-size: 15px;
        }

        .empty-state::before {
            content: "📊";
            display: block;
            font-size: 48px;
            margin-bottom: 15px;
        }

        .stats-summary {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 200px;
            padding: 20px;
            background: linear-gradient(135deg, #f0ebf8 0%, rgba(240, 235, 248, 0.5) 100%);
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .stat-label {
            font-size: 13px;
            color: #764ba2;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #667eea;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 20px 10px;
            }

            .header {
                flex-direction: column;
                align-items: stretch;
            }

            .container {
                padding: 20px;
            }

            h2 {
                font-size: 22px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px 8px;
            }

            .btn-export {
                width: 100%;
                justify-content: center;
            }

            .stats-summary {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="page-container">
    <a href="admin_menu.php" class="back-link">← กลับหน้าหลัก</a>

    <div class="header">
        <h2>📊 รายงานสรุปผลการเรียนทั้งหมด</h2>
        <a href="?export=csv" class="btn-export">
            <span>📥</span>
            <span>ดาวน์โหลดเป็น Excel (CSV)</span>
        </a>
    </div>

    <div class="container">
        <?php
        // นับสถิติ
        $sql_count = "SELECT COUNT(DISTINCT s.id) as total_students, 
                             COUNT(DISTINCT sub.id) as total_subjects,
                             AVG(g.score) as avg_score
                      FROM grades g
                      JOIN students s ON g.student_id = s.id
                      JOIN subjects sub ON g.subject_id = sub.id";
        $stats_result = mysqli_query($conn, $sql_count);
        $stats = mysqli_fetch_assoc($stats_result);
        ?>

        <div class="stats-summary">
            <div class="stat-card">
                <div class="stat-label">จำนวนนักเรียน</div>
                <div class="stat-value"><?php echo $stats['total_students'] ?? 0; ?> คน</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">จำนวนรายวิชา</div>
                <div class="stat-value"><?php echo $stats['total_subjects'] ?? 0; ?> วิชา</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">คะแนนเฉลี่ย</div>
                <div class="stat-value"><?php echo isset($stats['avg_score']) ? number_format($stats['avg_score'], 2) : '0.00'; ?></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ชื่อนักเรียน</th>
                    <th>รายวิชา</th>
                    <th>คะแนน</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT u.name as s_name, sub.name as sub_name, g.score 
                        FROM grades g
                        JOIN students s ON g.student_id = s.id
                        JOIN users u ON s.user_id = u.id
                        JOIN subjects sub ON g.subject_id = sub.id
                        ORDER BY u.name, sub.name";
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td class='student-name'>" . htmlspecialchars($row['s_name']) . "</td>
                                <td>" . htmlspecialchars($row['sub_name']) . "</td>
                                <td class='score-cell'><span class='score-badge'>{$row['score']}</span></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='empty-state'>ยังไม่มีข้อมูลการประเมินผล</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>