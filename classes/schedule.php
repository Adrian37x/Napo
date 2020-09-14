<?php

    class Schedule {
        public $id;
        public $weekdayId;
        public $start;
        public $end;
        public $userId;

        // get schedules by user id
        public static function get_by_userId($userId) {
            global $db;
    
            $raw = $db->run($db->prepare('SELECT id, weekdayId, start, end FROM schedule WHERE userId = :value0 ORDER BY weekdayId, start'), [$userId]);

            $schedules = [];

            if ($raw) {
                foreach ($raw as $row){
                    $schedule = new Schedule();
    
                    $schedule->id = $row["id"];
                    $schedule->weekdayId = $row["weekdayId"];
                    $schedule->start = $row["start"];
                    $schedule->end = $row["end"];
                    $schedule->userId = $userId;
    
                    array_push($schedules, $schedule);
                }
            }

            return $schedules;
        }

        // add schedule
        public static function add($userId, $weekdayId, $start, $end) {
            global $db;

            $addSchedule = $db->prepare('INSERT INTO schedule (weekdayId, start, end, userId) VALUES (:value0, :value1, :value2, :value3)');

            // bring the times in the correct format (didn't work with strtotime())
            $start = $start . ":00";
            $end = $end . ":00";

            $schedules = Schedule::get_by_userId($userId);

            foreach ($schedules as $schedule) {
                // if schedule overlaps with new one
                if ($schedule->weekdayId == $weekdayId && (strtotime($schedule->end) >= strtotime($start) && strtotime($schedule->start) <= strtotime($end))) {
                    return false;
                }
            }

            $db->run($addSchedule, [$weekdayId, $start, $end, $userId]);

            return true;
        }

        // delete schedule
        public static function delete($id) {
            global $db;

            $db->run($db->prepare('DELETE FROM schedule WHERE id = :value0'), [$id]);
        }

        // checks if start is earlier than the end
        public static function check_timediff($start, $end) {
            return strtotime($start) < strtotime($end);
        }
    }

?>