<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/8/2015
 * Time: 1:48 PM
 */
class NAVDateFormulaManagement
{
    const REGEX_PATTERN_EXPRESSION = '/^[\+\-]{0,1}(?:(?:[0-9]+(?:D|T|WD|WT|W|M|Q|Y|J))|(?:(?:D|T|WD|WT|W|M|Q|Y|J)[0-9]+)|(?:[CL]{1}(?:D|T|WD|WT|W|M|Q|Y|J)))(?:[\+\-]{1}(?:(?:[0-9]+(?:D|T|WD|WT|W|M|Q|Y|J))|(?:(?:D|T|WD|WT|W|M|Q|Y|J)[0-9]+)|(?:[CL]{1}(?:D|T|WD|WT|W|M|Q|Y|J))))*$/';
    const REGEX_PATTERN_SUBEXPRESSION = '/([\+\-]{0,1}(?:(?:[0-9]+(?:D|T|WD|WT|W|M|Q|Y|J))|(?:(?:D|T|WD|WT|W|M|Q|Y|J)[0-9]+)|(?:[CL]{1}(?:D|T|WD|WT|W|M|Q|Y|J))))/';
    const REGEX_PATTERN_SIGN = '/^([\+\-]{1})/';
    const REGEX_PATTERN_NO_OF_INTERVALS = '/^(?:[\+\-]{0,1})([0-9]+)/';
    const REGEX_PATTERN_PREFIX = '/^(?:[\+\-]{0,1})(?:[0-9]{0,})([CL]{1})/';
    const REGEX_PATTERN_INTERVAL_TERM = '/(D|T|WD|WT|W|M|Q|Y|J)/';
    const REGEX_PATTERN_INTERVAL_ORDINAL = '/(?:D|T|WD|WT|W|M|Q|Y|J)([0-9]+)$/';

    /**
     * @param $dateExpr
     * @return int
     */
    public function isValidDateExpression($dateExpr)
    {
        return preg_match(self::REGEX_PATTERN_EXPRESSION, $dateExpr);
    }

    /**
     * @param $subExp
     * @return int
     */
    public function isValidDateSubExpression($subExp)
    {
        return preg_match(self::REGEX_PATTERN_SUBEXPRESSION, $subExp);
    }

