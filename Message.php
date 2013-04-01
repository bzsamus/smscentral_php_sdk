<?php
class Message{
	function __construct($username, $password, $originator, $recipient, $message_text){
		$this->fields['USERNAME'] = $username;
		$this->fields['PASSWORD'] = $password;
		$this->fields['ACTION'] = 'send';
		$this->fields['ORIGINATOR'] = $originator;
		$this->fields['RECIPIENT'] = $recipient;
		$this->fields['MESSAGE_TEXT'] = $message_text;
	}
	
	function toArray(){
		return $this->fields;
	}
}

class BulkMessage extends Message{
	function __construct($message, $recipentmessages){
		$this->fields = $message->fields;
		$this->fields['RECIPIENTMESSAGES'] = $recipentmessages;
		unset($this->fields['RECIPIENT']);
		unset($this->fields['MESSAGE_TEXT']);
	}
}

class ScheduleMessage extends Message{
	function __construct($message, $scheduledate){
		$this->fields = $message->fields;
		$this->fields['SCHEDULE'] = $scheduledate;
	}
}
?>