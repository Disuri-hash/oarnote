USE oarnote;

ALTER TABLE sessions
  ADD COLUMN workout_format ENUM('single_distance','single_time','distance_intervals','time_intervals','variable') NULL AFTER session_type;

CREATE TABLE workout_segments (
  id INT AUTO_INCREMENT PRIMARY KEY, session_id INT NOT NULL, segment_order INT NOT NULL DEFAULT 1,
  segment_type ENUM('single_distance','single_time','distance_interval','time_interval','variable_distance','variable_time','water_shed') NOT NULL,
  title VARCHAR(255), repetitions INT, work_value VARCHAR(100), work_unit ENUM('metres','seconds','minutes','strokes'),
  recovery_value VARCHAR(100), recovery_unit ENUM('metres','seconds','minutes','strokes'), target_rate VARCHAR(50), target_split VARCHAR(50), notes TEXT,
  FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE, INDEX idx_segment_session_order (session_id,segment_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE segment_notes (
  id INT AUTO_INCREMENT PRIMARY KEY, result_id INT NOT NULL, segment_id INT NOT NULL, rower_note TEXT NOT NULL,
  FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
  FOREIGN KEY (segment_id) REFERENCES workout_segments(id) ON DELETE CASCADE,
  UNIQUE KEY unique_segment_note (result_id,segment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE feedback ADD UNIQUE KEY unique_feedback_result (result_id);
