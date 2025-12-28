<?php
require_once 'config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CSE Study Resource</title>
    <style>
        /* Floating icons */
        .floating-icon {
            position: fixed;
            width: 30px;
            height: 30px;
            opacity: 0.5;
            animation: floatUp 6s linear infinite;
        }
        @keyframes floatUp {
            from { transform: translateY(100vh); }
            to { transform: translateY(-10vh); }
        }

        /* Loader */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f2f0ea;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease;
            z-index: 9999;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #dcd6f7;
            border-top-color: #4b3f72;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Smooth reveal */
        .card, .hero, .sections {
            opacity: 0;
            transform: translateY(40px);
            transition: 0.8s ease;
        }
        .show {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: #f2f0ea;
            color: #333;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        header {
            background: #dcd6f7;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            animation: slideDown 0.9s ease;
        }
        @keyframes slideDown {
            from { transform: translateY(-40px); opacity:0; }
            to { transform: translateY(0); opacity:1; }
        }
        header h1 {
            margin: 0;
            color: #4b3f72;
            font-size: 28px;
        }
        nav a {
            margin-left: 20px;
            text-decoration: none;
            color: #4b3f72;
            font-weight: 600;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        nav a:hover {
            color: #2f2652;
            transform: translateY(-3px);
        }
        .hero {
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(to bottom right, #e8e4f8, #f7f5ff);
            animation: fadeUp 1.2s ease;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hero h2 {
            font-size: 40px;
            color: #4b3f72;
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 18px;
            color: #6a5a87;
        }
        .sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); /* was 250px or wider */
            gap: 30px;             /* smaller gap */
            padding: 40px 60px;    /* less padding around */
        }

        .card {
            background: white;
            padding: 16px 18px;          /* smaller inside space */
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            text-align: center;
            transition: 0.4s ease;
            opacity: 0;
            animation: cardFade 0.8s forwards;
            height: 180px;               /* <<< add this */
            display: flex;               /* center content */
            flex-direction: column;
            justify-content: flex-start; /* or center */
        }


        @keyframes cardFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        footer {
            text-align: center;
            padding: 20px;
            background: #dcd6f7;
            margin-top: 40px;
            color: #4b3f72;
            animation: fadeIn 1s ease-in;
        }
        .primary-btn {
            margin-top: 20px;
            padding: 14px 32px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, #4b3f72, #7a5cf4);
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.3px;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(75,63,114,0.35);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
            animation: subtle-pulse 2.4s ease-in-out infinite;
        }

        .primary-btn::after {
            content: "→";
            font-size: 16px;
            transition: transform 0.18s ease;
        }

        .primary-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 14px 30px rgba(75,63,114,0.45);
            background: linear-gradient(135deg, #3c315d, #6647e0);
            animation-play-state: paused;
        }

        .primary-btn:hover::after {
            transform: translateX(3px);
        }

        @keyframes subtle-pulse {
            0%, 100% { transform: translateY(0) scale(1); }
            50%      { transform: translateY(-2px) scale(1.03); }
        }

        .how-it-works {
            padding: 40px 50px 20px;
            text-align: center;
        }
        .how-it-works h3 {
            color: #4b3f72;
            margin-bottom: 25px;
        }
        .steps {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .step {
            background: #ffffff;
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.06);
            width: 260px;
        }
        .step-number {
            display: inline-flex;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            background: #dcd6f7;
            color: #4b3f72;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .step h4 {
            margin: 4px 0 6px;
            color: #333;
        }
        .step p {
            font-size: 14px;
            color: #666;
        }
        .card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .card-link .card:hover {
            cursor: pointer;
        }
        .learning-stats {
            display: grid;
            grid-template-columns: minmax(260px, 1.2fr) minmax(280px, 1.4fr);
            gap: 32px;
            padding: 20px 60px 10px;
            align-items: center;
        }

        .learning-text h3 {
            margin: 12px 0 8px;
            color: #3b305f;
            font-size: 24px;
        }

        .learning-text p {
            margin: 0 0 14px;
            color: #555;
            line-height: 1.6;
            max-width: 420px;
        }

        .tagline {
            display: inline-block;
            background: #ffe3e3;
            color: #333;
            padding: 6px 14px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
        }

        .secondary-btn {
            padding: 10px 24px;
            border-radius: 999px;
            border: none;
            background: #2563eb;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(37,99,235,0.35);
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .secondary-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(37,99,235,0.45);
        }

        .learning-cards {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .ls-card {
            border-radius: 24px;
            padding: 18px 20px;
            background: #eef2ff;
            box-shadow: 0 12px 26px rgba(0,0,0,0.06);
        }

        .ls-card.big {
            min-height: 150px;
        }

        .ls-card-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0,1fr));
            gap: 14px;
        }

        .ls-card.small {
            min-height: 120px;
        }

        .ls-card.small.yellow {
            background: #fff5d9;
        }

        .ls-card.small.purple {
            background: #f3e8ff;
        }

        .ls-card h4 {
            margin: 0 0 4px;
            font-size: 28px;
            color: #111827;
        }

        .ls-sub {
            margin: 0 0 10px;
            font-size: 13px;
            color: #4b5563;
        }

        .ls-bar {
            width: 100%;
            height: 10px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .ls-bar-fill {
            width: 92%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #22c55e, #a3e635);
        }

        /* simple responsiveness */
        @media (max-width: 900px) {
            .learning-stats {
                grid-template-columns: 1fr;
                padding: 20px 24px 10px;
            }
        }
        .ls-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
        }

        .dog-icon {
            width: 70px;
            height: 70px;
            object-fit: contain;
            animation: dog-bounce 2.2s ease-in-out infinite;
            transform-origin: bottom center;
        }

        .dog-icon.small {
            width: 90px !important;
            height: 90px !important;
        }


        @keyframes dog-bounce {
            0%, 100%   { transform: translateY(0) scale(1); }
            25%        { transform: translateY(-4px) scale(1.02); }
            50%        { transform: translateY(0) scale(1); }
            75%        { transform: translateY(-2px) rotate(-2deg); }
        }



    </style>
