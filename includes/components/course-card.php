<?php



// Sample Course Card Component

// Variables expected:

// $course['title'], $course['image'], $course['description'], $course['teacher'], $course['link']

?>

<div class="course-card">

    <div class="course-image">

        <img src="<?= $course['image']; ?>" alt="<?= $course['title']; ?>">

        <?php if(isset($course['badge'])): ?><span class="badge"><?= $course['badge']; ?></span><?php endif; ?>

    </div>

    <div class="course-content">

        <h3><?= $course['title']; ?></h3>

        <p><?= $course['description']; ?></p>

        <p class="teacher">المعلم: <?= $course['teacher']; ?></p>

        <a href="<?= $course['link']; ?>" class="btn-primary">ابدأ الدورة</a>

    </div>

</div>

