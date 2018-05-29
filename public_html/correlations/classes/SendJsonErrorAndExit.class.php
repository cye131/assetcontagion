<?php
class SendJsonErrorAndExit {
    public $jsonmessage = array();
    
    function __construct($message) {
        $this -> message = $message;
    }

    public function sendJsonErrorAndExit() {
        $jsonmessage = array(
          'errstatus' => (boolean) 1,
          'errmessage' => $this->message
        );
        echo json_encode($jsonmessage);
        exit();
    }

}