    /**
     * @param $dateExpr
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getAllSubExpressions($dateExpr)
    {
        if (!$this->isValidDateExpression($dateExpr)) {
            $sanitized = str_replace(array('<', '>'), '', $dateExpr);
            throw new \InvalidArgumentException("DateFormula $sanitized is invalid.");
        }
        $matches = array();
        $returnMatches = array();
        if (preg_match_all(self::REGEX_PATTERN_SUBEXPRESSION, $dateExpr, $matches)) {
            $returnMatches = $matches[0];
        }
        return $returnMatches;
    }

    /**
     * @param $subExp
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getSubExpressionSign($subExp)
    {
        if (!$this->isValidDateSubExpression($subExp)) {
            $sanitized = str_replace(array('<', '>'), '', $subExp);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $matches = array();
        preg_match(self::REGEX_PATTERN_SIGN, $subExp, $matches);
        if (isset($matches[0]) && $matches[0] === '-') {
            return '-';
        } else {
            return '+';
        }
    }

    /**
     * @param $subExp
     * @return int
     * @throws \InvalidArgumentException
     */
    public function getSubExpressionNoOfIntervals($subExp)
    {
        if (!$this->isValidDateSubExpression($subExp)) {
            $sanitized = str_replace(array('<', '>'), '', $subExp);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $matches = array();
        preg_match(self::REGEX_PATTERN_NO_OF_INTERVALS, $subExp, $matches);
        if (isset($matches[1]) && (int)$matches[1] > 0) {
            return (int)$matches[1];
        } else {
            return 0;
        }
    }

    /**
     * @param $subExp
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getSubExpressionPrefix($subExp)
    {
        if (!$this->isValidDateSubExpression($subExp)) {
            $sanitized = str_replace(array('<', '>'), '', $subExp);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $matches = array();
        preg_match(self::REGEX_PATTERN_PREFIX, $subExp, $matches);
        if (isset($matches[1])) {
            return 'C';
        } else {
            return '';
        }
    }

    /**
     * @param $subExp
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getSubExpressionIntervalTerm($subExp)
    {
        if (!$this->isValidDateSubExpression($subExp)) {
            $sanitized = str_replace(array('<', '>'), '', $subExp);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $matches = array();
        preg_match(self::REGEX_PATTERN_INTERVAL_TERM, $subExp, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        } else {
            return 'CD';
        }
    }

    /**
     * @param $subExp
     * @return int
     * @throws \InvalidArgumentException
     */
    public function getSubExpressionIntervalOrdinal($subExp)
    {
        if (!$this->isValidDateSubExpression($subExp)) {
            $sanitized = str_replace(array('<', '>'), '', $subExp);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $matches = array();
        preg_match(self::REGEX_PATTERN_INTERVAL_ORDINAL, $subExp, $matches);
        if (isset($matches[1]) && (int)$matches[1] > 0) {
            return (int)$matches[1];
        } else {
            return 0;
        }
    }

    /**
     * @param $formula
     * @param \DateTime $dateTime
     * @throws \InvalidArgumentException
     */
    public function evaluateDateFormula($formula, \DateTime $dateTime)
    {
        $formula = str_replace(array('<', '>'), '', $formula);
        if (!$this->isValidDateExpression($formula)) {
            throw new \InvalidArgumentException('Argument ' . $formula . ' is not a valid DateFormula');
        }

        $subExpressions = $this->getSubExpressionArray($formula);
        foreach ($subExpressions as $subExpression) {
            $this->_evaluateSubExpression($subExpression, $dateTime);
        }
    }

    /**
     * @param \DateTime $referenceDateTime
     * @return int
     */
    public function getMinusCW(\DateTime $referenceDateTime)
    {
        return strtotime('last monday', strtotime('tomorrow', $referenceDateTime->getTimestamp()));
    }

    /**
     * @param \DateTime $referenceDateTime
     * @return int
     */
    public function getPlusCW(\DateTime $referenceDateTime)
    {
        return strtotime('next sunday + 1 day', strtotime('yesterday', $referenceDateTime->getTimestamp()));
    }

    /**
     * @param \DateTime $referenceDateTime
     * @return int
     */
    public function getMinusCM(\DateTime $referenceDateTime)
    {
        return strtotime('first day of this month', $referenceDateTime->getTimestamp());
    }

    /**
     * @param \DateTime $referenceDateTime
     * @return int
     */
    public function getPlusCM(\DateTime $referenceDateTime)
    {
        return strtotime('last day of this month', $referenceDateTime->getTimestamp());
    }

    /**
     * @param \DateTime $referenceDateTime
     * @return mixed
     */
    public function getMinusCQ(\DateTime $referenceDateTime)
    {
        return $this->getStartEndDateForCurrentQuarter($referenceDateTime)[0];
    }

    /**
     * @param \DateTime $referenceDateTime
     * @return mixed
     */
    public function getPlusCQ(\DateTime $referenceDateTime)
    {
        return $this->getStartEndDateForCurrentQuarter($referenceDateTime)[1];
    }

    /**
     * @param \DateTime $referenceDateTime
     * @return int
     */
    public function getMinusCY(\DateTime $referenceDateTime)
    {
        $year = date('Y', $referenceDateTime->getTimestamp());
        return strtotime('01-January-' . $year);
    }

    /**
     * @param \DateTime $referenceDateTime
     * @return int
     */
    public function getPlusCY(\DateTime $referenceDateTime)
    {
        $year = date('Y', $referenceDateTime->getTimestamp());
        return strtotime('31-December-' . $year);
    }

    /**
     * @param $subExp
     * @param \DateTime $dateRef
     */
    protected function _evaluateSubExpression($subExp, \DateTime $dateRef)
    {
        if (!$this->isValidDateSubExpression($subExp)) {
            $sanitized = str_replace(array('<', '>'), '', $subExp);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }

        $sign = $this->getSubExpressionSign($subExp);
        $prefix = $this->getSubExpressionPrefix($subExp);
        $noOfIntervals = $this->getSubExpressionNoOfIntervals($subExp);
        $intervalTerm = $this->getSubExpressionIntervalTerm($subExp);
        $intervalOrdinal = $this->getSubExpressionIntervalOrdinal($subExp);
        if ($prefix === 'C' || $prefix === 'L') {
            $prefix = 'C';
        }
        if ($intervalTerm === 'T') {
            $intervalTerm = 'D';
        } elseif ($intervalTerm === 'WT') {
            $intervalTerm = 'WD';
        } elseif ($intervalTerm === 'J') {
            $intervalTerm = 'Y';
        }

        //NAV handles WD/WT when preceded by a number as 'D'
        if ($noOfIntervals > 0 && $intervalTerm == 'WD') {
            $intervalTerm = 'D';
        }

        if ($prefix === 'C') {
            if ($intervalTerm === 'D' || $intervalTerm === 'WD') {
                //Day is equal to reference date, thus no change in $dateRef
                return;
            }
            $callableNamePrefix = 'get';
            $signNamePart = 'Plus';
            if ($sign === '-') {
                $signNamePart = 'Minus';
            }
            $callableName = $callableNamePrefix . $signNamePart . $prefix . $intervalTerm;
            $newTimestamp = $this->{$callableName}($dateRef);
            $dateRef->setTimestamp($newTimestamp);
        } elseif ($prefix === '' && $noOfIntervals > 0) {
            $this->traverseDateAndMutateRef($sign, $noOfIntervals, $intervalTerm, $dateRef);
        } elseif ($prefix === '' && $intervalOrdinal > 0) {
            $this->mutateDateTimeForOrdinalExpression($sign, $intervalTerm, $intervalOrdinal, $dateRef);
        }
    }

    /**
     * @param $sign
     * @param $intervalTerm
     * @param $intervalOrdinal
     * @param \DateTime $dateRef
     */
    public function mutateDateTimeForOrdinalExpression($sign, $intervalTerm, $intervalOrdinal, \DateTime $dateRef)
    {
        $combined = $sign . $intervalTerm . $intervalOrdinal;
        if (!$this->isValidDateSubExpression($combined)) {
            $sanitized = str_replace(array('<', '>'), '', $combined);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        switch ($intervalTerm) {
            case 'D':
                $this->mutateDateTimeForDayOrdinal($sign, $intervalOrdinal, $dateRef);
                break;
            case 'T':
                $this->mutateDateTimeForDayOrdinal($sign, $intervalOrdinal, $dateRef);
                break;
            case 'WD':
                $this->mutateDateTimeForWeekDayOrdinal($sign, $intervalOrdinal, $dateRef);
                break;
            case 'WT':
                $this->mutateDateTimeForWeekDayOrdinal($sign, $intervalOrdinal, $dateRef);
                break;
            case 'W':
                $this->mutateDateTimeForWeekOrdinal($sign, $intervalOrdinal, $dateRef);
                break;
            case 'M':
                $this->mutateDateTimeForMonthOrdinal($sign, $intervalOrdinal, $dateRef);
                break;
            case 'Q':
                $this->mutateDateTimeForQuarterOrdinal($sign, $intervalOrdinal, $dateRef);
                break;
            case 'Y':
                $this->mutateDateTimeForYearOrdinal($sign, $intervalOrdinal, $dateRef);
                break;
            case 'J':
                $this->mutateDateTimeForYearOrdinal($sign, $intervalOrdinal, $dateRef);
                break;
        }
    }

    /**
     * @param $sign
     * @param $intervalOrdinal
     * @param \DateTime $dateRef
     */
    protected function mutateDateTimeForDayOrdinal($sign, $intervalOrdinal, \DateTime $dateRef)
    {
        $intervalTerm = 'D';
        $combined = $sign . $intervalTerm . $intervalOrdinal;
        if (!$this->isValidDateSubExpression($combined)) {
            $sanitized = str_replace(array('<', '>'), '', $combined);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $dateRefInitTimestamp = $dateRef->getTimestamp();
        $dateRefInitMidnightTimestamp = strtotime($dateRef->format('d.m.Y'));
        $dateRefInitDayOfMonthNo = strftime('%d', $dateRefInitTimestamp);
        $dateRefInitMonthNo = strftime('%m', $dateRefInitTimestamp);
        $dateRefInitYear4digit = strftime('%Y', $dateRefInitTimestamp);

        $intermediateTimestamp = strtotime(sprintf('%02d.%02d.%4d', $intervalOrdinal, $dateRefInitMonthNo, $dateRefInitYear4digit));
        $finalTimestamp = $intermediateTimestamp;
        if ($sign === '+' && (($intermediateTimestamp <= $dateRefInitMidnightTimestamp))) {
            $calcMonthNo = (int)$dateRefInitMonthNo + 1;
            $calcYearNo = $dateRefInitYear4digit;
            if ($calcMonthNo === 13) {
                $calcMonthNo = '01';
                $calcYearNo = (int)$calcYearNo + 1;
            }
            $finalTimestamp = strtotime(sprintf('%02d.%02d.%4d', $intervalOrdinal, $calcMonthNo, $calcYearNo));
        } elseif ($sign === '-' && ($intermediateTimestamp <= $dateRefInitMidnightTimestamp)) {
            $finalTimestamp = $intermediateTimestamp;
        } elseif ($sign === '-' && ($intermediateTimestamp > $dateRefInitMidnightTimestamp)) {
            $calcMonthNo = (int)$dateRefInitDayOfMonthNo - 1;
            $calcYearNo = $dateRefInitYear4digit;
            if ($calcMonthNo === 0) {
                $calcMonthNo = '12';
                $calcYearNo = (int)$calcYearNo - 1;
            }
            $finalTimestamp = strtotime(sprintf('%02d.%02d.%4d', $intervalOrdinal, $calcMonthNo, $calcYearNo));
        }
        $dtStr = date("c", $finalTimestamp);
        $newDateTime = new \DateTime($dtStr);
        $dateRef->setTimestamp($newDateTime->getTimestamp());
    }

    /**
     * @param $sign
     * @param $intervalOrdinal
     * @param \DateTime $dateRef
     */
    protected function mutateDateTimeForWeekDayOrdinal($sign, $intervalOrdinal, \DateTime $dateRef)
    {
        $intervalTerm = 'WD';
        $combined = $sign . $intervalTerm . $intervalOrdinal;
        if (!$this->isValidDateSubExpression($combined)) {
            $sanitized = str_replace(array('<', '>'), '', $combined);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }

        $dateRefInitTimestamp = $dateRef->getTimestamp();
        $dateRefInitWeekNo = $dateRef->format('W');
        $dateRefInitDayOfWeek = date("N", $dateRefInitTimestamp);
        $dateRefInitYear4digit = $dateRef->format('Y');
        $startEndOfRefWeekArr = $this->getStartAndEndDateTimestampForWeek($dateRefInitWeekNo, $dateRefInitYear4digit);
        $startOfRefWeek = $startEndOfRefWeekArr[0];
        $startOfPrevWeek = strtotime("-1 weeks", $startOfRefWeek);
        $startOfNextWeek = strtotime("+1 weeks", $startOfRefWeek);

        $targetTimestamp = $startOfNextWeek;
        if(($sign === '+' && ($dateRefInitDayOfWeek < $intervalOrdinal))||($sign === '-' && ($dateRefInitDayOfWeek > $intervalOrdinal))) {
            $targetTimestamp = $startOfRefWeek;
        } elseif($sign === '-') {
            $targetTimestamp = $startOfPrevWeek;
        }

        if ($intervalOrdinal > 1) {
            $noOfDaysToTraverse = $intervalOrdinal - 1;
            $targetTimestamp = strtotime("+$noOfDaysToTraverse days", $targetTimestamp);
        }

        $dtStr = date("c", $targetTimestamp);
        $newDateTime = new \DateTime($dtStr);
        $dateRef->setTimestamp($newDateTime->getTimestamp());
    }

    /**
     * @param $sign
     * @param $intervalOrdinal
     * @param \DateTime $dateRef
     */
    protected function mutateDateTimeForWeekOrdinal($sign, $intervalOrdinal, \DateTime $dateRef)
    {
        $intervalTerm = 'W';
        $combined = $sign . $intervalTerm . $intervalOrdinal;
        if (!$this->isValidDateSubExpression($combined)) {
            $sanitized = str_replace(array('<', '>'), '', $combined);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $dateRefInitTimestamp = $dateRef->getTimestamp();
        $dateRefInitYear4digit = strftime('%Y', $dateRefInitTimestamp);
        $dateRefInitWeekNo = strftime('%V', $dateRefInitTimestamp);

        $finalTimestamp = strtotime(sprintf("%4dW%02d", $dateRefInitYear4digit, $intervalOrdinal));
        if ($sign === '' && ($dateRefInitWeekNo >= $intervalOrdinal)) {
            $calcYearNo = (int)$dateRefInitYear4digit + 1;
            $finalTimestamp = strtotime(sprintf("%4dW%02d", $calcYearNo, $intervalOrdinal));
        } elseif ($sign === '-' && ($dateRefInitWeekNo < $intervalOrdinal)) {
            $calcYearNo = (int)$dateRefInitYear4digit - 1;
            $finalTimestamp = strtotime(sprintf("%4dW%02d", $calcYearNo, $intervalOrdinal));
        }
        $dtStr = date("c", $finalTimestamp);
        $newDateTime = new \DateTime($dtStr);
        $dateRef->setTimestamp($newDateTime->getTimestamp());
    }

    /**
     * @param $sign
     * @param $intervalOrdinal
     * @param \DateTime $dateRef
     * @throws \InvalidArgumentException
     */
    protected function mutateDateTimeForMonthOrdinal($sign, $intervalOrdinal, \DateTime $dateRef)
    {
        $intervalTerm = 'M';
        $combined = $sign . $intervalTerm . $intervalOrdinal;
        if (!$this->isValidDateSubExpression($combined)) {
            $sanitized = str_replace(array('<', '>'), '', $combined);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $dateRefInitTimestamp = $dateRef->getTimestamp();
        $dateRefInitYear4digit = strftime('%Y', $dateRefInitTimestamp);
        $dateRefInitMonthNo = strftime('%m', $dateRefInitTimestamp);

        $newDateTime = \DateTime::createFromFormat('d.m.Y', sprintf('%02d.%02d.%04d', '01', $intervalOrdinal, $dateRefInitYear4digit));
        if ($sign === '' && ($dateRefInitMonthNo >= $intervalOrdinal)) {
            $calcYearNo = (int)$dateRefInitYear4digit + 1;
            $newDateTime = \DateTime::createFromFormat('d.m.Y', sprintf('%02d.%02d.%04d', '01', $intervalOrdinal, $calcYearNo));
        } elseif ($sign === '-' && ($dateRefInitMonthNo < $intervalOrdinal)) {
            $calcYearNo = (int)$dateRefInitYear4digit - 1;
            $newDateTime = \DateTime::createFromFormat('d.m.Y', sprintf('%02d.%02d.%04d', '01', $intervalOrdinal, $calcYearNo));
        }
        $dateRef->setTimestamp($newDateTime->getTimestamp());
    }

    /**
     * @param $sign
     * @param $intervalOrdinal
     * @param \DateTime $dateRef
     */
    protected function mutateDateTimeForQuarterOrdinal($sign, $intervalOrdinal, \DateTime $dateRef)
    {
        $intervalTerm = 'Q';
        $combined = $sign . $intervalTerm . $intervalOrdinal;
        if (!$this->isValidDateSubExpression($combined)) {
            $sanitized = str_replace(array('<', '>'), '', $combined);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $dateRefInitTimestamp = $dateRef->getTimestamp();
        $dateRefInitYear4digit = strftime('%Y', $dateRefInitTimestamp);
        $dateRefInitMonthNo = strftime('%m', $dateRefInitTimestamp);
        $dateRefInitQuarterNo = ceil($dateRefInitMonthNo / 3);


        if ($intervalOrdinal < 1) {
            $intervalOrdinal = 1;
        }
        if ($intervalOrdinal > 4) {
            $intervalOrdinal = 4;
        }
        $endMonthNoTargetQuarter = ($intervalOrdinal * 3);
        $startMonthTargetQuarter = $endMonthNoTargetQuarter - 2;

        $newDateTime = \DateTime::createFromFormat('d.m.Y', sprintf('%02d.%02d.%04d', '01', $startMonthTargetQuarter, $dateRefInitYear4digit));
        if ($sign === '' && ($dateRefInitQuarterNo >= $intervalOrdinal)) {
            $calcYearNo = (int)$dateRefInitYear4digit + 1;
            $newDateTime = \DateTime::createFromFormat('d.m.Y', sprintf('%02d.%02d.%04d', '01', $startMonthTargetQuarter, $calcYearNo));
        } elseif ($sign === '-' && ($dateRefInitQuarterNo < $intervalOrdinal)) {
            $calcYearNo = (int)$dateRefInitYear4digit - 1;
            $newDateTime = \DateTime::createFromFormat('d.m.Y', sprintf('%02d.%02d.%04d', '01', $startMonthTargetQuarter, $calcYearNo));
        }
        $dateRef->setTimestamp($newDateTime->getTimestamp());
    }

    /**
     * @param $sign
     * @param $intervalOrdinal
     * @param \DateTime $dateRef
     */
    protected function mutateDateTimeForYearOrdinal($sign, $intervalOrdinal, \DateTime $dateRef)
    {
        $intervalTerm = 'Y';
        $combined = $sign . $intervalTerm . $intervalOrdinal;
        if (!$this->isValidDateSubExpression($combined)) {
            $sanitized = str_replace(array('<', '>'), '', $combined);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $dateRefInitTimestamp = $dateRef->getTimestamp();
        $dateRefInitYear4digit = strftime('%y', $dateRefInitTimestamp);
        $dateRefInitLast2Digits = substr($dateRefInitYear4digit, -2, 2);
        $dateRefInitFirst2Digits = substr($dateRefInitYear4digit, 0, 2);

        if ($intervalOrdinal >= $dateRefInitLast2Digits) {
            $newYearStr = $dateRefInitFirst2Digits . $intervalOrdinal;
        } else {
            $newYearStr = sprintf('%02d%02d', ((int)$dateRefInitFirst2Digits + 1), $intervalOrdinal);
        };
        $newDateTime = \DateTime::createFromFormat('d.m.Y', sprintf('%02d.%02d.%04d', '01', '01', $newYearStr));
        $dateRef->setTimestamp($newDateTime->getTimestamp());
    }

    /**
     * @param $sign
     * @param $noOfTraversals
     * @param $intervalTerm
     * @param \DateTime $dateRef
     * @throws \InvalidArgumentException
     */
    public function traverseDateAndMutateRef($sign, $noOfTraversals, $intervalTerm, \DateTime $dateRef)
    {
        $combined = $sign . $noOfTraversals . $intervalTerm;
        if (!$this->isValidDateSubExpression($combined)) {
            $sanitized = str_replace(array('<', '>'), '', $combined);
            throw new \InvalidArgumentException("SubExpression $sanitized is invalid.");
        }
        $strtotimeIntervalTerm = $this->getIntervalTermStrtotimeMapping($intervalTerm);
        $newTime = strtotime("$sign $noOfTraversals $strtotimeIntervalTerm", $dateRef->getTimestamp());
        $dtStr = date("c", $newTime);
        $newDateTime = new \DateTime($dtStr);
        $dateRef->setTimestamp($newDateTime->getTimestamp());
    }

    /**
     * @param $formula
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getSubExpressionArray($formula)
    {
        if (!$this->isValidDateExpression($formula)) {
            throw new \InvalidArgumentException('Argument is not a valid DateFormula');
        }

        $firstChar = mb_substr($formula, 0);
        $lastChar = mb_substr($formula, -1);

        if ($firstChar === '<' && $lastChar === '>') {
            $formula = rtrim(ltrim($formula, '<'), '>');
        }

        $subExpressionRegex = '/([\+\-]{0,1}(?:(?:[0-9]+(?:D|T|WD|WT|W|M|Q|Y|J))|(?:(?:D|T|WD|WT|W|M|Q|Y|J)[0-9]+)|(?:[CL]{1}(?:D|T|WD|WT|W|M|Q|Y|J))))/';
        $allSubExpressions = array();
        preg_match_all($subExpressionRegex, $formula, $allSubExpressions);
        return $allSubExpressions[0];
    }

    /**
     * @param \DateTime $dateTime
     * @return int
     */
    public function getCurrentQuarterStartDate(\DateTime $dateTime)
    {
        $currentMonth = date('m', $dateTime->getTimestamp());
        $currentYear = date('Y', $dateTime->getTimestamp());
        $startDate = $dateTime->getTimestamp();
        if ($currentMonth >= 1 && $currentMonth <= 3) {
            $startDate = strtotime('1-January-' . $currentYear);  // timestamp or 1-January 12:00:00 AM
        } elseif ($currentMonth >= 4 && $currentYear <= 6) {
            $startDate = strtotime('1-April-' . $currentYear);  // timestamp or 1-April 12:00:00 AM
        } elseif ($currentMonth >= 7 && $currentMonth <= 9) {
            $startDate = strtotime('1-July-' . $currentYear);  // timestamp or 1-July 12:00:00 AM
        } elseif ($currentMonth >= 10 && $currentMonth <= 12) {
            $startDate = strtotime('1-October-' . $currentYear);  // timestamp or 1-October 12:00:00 AM
        }
        return $startDate;
    }

    /**
     * @param \DateTime $dateTime
     * @return int
     */
    public function getNextQuarterStartDate(\DateTime $dateTime)
    {
        $currentMonth = date('m', $dateTime->getTimestamp());
        $currentYear = date('Y', $dateTime->getTimestamp());
        $startDate = $dateTime->getTimestamp();
        if ($currentMonth >= 1 && $currentMonth <= 3) {
            $startDate = strtotime('1-April-' . $currentYear);  // timestamp or 1-April 12:00:00 AM
        } elseif ($currentMonth >= 4 && $currentYear <= 6) {
            $startDate = strtotime('1-July-' . $currentYear);  // timestamp or 1-July 12:00:00 AM
        } elseif ($currentMonth >= 7 && $currentMonth <= 9) {
            $startDate = strtotime('1-October-' . $currentYear);  // timestamp or 1-October 12:00:00 AM
        } elseif ($currentMonth >= 10 && $currentMonth <= 12) {
            $startDate = strtotime('1-January-' . $currentYear);  // timestamp or 1-January 12:00:00 AM
        }
        return $startDate;
    }

    /**
     * @param \DateTime $dateTime
     * @return array
     */
    public function getStartEndDateForCurrentQuarter(\DateTime $dateTime)
    {
        $dtTimeStamp = $dateTime->getTimestamp();
        $currentMonth = date('m', $dtTimeStamp);
        $currentYear = date('Y', $dtTimeStamp);
        $startDate = $dtTimeStamp;
        $endDate = $dtTimeStamp;
        if ($currentMonth >= 1 && $currentMonth <= 3) {
            $startDate = strtotime('1-January-' . $currentYear);  // timestamp or 1-Januray 12:00:00 AM
            $endDate = strtotime('1-April-' . $currentYear);  // timestamp or 1-April 12:00:00 AM means end of 31 March
        } elseif ($currentMonth >= 4 && $currentMonth <= 6) {
            $startDate = strtotime('1-April-' . $currentYear);  // timestamp or 1-April 12:00:00 AM
            $endDate = strtotime('1-July-' . $currentYear);  // timestamp or 1-July 12:00:00 AM means end of 30 June
        } elseif ($currentMonth >= 7 && $currentMonth <= 9) {
            $startDate = strtotime('1-July-' . $currentYear);  // timestamp or 1-July 12:00:00 AM
            $endDate = strtotime('1-October-' . $currentYear);  // timestamp or 1-October 12:00:00 AM means end of 30 September
        } elseif ($currentMonth >= 10 && $currentMonth <= 12) {
            $startDate = strtotime('1-October-' . $currentYear);  // timestamp or 1-October 12:00:00 AM
            $endDate = strtotime('1-January-' . ($currentYear + 1));  // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
        }
        $dateArr = array($startDate, $endDate);
        return $dateArr;
    }

    /**
     * @param $term
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getIntervalTermStrtotimeMapping($term)
    {
        $matches = array();
        if (!preg_match(self::REGEX_PATTERN_INTERVAL_TERM, $term, $matches)) {
            throw new \InvalidArgumentException("$term is not a valid interval-term.");
        }
        $term = $matches[1];
        $strtotimeExp = '';
        switch ($term) {
            case 'D':
                $strtotimeExp = 'days';
                break;
            case 'T':
                $strtotimeExp = 'days';
                break;
            case 'WD':
                $strtotimeExp = 'weekdays';
                break;
            case 'WT':
                $strtotimeExp = 'weekdays';
                break;
            case 'W':
                $strtotimeExp = 'weeks';
                break;
            case 'M':
                $strtotimeExp = 'months';
                break;
            case 'Q':
                $strtotimeExp = 'months';
                break; //special case - calculation by consumer is necessary
            case 'Y':
                $strtotimeExp = 'years';
                break;
            case 'J':
                $strtotimeExp = 'years';
                break;
        }
        return $strtotimeExp;
    }

    /**
     * @param $week
     * @param $year
     * @return array
     */
    public function getStartAndEndDateTimestampForWeek($week, $year)
    {
        $startDateTime = new \DateTime();
        $startDateTime->setISODate($year, $week);
        $endDateTime = new \DateTime();
        $endDateTime->setISODate($year, $week, 7);
        return array($startDateTime->getTimestamp(), $endDateTime->getTimestamp());
    }

    /**
     * @param \DateTime $dateRef
     * @param string $direction
     */
    public function mutateToClosestWorkDay(\DateTime $dateRef,$direction = '>') {
        $dateRefInitTimestamp = $dateRef->getTimestamp();
        $dateRefInitDayOfWeek = date("N", $dateRefInitTimestamp);
        if($dateRefInitDayOfWeek <= 5) {
            return;
        } else {
            $targetTimestamp = strtotime('+1 weekdays',$dateRefInitTimestamp);
            if($direction === '<') {
                $targetTimestamp = strtotime('-1 weekdays',$dateRefInitTimestamp);
            }
            $dateRef->setTimestamp($targetTimestamp);
        }
    }
}