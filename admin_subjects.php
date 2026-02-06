<?php
include 'config.php';
session_start();
$role = $_SESSION['role'];

// ถ้าเป็นครูและมีการกดปุ่มสร้าง
if ($role == 'teacher' && isset($_POST['add_subject'])) {
    $name = $_POST['subject_name'];
    mysqli_query($conn, "INSERT INTO subjects (name) VALUES ('$name')");
    echo "<div class='success-message'>✅ สร้างหลักสูตรสำเร็จ!</div>";
}

// ดึงข้อมูลวิชามาโชว์ (ดูได้ทุกคน)
$result = mysqli_query($conn, "SELECT * FROM subjects");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายวิชา/หลักสูตร</title>
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

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
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
            margin-bottom: 20px;
        }

        h3 {
            font-size: 18px;
            font-weight: 600;
            color: #764ba2;
            margin-bottom: 20px;
        }

        .success-message {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }

        .subject-list {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        ul {
            list-style: none;
        }

        ul li {
            padding: 16px 20px;
            background: linear-gradient(90deg, #f0ebf8 0%, rgba(240, 235, 248, 0.3) 100%);
            margin-bottom: 10px;
            border-radius: 8px;
            color: #5a67d8;
            font-size: 15px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }

        ul li:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }

        .form-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        form label {
            font-size: 15px;
            color: #5a67d8;
            font-weight: 500;
        }

        input[type="text"] {
            flex: 1;
            min-width: 250px;
            padding: 12px 16px;
            border: 2px solid #e0d9f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button[type="submit"] {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
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

        @media (max-width: 768px) {
            form {
                flex-direction: column;
                align-items: stretch;
            }

            input[type="text"] {
                min-width: 100%;
            }

            button[type="submit"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_menu.php" class="back-link">← กลับหน้าหลัก</a>
        
        <div class="subject-list">
            <h2>📚 รายวิชาทั้งหมด</h2>
            <ul>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <li><?php echo htmlspecialchars($row['name']); ?></li>
                <?php endwhile; ?>
            </ul>
        </div>

        <?php if ($role == 'teacher'): ?>
            <div class="form-section">
                <h3>✏️ สร้างหลักสูตรใหม่ (เฉพาะครู)</h3>
                <form method="post">
                    <label>ชื่อวิชา:</label>
                    <input type="text" name="subject_name" required placeholder="กรอกชื่อวิชา...">
                    <button type="submit" name="add_subject">บันทึก</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>