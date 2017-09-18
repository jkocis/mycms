<?php

/**
 * MyCMS
 * 
 * @author Jan Kocis
 */
declare(strict_types = 1);

namespace MyCms;

use Nette;

class DateTime extends Nette\Utils\DateTime
{
	const FORMAT_DEFAULT = 'j.n.Y H:i:s';

	/** @var array */
	private static $holidays = ['12-24', '12-25', '12-26', '12-31', '01-01', '05-01', '05-08', '07-05', '07-06', '09-28', '10-28', '11-17'];

	/**
	 * @return self
	 */
	public function addWorkday(int $amount = 1)
	{
		for ($i = 0; $i < $amount; $i++) {
			$this->modify('+1 day');
			while (!$this->isWorkingDay()) {
				$this->modify('+1 day');
			}
		}

		return $this;
	}

	/**
	 * Vrátí nejbližší pracovní den (včetně aktuálního)
	 * @return self
	 */
	public function getClosestWorkday()
	{
		while (!$this->isWorkingDay()) {
			$this->modify('+1 day');
		}

		return $this;
	}

	/**
	 * Vrátí defaultně počet dní od teď
	 * 
	 * !! př. 18.1.2016 16:00 -> 20.1.2016 14:00 => 1 den a 22 hodin
	 * 
	 * @param string
	 */
	public function getDistanceFromNow(string $type = 'd')
	{
		$today = new self;
		$diff = $this->diff($today);

		if ($type) {
			return $diff->{$type};
		}

		return $diff;
	}

	/**
	 * Vrátí abs počet dní od teď vyjádřeno na dny
	 * 
	 * !! př. 18.1.2016 16:00 -> 20.1.2016 14:00 => 2
	 * 
	 * @return int
	 */
	public function getDaysFromNow(): int
	{
		$today = new self(date('Y-m-d'));
		return (int) $this->diff($today)->days;
	}

	public function isToday(): bool
	{
		return $this->format('Y-m-d') === self::from(NULL)->format('Y-m-d');
	}

	public function isWorkingDay(): bool
	{
		return $this->format('N') >= 6 || $this->isHoliday() ? FALSE : TRUE;
	}

	public function isTomorrowWorkingDay(): bool
	{
		$day = clone $this;
		$day->modify('+1 day');
		return $day->isWorkingDay();
	}

	public function isHoliday(): bool
	{
		if (in_array($this->format('m-d'), self::$holidays)) {
			return TRUE;
		}

		$easterSunday = easter_date((int) $this->format('Y'));

		$easterFriday = strftime('%m-%d', strtotime('- 2 day', $easterSunday)); // velký pátek
		$easterMonday = strftime('%m-%d', strtotime('+ 1 day', $easterSunday)); // velikonoční pondělí

		if ($this->format('m-d') === $easterFriday || $this->format('m-d') === $easterMonday) {
			return TRUE;
		}

		return FALSE;
	}

	public function isFuture(): bool
	{
		return $this > new self;
	}

	public function isPast(): bool
	{
		return !$this->isFuture();
	}
}