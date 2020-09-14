<?php

    class Appointment {
        public $id;
        public $date;
        public $start;
        public $end;
        public $subject;
        public $teacherId;
        public $studentId;
        public $statusId;

        // get appointments by user id which aren't outdated
        public static function get_all_by_userId($userId) {
            global $db;

            $getAll = $db->prepare('SELECT id, date, start, end, subject, teacherId, studentId, statusId FROM appointment WHERE teacherId = :value0 OR studentId = :value1 ORDER BY date, start');
    
            $raw = $db->run($getAll, [$userId, $userId]);

            $appointments = [];

            if ($raw) {
                foreach ($raw as $row){
                    $appointment = new Appointment();
    
                    $appointment->id = $row["id"];
                    $appointment->date = $row["date"];
                    $appointment->start = $row["start"];
                    $appointment->end = $row["end"];
                    $appointment->subject = $row["subject"];
                    $appointment->teacherId = $row["teacherId"];
                    $appointment->studentId = $row["studentId"];
                    $appointment->statusId = $row["statusId"];
    
                    if ($appointment->date >= date('Y-m-d')) {
                        array_push($appointments, $appointment);
                    }
                }
            }

            return $appointments;
        }

        // add appointment
        public static function add($date, $start, $end, $subject, $teacherId, $studentId) {
            global $db;

            $addAppointment = $db->prepare('INSERT INTO appointment (date, start, end, subject, teacherId, studentId, statusId) VALUES (:value0, :value1, :value2, :value3, :value4, :value5, :value6)');

            // bring the times in the correct format (didn't work with strtotime())
            $start = $start . ":00";
            $end = $end . ":00";

            $openStatus = Status::get_by_type("Open");
            if ($openStatus) {
                $db->run($addAppointment, [$date, $start, $end, $subject, $teacherId, $studentId, $openStatus->id]);
            }

            return true;
        }

        // accept an appointment
        public static function accept($id) {
            global $db;

            // get accept status
            $acceptStatus = Status::get_by_type("Accepted");

            if (!$acceptStatus) {
                return false;
            }

            $accept = $db->prepare('UPDATE appointment SET statusId = :value0 WHERE id = :value1');

            $db->run($accept, [$acceptStatus->id, $id]);

            return true;
        }

        // decline an appointment
        public static function decline($id) {
            global $db;

            // get decline status
            $declineStatus = Status::get_by_type("Declined");

            if (!$declineStatus) {
                return false;
            }
                
            $decline = $db->prepare('UPDATE appointment SET statusId = :value0 WHERE id = :value1');

            $db->run($decline, [$declineStatus->id, $id]);

            return true;
        }

        // checks if a timespan overlaps any schedule
        public static function check_overlap_schedule($userId, $weekday, $start, $end) {
            $schedules = Schedule::get_by_userId($userId);

            foreach ($schedules as $schedule) {
                // if schedule overlaps with timespan
                if (Weekday::get_by_name($weekday)->id == $schedule->weekdayId && (strtotime($schedule->start) <= strtotime($start) && strtotime($schedule->end) >= strtotime($end))) {
                    return true;
                }
            }

            // is not in any schedule timespan
            return false;
        }
    }

?>