<!-- USER FAQ -->

<?php
require_once 'config.php';
require_once 'functions.php';

// Process feedback form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $message = sanitizeInput($_POST['message']);

    try {
        $stmt = $pdo->prepare("INSERT INTO faq_feedback (name, message) VALUES (?, ?)");
        $stmt->execute([$name, $message]);
        $_SESSION['feedback_message'] = "Thank you for your feedback!";
    } catch (PDOException $e) {
        $_SESSION['feedback_error'] = "Error submitting feedback: " . $e->getMessage();
    }
}

// Get all FAQ questions
try {
    $faqs = $pdo->query("
        SELECT fq.*, fc.name as category_name 
        FROM faq_questions fq
        LEFT JOIN faq_categories fc ON fq.category_id = fc.id
        ORDER BY fq.is_featured DESC, fc.name, fq.question
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $faqs = [];
    error_log("FAQ query error: " . $e->getMessage());
}

include 'templates/header_faq.php';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="styles/faq.css"> -->
    <style>
        /* FAQ Container */
        .faq-container {
            background-color:rgb(120, 35, 35);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 40px;
        }

        .faq-title {
            text-align: center;
            color: #1b3f1c;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .faq-category {
            color: #3a7d44;
            margin: 25px 0 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #bfe3b4;
        }

        .faq-item {
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .faq-question {
            background-color: #f8f9fa;
            padding: 15px 20px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        .faq-question:hover {
            background-color: #e9ecef;
        }

        .faq-toggle {
            font-size: 1.5rem;
            transition: transform 0.3s;
        }

        .faq-answer {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            background-color: white;
        }

        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 1000px;
        }

        .faq-item.active .faq-toggle {
            transform: rotate(45deg);
        }

        /* Feedback Form */
        .feedback-container {
            background-color:rgb(180, 50, 117);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .w3-input {
            padding: 12px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {

            .faq-container,
            .feedback-container {
                padding: 20px;
            }
        }

        .faq-title {
            font-size: 1.5rem;
        }

        .feedback-container {
            padding: 20px;
        }

        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: "Raleway", sans-serif;
        }

        .w3-display-left {
            padding-top: 120px;
            /* Give space below header */
        }

        /* Rest of your existing styles... */
        .w3-bar .w3-button {
            padding: 16px;
        }

        .card {
            background-color: #ffffff;
            padding: 50px;
            margin-top: 20px;
        }

        /* Create two unequal columns that floats next to each other */
        .leftcolumn {
            float: left;
            width: 65%;
        }

        .rightcolumn {
            float: left;
            width: 33%;
            padding-left: 20px;
        }

        .logo {
            width: 26px;
            height: 26px;
            margin-left: 10px;
        }

        .w3-card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .w3-container {
            background-color: #c1e1c1;
            /* pastel green */
            padding: 60px 20px;
        }
    </style>
</head>

<body>
    <div style="padding-top: 120px; background-color: #c1e1c1" class="w3-container w3-row-padding">
        <div class="w3-col">
            <div class="faq-container">
                <h2 class="faq-title">Frequently Asked Questions</h2>

                <?php if (!empty($faqs)): ?>
                    <?php
                    $current_category = null;
                    foreach ($faqs as $faq):
                        if ($current_category !== $faq['category_name']):
                            $current_category = $faq['category_name'];
                            ?>
                            <h3 class="faq-category"><?php echo htmlspecialchars($current_category); ?></h3>
                        <?php endif; ?>

                        <div class="faq-item">
                            <div class="faq-question">
                                <?php echo htmlspecialchars($faq['question']); ?>
                                <span class="faq-toggle">+</span>
                            </div>
                            <div class="faq-answer">
                                <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="w3-center">No FAQs found. Please check back later.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="">
            <div class="feedback-container" id="contact">
                <h3 class="w3-center">Send us your feedback!</h3>

                <?php if (isset($_SESSION['feedback_message'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['feedback_message'];
                        unset($_SESSION['feedback_message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['feedback_error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['feedback_error'];
                        unset($_SESSION['feedback_error']); ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                    <div class="form-group">
                        <input class="w3-input w3-border" type="text" placeholder="Name" required name="name">
                    </div>
                    <div class="form-group">
                        <textarea class="w3-input w3-border" placeholder="Message" required name="message"
                            rows="4"></textarea>
                    </div>
                    <button class="w3-button w3-black" type="submit">
                        <i class="fa fa-paper-plane"></i> SEND MESSAGE
                    </button>
                </form>
            </div>
            <br>
            <br>
        </div>
    </div>
</body>

</html>

<script>
    // FAQ Toggle Functionality
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            faqItem.classList.toggle('active');

            // Close other open FAQs
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem && item.classList.contains('active')) {
                    item.classList.remove('active');
                }
            });
        });
    });

    // Form submission feedback
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> SENDING...';
                submitBtn.disabled = true;
            }
        });
    }
</script>

<?php include 'templates/footer.php'; ?>