<?php
function render_portswigger_header($lab_title, $is_solved = false) {
?>
<div class="ps-academy-bar">
    <div class="ps-logo">
        <span class="ps-badge">PortSwigger</span>
        <span class="ps-title">WEB SECURITY ACADEMY</span>
    </div>
    <div class="ps-lab-info">
        <span class="ps-lab-label">LAB</span>
        <span class="ps-lab-name"><?php echo htmlspecialchars($lab_title); ?></span>
    </div>
    <div class="ps-status">
        <?php if ($is_solved): ?>
            <span class="ps-status-pill ps-solved">SOLVED</span>
        <?php else: ?>
            <span class="ps-status-pill ps-unsolved">UNSOLVED</span>
        <?php endif; ?>
    </div>
</div>

<?php if ($is_solved): ?>
<div class="ps-congrats-banner">
    🎉 <strong>Congratulations, you solved the lab!</strong>
</div>
<?php endif; ?>
<?php
}
?>