</head>
<body>

    <?php include 'header.php'; ?>




    <section class="hero">
        <h2>Your All‑in‑One CSE Study Companion</h2>
        <p>Access curated resources, class notes and practice problems shared by CSE students and faculty.</p>
        <button class="primary-btn" onclick="window.location.href='signup.php'">
            Get Started – It’s Free
        </button>
    </section>


    <section class="sections">
        <a href="resource.php" class="card-link">
            <div class="card" id="resources">
                <h3>📚 Study Materials</h3>
                <p>Browse all uploaded course materials, slides, notes and code files.</p>
            </div>
        </a>

        <a href="resource.php/notes" class="card-link"> 
            <div class="card" id="notes">
                <h3>📝 Notes &amp; Cheat Sheets</h3>
                <p>Clean, easy-to-follow study guides and topic summaries.</p>
            </div>
        </a>
        <div class="card" id="practice">
            <h3>🧠 Practice Problems</h3>
            <p>Interactive quizzes and coding problems to test your skills.</p>
        </div>
        <a href="review.php" class ="card-link">
            <div class="card" id="review">
                <h3>🧐 Reviews </h3>
                <p>See reviews provided by your peers of your faculties and courses.</p>
            </div>
        </a>
        </section>          

        <section class="learning-stats">
            <div class="learning-text">
                <span class="tagline">Learning is hard</span>
                <h3>But it gets easier with the right resources.</h3>
                <p>
                    CSE_Study_Resource helps you stay organized, reduce revision time and
                    revise smarter for every CSE course.
                </p>
                <button class="secondary-btn" onclick="window.location.href='signup.php'">
                    Start for free
                </button>
            </div>

            <div class="learning-cards">
                <div class="ls-card big">
                    <div class="ls-card-header">
                        <h4>92%</h4>
                        <!-- main robot image -->
                        <img src="images/dog-study-2.png" alt="Study robot" class="dog-icon small">

                    </div>
                    <p class="ls-sub">
                        Students feel more confident before exams after using shared notes and past resources.
                    </p>
                    <div class="ls-bar">
                        <div class="ls-bar-fill"></div>
                    </div>
                </div>

                <div class="ls-card-row">
                    <div class="ls-card small yellow">
                        <div class="ls-card-header">
                            <h4>30%</h4>
                            <img src="images/dog-study-1.png" alt="Study robot" class="dog-icon small">
                        </div>
                        <p class="ls-sub">Average reduction in study time when content is well organized.</p>
                    </div>
                    <div class="ls-card small purple">
                        <div class="ls-card-header">
                            <h4>92%</h4>
                            <img src="images/dog-study.png" alt="Study robot" class="dog-icon small">
                        </div>
                        <p class="ls-sub">Success rate for students who regularly practice problems.</p>
                    </div>

                </div>
            </div>
        </section>



        <section class="how-it-works">
            <h3>How CSE_Study_Resource helps you</h3>
            <div class="steps">
                <div class="step">
                    <span class="step-number">1</span>
                    <h4>Sign up</h4>
                    <p>Create a free account with your university email.</p>
                </div>
                <div class="step">
                    <span class="step-number">2</span>
                    <h4>Find resources</h4>
                    <p>Filter by course code, topic and semester to get exactly what you need.</p>
                </div>
                <div class="step">
                    <span class="step-number">3</span>
                    <h4>Practice & share</h4>
                    <p>Download notes, solve problems and upload your own high‑quality materials.</p>
                </div>
            </div>
        </section>

    </section>


    <footer>
        © 2025 CSE_Study_Resource — Designed with care.
    </footer>

    <!-- Loading Screen -->
    <div id="loader">
        <div class="spinner"></div>
    </div>

    <script>
        // Hide loader when page loads
        window.addEventListener('load', () => {
            const loader = document.getElementById('loader');
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 500);
        });

        // Smooth transitions on scroll
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                }
            });
        });

        document.querySelectorAll('.card, .hero, .sections').forEach(el => {
            observer.observe(el);
        });
    </script>

</body>
</html>
