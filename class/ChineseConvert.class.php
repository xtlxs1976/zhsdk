<?php
namespace zh;

require __DIR__ . '/../vendor/Chinese/Lunar.class.php';
require __DIR__ . '/../vendor/Chinese/Pinyin.class.php';
require __DIR__ . '/../vendor/MoneyConvertor/MoneyConvertor.php';


// 中文转换相关
class ChineseConvert{
	private $mc;
	private $py;
	private $lunar;

	public function __construct()
	{
		$this->mc = NULL;
		$this->py = NULL;
	}
	// 人民币大写
	public function rmb_uppstr($number){
		if(!isset($this->mc)){
			$this->mc = new \MoneyConvertor();
		}

		return '人民币'. $this->mc->convert($number);
	}

	// 人民币小写
	public function rmb_lowstr($number,$thousandth = true){
		if($thousandth){
			$tmp = number_format($number,2);
		}
		else
		{
			$tmp = $number;
		}
		return '￥'.$tmp ;
	}

	// $FirstType 是否转成首字母
	public function pinyin_str($chinese_str,$FirstType = false)
	{
		if(!isset($this->py)){
			$this->py = new \Pinyin();
		}

		return $this->py->strtopin($chinese_str,$FirstType);
	}

	public function lunar_timestamp($timestamp = 0)
	{
		if(!isset($this->lunar)){
			$this->lunar = new \Lunar();
		}

		if(empty($timestamp))
		{
			$ret = getdate(time());
		}
		else
		{
			$ret = getdate($timestamp);
		}

		$year  = $ret['year'];
		if($year < 1902 || $year > 2100 ) return false;

		$ret  = $this->lunar->convertSolarToLunar($year,$ret['mon'],$ret['mday']);
		return '农历' . $ret[0] .'年' . $ret[1] . $ret[2].'日';
	}

	public function lunar_date($year,$month,$day)
	{
		if(!isset($this->lunar)){
			$this->lunar = new \Lunar();
		}

		if(!checkdate($month,$day,$year))
		{
			return false;
		}

		if($year < 1902 || $year > 2029) return false;

		$ret  = $this->lunar->convertSolarToLunar($year,$month,$day);
		return '农历' . $ret[0] .'年' . $ret[1] . $ret[2].'日';
	}	
}
