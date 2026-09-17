USE oarnote;

ALTER TABLE sessions
  ADD COLUMN status ENUM('upcoming','open','completed') NOT NULL DEFAULT 'upcoming' AFTER workout_format,
  ADD COLUMN purpose ENUM('UT2','UT1','AT','TR','Race/Test','Technique') NULL AFTER status,
  ADD COLUMN boat_class ENUM('8+','4+','4-','2-','2x','1x','Other') NULL AFTER purpose;

ALTER TABLE results
  ADD COLUMN attendance_status ENUM('completed','missed','unavailable') NOT NULL DEFAULT 'completed' AFTER effort_rating;

CREATE TABLE interval_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  result_id INT NOT NULL,
  segment_id INT NOT NULL,
  interval_number INT NOT NULL,
  result_time VARCHAR(20),
  split_500m VARCHAR(20),
  stroke_rate INT,
  FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
  FOREIGN KEY (segment_id) REFERENCES workout_segments(id) ON DELETE CASCADE,
  UNIQUE KEY unique_interval_result (result_id,segment_id,interval_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
