<?php
include('Message.php');

class SMS{
	function __construct($gateway, $username, $password){
		$this->gateway = $gateway;
		$this->username = $username;
		$this->password = $password;
	}
	
	function postMessage($postFields){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->gateway);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		if($result === false){
			$result = curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}
	
	function sendSMS($originator, $recipient, $text){
		$message = new Message($this->username, $this->password, $originator, $recipient, $text);
		return $this->postMessage($message->toArray());
	}
	
	function sendBulk($messages){
		$this->bulkTasks = array();
		$this->scheduleTasks = array();
		// building up task queue
		foreach($messages as $m){
			$msg = array('RECIPIENT'=>$m['to'], 'MESSAGE_TEXT'=>$m['message']);
			if(isset($m['scheduledate'])){
				$this->scheduleTasks[$m['from']][$m['scheduledate']][] = $msg;
			}
			else{
				$this->bulkTasks[$m['from']][] = $msg;
			}
		}
		// sending out messages
		foreach($this->bulkTasks as $key=>$task){
			$message = new Message($this->username, $this->password, $key, '', '');
			$bulkMessage = new BulkMessage($message, json_encode($task));
			$result[] = $this->postMessage($bulkMessage->toArray());
		}
		foreach($this->scheduleTasks as $key=>$task){
			$message = new Message($this->username, $this->password, $key, '', '');
			$scheduleMessage = new ScheduleMessage($message, key($task));
			$bulkScheduleMessage = new BulkMessage($scheduleMessage, json_encode(array_pop($task)));
			$result[] = $this->postMessage($bulkScheduleMessage->toArray());
		}
		return json_encode($result);
	}
}
?>