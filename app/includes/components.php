<?php
// Reusable UI components

function button($text, $href, $type = 'primary', $class = '') {
    $btn_class = 'btn btn-' . htmlspecialchars($type);
    if ($class) {
        $btn_class .= ' ' . htmlspecialchars($class);
    }
    echo '<a href="' . htmlspecialchars($href) . '" class="' . $btn_class . '">' . htmlspecialchars($text) . '</a>';
}

function button_submit($text = 'Submit', $type = 'primary', $class = '') {
    $btn_class = 'btn btn-' . htmlspecialchars($type);
    if ($class) {
        $btn_class .= ' ' . htmlspecialchars($class);
    }
    echo '<button type="submit" class="' . $btn_class . '">' . htmlspecialchars($text) . '</button>';
}

function card($title, $content, $footer = '') {
    echo '<div class="card">';
    if ($title) {
        echo '<div class="card-header">' . htmlspecialchars($title) . '</div>';
    }
    echo '<div class="card-body">' . $content . '</div>';
    if ($footer) {
        echo '<div class="card-footer">' . $footer . '</div>';
    }
    echo '</div>';
}

function form_group($label, $input_html, $help_text = '') {
    echo '<div class="form-group">';
    if ($label) {
        echo '<label class="form-label">' . htmlspecialchars($label) . '</label>';
    }
    echo $input_html;
    if ($help_text) {
        echo '<small class="form-help">' . htmlspecialchars($help_text) . '</small>';
    }
    echo '</div>';
}

function table_head($columns) {
    echo '<table class="table"><thead><tr>';
    foreach ($columns as $col) {
        echo '<th>' . htmlspecialchars($col) . '</th>';
    }
    echo '</tr></thead><tbody>';
}

function table_row($cells) {
    echo '<tr>';
    foreach ($cells as $cell) {
        echo '<td>' . $cell . '</td>';
    }
    echo '</tr>';
}

function table_end() {
    echo '</tbody></table>';
}

function stat_card($label, $value, $color = 'primary') {
    echo '<div class="stat-card stat-' . htmlspecialchars($color) . '">';
    echo '<div class="stat-value">' . htmlspecialchars($value) . '</div>';
    echo '<div class="stat-label">' . htmlspecialchars($label) . '</div>';
    echo '</div>';
}
