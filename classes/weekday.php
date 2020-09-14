<?php

    class Weekday {
        public $id;
        public $name;
        public $abbreviation;

        // gets all weekdays
        public static function get_all() {
            global $db;
    
            $raw = $db->run($db->prepare('SELECT id, name, abbreviation FROM weekday'), []);
            
            $weekdays = [];

            if ($raw) {
                foreach ($raw as $row) {
                    $weekday = new Weekday();

                    $weekday->id = $row["id"];
                    $weekday->name = $row["name"];
                    $weekday->abbreviation = $row["abbreviation"];

                    array_push($weekdays, $weekday);
                }
            }

            return $weekdays;
        }

        // get weekday by id
        public static function get_by_id($id) {
            global $db;
    
            $raw = $db->run($db->prepare('SELECT id, name, abbreviation FROM weekday WHERE id = :value0'), [$id])[0];
            
            $weekday = new Weekday();

            if ($raw) {
                $weekday->id = $id;
                $weekday->name = $raw["name"];
                $weekday->abbreviation = $raw["abbreviation"];
            }

            return $weekday;
        }

        // get weekday by name
        public static function get_by_name($name) {
            global $db;
    
            $raw = $db->run($db->prepare('SELECT id, name, abbreviation FROM weekday WHERE name = :value0'), [$name])[0];
            
            $weekday = new Weekday();

            if ($raw) {
                $weekday->id = $raw["id"];
                $weekday->name = $raw["name"];
                $weekday->abbreviation = $raw["abbreviation"];
            }

            return $weekday;
        }
    }

?>