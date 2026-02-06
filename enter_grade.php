<?php
include 'config.php';
session_start();

// ตรวจสอบ Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// --- ส่วนของครู: จัดการการบันทึกคะแนน ---
if ($role == 'teacher') {
    if (isset($_POST['submit_grade'])) {
        $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
        $sub_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
        $score = mysqli_real_escape_string($conn, $_POST['score']);

        // ตรวจสอบว่าเคยกรอกเกรดวิชานี้ให้นักเรียนคนนี้ไปหรือยัง (Update หรือ Insert)
        $check_sql = "SELECT id FROM grades WHERE student_id = '$student_id' AND subject_id = '$sub_id'";
        $check_res = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_res) > 0) {
            $sql = "UPDATE grades SET score = '$score' WHERE student_id = '$student_id' AND subject_id = '$sub_id'";
        } else {
            $sql = "INSERT INTO grades (student_id, subject_id, score) VALUES ('$student_id', '$sub_id', '$score')";
        }

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('บันทึกคะแนนเรียบร้อยแล้ว');</script>";
        }
    }

    // ดึงรายชื่อนักเรียนและวิชามาใส่ใน Dropdown
    $students_list = mysqli_query($conn, "SELECT s.id, u.name FROM students s JOIN users u ON s.user_id = u.id");
    $subjects_list = mysqli_query($conn, "SELECT * FROM subjects");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการเกรดและคะแนน</title>
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
            max-width: 900px;
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

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        h2 {
            font-size: 26px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            font-size: 15px;
            font-weight: 500;
            color: #5a67d8;
        }

        select,
        input[type="number"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0d9f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: white;
        }

        select:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%235a67d8' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        button[type="submit"] {
            flex: 1;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .link-button {
            flex: 1;
            padding: 14px 32px;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .link-button:hover {
            background: rgba(102, 126, 234, 0.2);
        }

        /* Table Styles */
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

        .status-pass {
            color: #38ef7d;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-pass::before {
            content: "✓";
            display: inline-block;
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 12px;
        }

        .status-fail {
            color: #f5576c;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-fail::before {
            content: "✕";
            display: inline-block;
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 12px;
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

        .score-badge {
            display: inline-block;
            padding: 6px 14px;
            background: linear-gradient(135deg, #a8b5ff 0%, #c8b5ff 100%);
            color: #5a67d8;
            border-radius: 20px;
            font-weight: 600;
        }

        .required {
            color: #f5576c;
            margin-left: 4px;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 20px 10px;
            }

            .container {
                padding: 25px;
            }

            h2 {
                font-size: 22px;
            }

            .button-group {
                flex-direction: column;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>

<div class="page-container">
    <a href="admin_menu.php" class="back-link">← กลับหน้าหลัก</a>

    <div class="container">
        <?php if ($role == 'teacher'): ?>
            <h2>👨‍🏫 สำหรับอาจารย์: กรอกคะแนนนักเรียน</h2>
            <form method="post">
                <div class="form-group">
                    <label>เลือกนักเรียน <span class="required">*</span></label>
                    <select name="student_id" required>
                        <option value="">-- รายชื่อนักเรียน --</option>
                        <?php while($row = mysqli_fetch_assoc($students_list)): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>เลือกรายวิชา <span class="required">*</span></label>
                    <select name="subject_id" required>
                        <option value="">-- รายชื่อวิชา --</option>
                        <?php while($row = mysqli_fetch_assoc($subjects_list)): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>คะแนน (0-100) <span class="required">*</span></label>
                    <input type="number" name="score" min="0" max="100" step="0.01" placeholder="กรอกคะแนน..." required>
                </div>

                <div class="button-group">
                    <button type="submit" name="submit_grade">✅ บันทึกคะแนน</button>
                    <a href="admin_menu.php" class="link-button">ยกเลิก</a>
                </div>
            </form>

        <?php else: ?>
            <h2>🎓 ผลการเรียนของคุณ: <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>รายวิชา</th>
                        <th>คะแนนที่ได้</th>
                        <th>การประเมิน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT s.name as subject_name, g.score 
                            FROM grades g 
                            JOIN subjects s ON g.subject_id = s.id 
                            WHERE g.student_id = (SELECT id FROM students WHERE user_id = '$user_id')";
                    $res = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)) {
                            $score = $row['score'];
                            // ตรรกะตัดเกรดเบื้องต้น
                            $grade = ($score >= 50) ? "<span class='status-pass'>ผ่าน</span>" : "<span class='status-fail'>ไม่ผ่าน</span>";
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['subject_name']) . "</td>
                                    <td><span class='score-badge'>{$score}</span></td>
                                    <td>{$grade}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='empty-state'>ยังไม่มีข้อมูลคะแนน</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>