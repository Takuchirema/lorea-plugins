<?php

class ElggPuSHEnvironment implements PuSHSubscriberEnvironmentInterface {
        // A message to be displayed to the user on the current page load.
        public function msg($msg, $level = 'status') {
                system_message($msg);
        }
        // A log message to be logged to the database or the file system
        public function log($msg, $level = 'status') {
        }
}

