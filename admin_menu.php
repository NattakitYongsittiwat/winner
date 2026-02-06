<?php
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); }
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Menu</title>
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
            color: #2c3e50;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 800px;
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

        h1 {
            font-size: 26px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 8px;
        }

        .role {
            display: inline-block;
            font-size: 13px;
            color: #764ba2;
            font-weight: 500;
            background: #f0ebf8;
            padding: 4px 12px;
            border-radius: 12px;
        }

        .menu {
            list-style: none;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .menu li {
            border-bottom: 1px solid #f0ebf8;
        }

        .menu li:last-child {
            border-bottom: none;
        }

        .menu a {
            display: block;
            padding: 18px 24px;
            color: #5a67d8;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 15px;
            position: relative;
            overflow: hidden;
        }

        .menu a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .menu a:hover {
            background: linear-gradient(90deg, #f0ebf8 0%, rgba(240, 235, 248, 0.3) 100%);
            padding-left: 32px;
            color: #764ba2;
        }

        .menu a:hover::before {
            transform: scaleY(1);
        }

        .logout {
            margin-top: 30px;
            text-align: center;
        }

        .logout a {
            display: inline-block;
            padding: 14px 40px;
            color: white;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            text-decoration: none;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
        }

        .logout a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
        }

        /* ตกแต่งเพิ่มเติม */
        .menu li:nth-child(odd) a {
            background: rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 10px;
            }
            
            h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
            <div class="role"><?php echo $role == 'teacher' ? 'ครู' : 'นักเรียน'; ?></div>
        </div>

        <ul class="menu">
            <li><a href="admin_subjects.php">📚 รายวิชา/หลักสูตร</a></li>
            
            <?php if ($role == 'teacher'): ?>
                <li><a href="enter_grade.php">✏️ บันทึกเกรดและประเมินผล</a></li>
                <li><a href="create_assignment.php">📝 ออกข้อสอบ/สั่งการบ้าน</a></li>
                <li><a href="check_submissions.php">✅ ตรวจงาน/เช็คการส่งงานของนักเรียน</a></li>
            <?php else: ?>
                <li><a href="enter_grade.php">📊 ดูผลการเรียน</a></li>
                <li><a href="submit_work.php">📄 ดูข้อสอบและส่งการบ้าน</a></li>
            <?php endif; ?>
            
            <li><a href="export_report.php">📈 Report / Export ข้อมูล</a></li>
        </ul>

        <div class="logout">
            <a href="logout.php">ออกจากระบบ</a>
        </div>
    </div>
</body>
</html>