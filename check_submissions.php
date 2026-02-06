<?php
include 'config.php';
session_start();

// 1. ตรวจสอบสิทธิ์ (Security Check)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    die("หน้านี้สำหรับครูเท่านั้น <a href='admin_menu.php'>กลับหน้าหลัก</a>");
}

// 2. คำสั่ง SQL ที่สำคัญ (ต้อง JOIN 4 ตารางเพื่อให้ได้ข้อมูลครบ)
// - submissions (การส่งงาน)
// - students/users (ข้อมูลนักเรียน)
// - assignments (โจทย์ที่ครูตั้งไว้)
$sql = "SELECT 
            s.id AS submission_id,
            u.name AS student_name,
            a.title AS task_title,
            a.type AS task_type,
            a.attachment_link AS question_file, -- ไฟล์โจทย์ที่ครูลง
            s.file_link AS student_file,        -- ไฟล์ที่นักเรียนส่ง
            s.submitted_at
        FROM submissions s
        JOIN students st ON s.student_id = st.id
        JOIN users u ON st.user_id = u.id
        JOIN assignments a ON s.assignment_id = a.id
        ORDER BY s.submitted_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบตรวจงานและข้อสอบ</title>
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
        }

        h2 {
            font-size: 24px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 12px;
        }

        .teacher-info {
            font-size: 14px;
            color: #764ba2;
        }

        .teacher-info strong {
            color: #5a67d8;
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

        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bg-exam {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 2px 8px rgba(245, 87, 108, 0.3);
        }

        .bg-homework {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            box-shadow: 0 2px 8px rgba(17, 153, 142, 0.3);
        }

        .btn {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-view {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-question {
            background: linear-gradient(135deg, #a8b5ff 0%, #c8b5ff 100%);
            color: #5a67d8;
            margin-right: 5px;
            box-shadow: 0 2px 8px rgba(168, 181, 255, 0.3);
        }

        .btn-question:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(168, 181, 255, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            font-size: 15px;
        }

        .empty-state::before {
            content: "📋";
            display: block;
            font-size: 48px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 20px 10px;
            }

            .container {
                padding: 20px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px 8px;
            }

            .btn {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>

<div class="page-container">
    <a href="admin_menu.php" class="back-link">← กลับหน้าหลัก</a>

    <div class="header">
        <h2>✅ ตรวจรายการส่งงานและข้อสอบ</h2>
        <p class="teacher-info">ครูผู้ตรวจ: <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></p>
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>วัน-เวลาที่ส่ง</th>
                    <th>ชื่อนักเรียน</th>
                    <th>หัวข้อ</th>
                    <th>ประเภท</th>
                    <th>ไฟล์โจทย์</th>
                    <th>ไฟล์งานนักเรียน</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['submitted_at'])); ?></td>
                        <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['task_title']); ?></td>
                        <td>
                            <span class="badge <?php echo ($row['task_type'] == 'exam') ? 'bg-exam' : 'bg-homework'; ?>">
                                <?php echo ($row['task_type'] == 'exam') ? 'ข้อสอบ' : 'การบ้าน'; ?>
                            </span>
                        </td>
                        <td>
                            <?php if($row['question_file']): ?>
                                <a href="<?php echo htmlspecialchars($row['question_file']); ?>" target="_blank" class="btn btn-question">📄 โจทย์</a>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo htmlspecialchars($row['student_file']); ?>" target="_blank" class="btn btn-view">🔍 เปิดตรวจงาน</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            ยังไม่มีข้อมูลการส่งงานในขณะนี้
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>