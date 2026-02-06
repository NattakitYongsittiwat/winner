<?php
include 'config.php';
session_start();
if ($_SESSION['role'] != 'student') { die("หน้านี้สำหรับนักเรียนเท่านั้น"); }

// (ส่วนประมวลผลการส่งงานคงเดิมจากที่คุณมี แต่เปลี่ยน $target_dir เป็น uploads/submissions/ เพื่อความระเบียบ)

$query = "SELECT a.*, s.name as subject_name FROM assignments a JOIN subjects s ON a.subject_id = s.id ORDER BY a.due_date ASC";
$assignments = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่งงานและข้อสอบ</title>
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
            font-size: 26px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 10px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            margin-bottom: 30px;
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

        .bg-work {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            box-shadow: 0 2px 8px rgba(17, 153, 142, 0.3);
        }

        .subject-title {
            font-weight: 600;
            color: #764ba2;
            margin-bottom: 4px;
        }

        .assignment-title {
            font-size: 13px;
            color: #999;
        }

        .download-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: linear-gradient(135deg, #a8b5ff 0%, #c8b5ff 100%);
            color: #5a67d8;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .download-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(168, 181, 255, 0.4);
        }

        .select-btn {
            padding: 8px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .select-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .select-btn.selected {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        /* Upload Form */
        .upload-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        h3 {
            font-size: 20px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 25px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 14px;
            font-weight: 500;
            color: #5a67d8;
        }

        input[type="text"] {
            padding: 12px 16px;
            border: 2px solid #e0d9f0;
            border-radius: 8px;
            font-size: 14px;
            background: #f8f9fa;
            color: #5a67d8;
            font-weight: 500;
        }

        input[type="file"] {
            padding: 12px;
            border: 2px dashed #e0d9f0;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }

        input[type="file"]:hover {
            border-color: #667eea;
            background: #f8f9fa;
        }

        input[type="file"]::file-selector-button {
            padding: 8px 16px;
            background: linear-gradient(135deg, #a8b5ff 0%, #c8b5ff 100%);
            color: #5a67d8;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            margin-right: 12px;
            transition: all 0.3s ease;
        }

        input[type="file"]::file-selector-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(168, 181, 255, 0.4);
        }

        button[type="submit"] {
            padding: 14px 32px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
        }

        button[type="submit"]:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .info-box {
            background: linear-gradient(90deg, #f0ebf8 0%, rgba(240, 235, 248, 0.5) 100%);
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            font-size: 13px;
            color: #5a67d8;
        }

        .required {
            color: #f5576c;
            margin-left: 4px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            font-size: 15px;
        }

        .empty-state::before {
            content: "📝";
            display: block;
            font-size: 48px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .page-container {
                padding: 20px 10px;
            }

            .container,
            .upload-section {
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
        }
    </style>
</head>
<body>

<div class="page-container">
    <a href="admin_menu.php" class="back-link">← กลับหน้าหลัก</a>

    <div class="header">
        <h2>📝 รายการงานที่ได้รับมอบหมาย</h2>
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>ประเภท</th>
                    <th>วิชา (หัวข้อ)</th>
                    <th>ไฟล์โจทย์</th>
                    <th>กำหนดส่ง</th>
                    <th>เลือกส่งงาน</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($assignments) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($assignments)): ?>
                    <tr>
                        <td>
                            <span class="badge <?php echo ($row['type'] == 'exam') ? 'bg-exam' : 'bg-work'; ?>">
                                <?php echo ($row['type'] == 'exam') ? 'ข้อสอบ' : 'การบ้าน'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="subject-title"><?php echo htmlspecialchars($row['subject_name']); ?></div>
                            <div class="assignment-title"><?php echo htmlspecialchars($row['title']); ?></div>
                        </td>
                        <td>
                            <?php if($row['attachment_link']): ?>
                                <a href="<?php echo htmlspecialchars($row['attachment_link']); ?>" target="_blank" class="download-link">
                                    <span>📄</span>
                                    <span>ดาวน์โหลดโจทย์</span>
                                </a>
                            <?php else: ?> 
                                <span style="color: #ccc;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($row['due_date'])); ?></td>
                        <td>
                            <button class="select-btn" onclick="selectAssignment('<?php echo $row['id']; ?>', this)">
                                เลือกงานนี้
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-state">
                            ยังไม่มีงานที่ได้รับมอบหมาย
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="upload-section">
        <h3>📤 ฟอร์มส่งไฟล์งาน</h3>
        
        <div class="info-box">
            💡 กรุณาเลือกงานจากตารางด้านบนก่อนอัปโหลดไฟล์
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>ID งานที่เลือก <span class="required">*</span></label>
                <input type="text" id="as_id" name="assignment_id" readonly required placeholder="กรุณาเลือกงานจากตารางด้านบน">
            </div>

            <div class="form-group">
                <label>เลือกไฟล์คำตอบ <span class="required">*</span></label>
                <input type="file" name="fileToUpload" required accept=".pdf,.doc,.docx,.zip,.rar">
            </div>

            <button type="submit" name="upload">✅ ส่งงาน</button>
        </form>
    </div>
</div>

<script>
function selectAssignment(assignmentId, button) {
    // อัพเดทค่า input
    document.getElementById('as_id').value = assignmentId;
    
    // ลบ class selected จากปุ่มอื่นๆ
    document.querySelectorAll('.select-btn').forEach(btn => {
        btn.classList.remove('selected');
        btn.textContent = 'เลือกงานนี้';
    });
    
    // เพิ่ม class selected ให้ปุ่มที่กด
    button.classList.add('selected');
    button.textContent = '✓ เลือกแล้ว';
    
    // Scroll ลงไปที่ form
    document.querySelector('.upload-section').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}
</script>

</body>
</html>
</html>