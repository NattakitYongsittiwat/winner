<?php
include 'config.php';
session_start();

if ($_SESSION['role'] != 'teacher') { die("หน้านี้สำหรับครูเท่านั้น"); }

if (isset($_POST['create'])) {
    $subject_id = $_POST['subject_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = $_POST['type']; // รับค่าประเภท
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = $_POST['due_date'];
    
    $attachment = "";
    // ส่วนของการอัปโหลดไฟล์โจทย์จากครู
    if (!empty($_FILES["attachment"]["name"])) {
        $target_dir = "uploads/questions/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $attachment = $target_dir . time() . "_" . basename($_FILES["attachment"]["name"]);
        move_uploaded_file($_FILES["attachment"]["tmp_name"], $attachment);
    }

    $sql = "INSERT INTO assignments (subject_id, title, type, description, attachment_link, due_date) 
            VALUES ('$subject_id', '$title', '$type', '$description', '$attachment', '$due_date')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('ประกาศสำเร็จ!'); window.location='admin_menu.php';</script>";
    }
}
$subjects = mysqli_query($conn, "SELECT * FROM subjects");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างโจทย์ข้อสอบ/การบ้าน</title>
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
            max-width: 800px;
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
        input[type="text"],
        input[type="date"],
        input[type="file"],
        textarea {
            padding: 12px 16px;
            border: 2px solid #e0d9f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: white;
        }

        select:focus,
        input[type="text"]:focus,
        input[type="date"]:focus,
        textarea:focus {
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

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="file"] {
            padding: 10px;
            cursor: pointer;
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

        .radio-group {
            display: flex;
            gap: 30px;
            padding: 10px 0;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .radio-option label {
            cursor: pointer;
            margin: 0;
            font-weight: 400;
        }

        button[type="submit"] {
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
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        .required {
            color: #f5576c;
            margin-left: 4px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }

            h2 {
                font-size: 22px;
            }

            .radio-group {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>

<div class="page-container">
    <a href="admin_menu.php" class="back-link">← กลับหน้าหลัก</a>

    <div class="container">
        <h2>📝 สร้างข้อสอบ / สั่งการบ้าน</h2>
        
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>วิชา <span class="required">*</span></label>
                <select name="subject_id" required>
                    <option value="">-- เลือกวิชา --</option>
                    <?php while($s = mysqli_fetch_assoc($subjects)): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>หัวข้อ <span class="required">*</span></label>
                <input type="text" name="title" placeholder="กรอกหัวข้องาน..." required>
            </div>

            <div class="form-group">
                <label>ประเภท <span class="required">*</span></label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" name="type" value="homework" id="homework" checked>
                        <label for="homework">📚 การบ้าน</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" name="type" value="exam" id="exam">
                        <label for="exam">📝 ข้อสอบ</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>รายละเอียด</label>
                <textarea name="description" placeholder="กรอกรายละเอียดเพิ่มเติม (ถ้ามี)..."></textarea>
            </div>

            <div class="form-group">
                <label>ไฟล์โจทย์ (ถ้ามี)</label>
                <input type="file" name="attachment">
            </div>

            <div class="form-group">
                <label>กำหนดส่ง <span class="required">*</span></label>
                <input type="date" name="due_date" required>
            </div>

            <button type="submit" name="create">✅ สร้างงาน</button>
        </form>
    </div>
</div>

</body>
</html>