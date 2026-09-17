CREATE DATABASE IF NOT EXISTS oarnote CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE oarnote;

-- Users table (for authentication - Stage 2)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  role ENUM('coach', 'rower') NOT NULL DEFAULT 'rower',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sessions table (for training sessions)
CREATE TABLE IF NOT EXISTS sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  coach_id INT NOT NULL,
  date DATE NOT NULL,
  session_type ENUM('Erg', 'Water') NOT NULL DEFAULT 'Water',
  workout_format ENUM('single_distance', 'single_time', 'distance_intervals', 'time_intervals', 'variable') NULL,
  status ENUM('upcoming','open','completed') NOT NULL DEFAULT 'upcoming',
  purpose ENUM('UT2','UT1','AT','TR','Race/Test','Technique') NULL,
  boat_class ENUM('8+','4+','4-','2-','2x','1x','Other') NULL,
  title VARCHAR(255),
  description TEXT,
  distance INT,
  boat VARCHAR(100),
  location VARCHAR(255),
  coach_notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (coach_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_date (date),
  INDEX idx_coach_id (coach_id),
  INDEX idx_session_type (session_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS workout_segments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT NOT NULL,
  segment_order INT NOT NULL DEFAULT 1,
  segment_type ENUM('single_distance','single_time','distance_interval','time_interval','variable_distance','variable_time','water_shed') NOT NULL,
  title VARCHAR(255),
  repetitions INT,
  work_value VARCHAR(100),
  work_unit ENUM('metres','seconds','minutes','strokes'),
  recovery_value VARCHAR(100),
  recovery_unit ENUM('metres','seconds','minutes','strokes'),
  target_rate VARCHAR(50),
  target_split VARCHAR(50),
  notes TEXT,
  FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
  INDEX idx_segment_session_order (session_id, segment_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Results table (for rower session submissions)
CREATE TABLE IF NOT EXISTS results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT NOT NULL,
  rower_id INT NOT NULL,
  session_type ENUM('Erg', 'Water') NOT NULL,
  -- Erg-specific fields
  distance INT,
  total_time_seconds INT,
  split_500m VARCHAR(50),
  stroke_rate INT,
  heart_rate INT,
  -- Common fields
  effort_rating INT CHECK (effort_rating >= 1 AND effort_rating <= 10),
  attendance_status ENUM('completed','missed','unavailable') NOT NULL DEFAULT 'completed',
  personal_notes TEXT,
  -- Water-specific fields
  technical_issues TEXT,
  pain_injury_notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE,
  FOREIGN KEY (rower_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_result (session_id, rower_id),
  INDEX idx_rower_id (rower_id),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS segment_notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  result_id INT NOT NULL,
  segment_id INT NOT NULL,
  rower_note TEXT NOT NULL,
  FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
  FOREIGN KEY (segment_id) REFERENCES workout_segments(id) ON DELETE CASCADE,
  UNIQUE KEY unique_segment_note (result_id, segment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS interval_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  result_id INT NOT NULL,
  segment_id INT NOT NULL,
  interval_number INT NOT NULL,
  result_time VARCHAR(20),
  split_500m VARCHAR(20),
  stroke_rate INT,
  FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
  FOREIGN KEY (segment_id) REFERENCES workout_segments(id) ON DELETE CASCADE,
  UNIQUE KEY unique_interval_result (result_id, segment_id, interval_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Feedback table (for coach feedback on results)
CREATE TABLE IF NOT EXISTS feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  result_id INT NOT NULL,
  coach_id INT NOT NULL,
  feedback_text TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (result_id) REFERENCES results(id) ON DELETE CASCADE,
  FOREIGN KEY (coach_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_feedback_result (result_id),
  INDEX idx_coach_id (coach_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
