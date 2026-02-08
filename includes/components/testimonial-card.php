<?php

// Sample Testimonial Card

// Variables expected: $testimonial['name'], $testimonial['message'], $testimonial['avatar']

?>



<div class="testimonial-card">

    <div class="avatar">

        <img src="<?= $testimonial['avatar']; ?>" alt="<?= $testimonial['name']; ?>">

    </div>

    <div class="testimonial-content">

        <p>"<?= $testimonial['message']; ?>"</p>

        <h4><?= $testimonial['name']; ?></h4>

    </div>

</div>

