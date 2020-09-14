<?php

    class Status {
        public $id;
        public $type;

        // get all status
        public static function get_all() {
            global $db;
    
            $raw = $db->run($db->prepare('SELECT id, type FROM status'), []);
            
            $allStatus = [];

            if ($raw) {
                foreach ($raw as $row) {
                    $status = new Status();

                    $status->id = $row["id"];
                    $status->type = $row["type"];

                    array_push($allStatus, $status);
                }
            }

            return $allStatus;
        }

        // get by id
        public static function get_by_id($id) {
            global $db;
    
            $raw = $db->run($db->prepare('SELECT id, type FROM status WHERE id = :value0'), [$id])[0];
            
            $status = new Status();

            if ($raw) {
                $status->id = $id;
                $status->type = $raw["type"];
            }

            return $status;
        }

        // get by type
        public static function get_by_type($type) {
            global $db;

            $allStatus = Status::get_all();
            $statusOfType = null;
            foreach ($allStatus as $status) {
                if ($status->type == $type) {
                    $statusOfType = $status;
                }
            }

            return $statusOfType;
        }
    }

?>