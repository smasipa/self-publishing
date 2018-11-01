<?php
namespace App\Utility
{
	class Time_Methods{
		
		static private $today;
		
		function __construct(){}
		
		static function get_time_diff($array_time = array()){
			//Returns string
			list($fyear,$fmonth,$fday) = explode(' ',date('Y m d',$array_time['par_date']));
			$par_date = $array_time['par_date'];//Past or future date
			$today_date = time();
			if(array_key_exists('today', $array_time)){
				list($tyear,$tmonth,$tday) = explode(' ',date('Y m d',$array_time['today']));
				$today_date = $array_time['today'];
			}else{
				list($tyear,$tmonth,$tday) = explode(' ',date('Y m d',time()));
			}
			
			$is_past = $today_date >= $par_date ? true : false;
			
			$yearDiff = $fyear - $tyear;
			if($yearDiff == 0){
				$monthDiff = !$is_past ? $fmonth - $tmonth : $tmonth - $fmonth;
				
				if($monthDiff == 0){
					$elapsed = !$is_past ? (int)($par_date - $today_date) : (int)($today_date - $par_date);
					$hrsDiff = (int)($elapsed/(60*60));
					$mins = (int)($elapsed/(60));
					$secs = (int)($elapsed/(1000));
					$days = (int)($hrsDiff/24);
					
					if($hrsDiff == 0){
						if($mins <= 0) $mins = 'just a sec';
						else
						$mins = $mins == 1 ? $mins." min" : $mins. " mins";
						return $mins;
					}elseif($hrsDiff > 0 && $hrsDiff < 24){
						$hrs = $hrsDiff == 1 ? $hrsDiff." hr" : $hrsDiff. " hrs";
						return $hrs;
					}
					
					if($days >= 1){
						$newdiffHrs = (int)(($par_date - strtotime($days . ' days')) / (60*60));
						$hrs = $newdiffHrs == 1 ? $newdiffHrs." hr" : $newdiffHrs. " hrs";
						$hrs = !$is_past ? " " . $hrs : '';
						$days = $days == 1 ? $days." day" : $days. " days" ;
						return $days . $hrs;
					}
				}else{
					$monthDiff = $monthDiff == 1 ? $monthDiff." month" : $monthDiff. " months";
					return $monthDiff;
				}
			}
			else
			{
				return date('d M Y', $par_date);
			}
		}
		
		static function getDateToday($sep='-'){
			return date('Y' . $sep .'m'. $sep .'d');
		}
		
		static function getCurrentTime($sep=':'){
			return date('H' . $sep .'i'. $sep .'s');
		}
		
		static function extractDate($timestamp){
			$split = explode(' ',$timestamp);
			$ret = array();
			$ret['date'] = explode('-',$split[0]);
			$ret['time'] = $split[1];
			return $ret;
		}
		
		static function getElapsedTime($time){
			// $days = (int)($hrsDiff/24);
			$days = (int)(($time/(60*60))/24);
			if($days != 0){
				$d = $days == 1 ? 'day' : 'days';
				return array($days,$d);
			} 
			$hrs = (int)($time/(60*60));
			if($hrs != 0){
				$h = $hrs == 1 ? 'hr' : 'hrs';
				return array($hrs,$h);
			} 
			
			$mins = (int)($time/(60));
			if($mins){
				$m = $mins == 1 ? 'min' : 'mins';
				return array($mins,$m);
			} 
			// $secs = (int)($time/(1000));
		}
	}
}
?>