<?php
class dateSub {
	static public function fromDbWithTime($str) {
		if(empty($str))
			return $str;
		$dateTime = explode(' ', $str);
		$yearMonthDay = explode('-', $dateTime[0]);
		$hoursMinutesSeconds = explode(':', $dateTime[1]);
		$timestamp = mktime($hoursMinutesSeconds[0], $hoursMinutesSeconds[1], $hoursMinutesSeconds[2], $yearMonthDay[1], $yearMonthDay[2], $yearMonthDay[0]);
		return date(SUB_DATE_FORMAT_HIS, $timestamp);
	}
	static public function getDbWithTime($timestamp = NULL) {
		if(!$timestamp)
			$timestamp = time();
		return date('Y-m-d H:i:s', $timestamp);
	}
	static public function getDb($timestamp = NULL) {
		if(!$timestamp)
			$timestamp = time();
		return date('Y-m-d', $timestamp);
	}
	static public function toDb($str) {
		return self::getDb(strtotime($str));
	}
}