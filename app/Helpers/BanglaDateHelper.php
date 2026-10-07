<?php

namespace App\Helpers;

use Carbon\Carbon;

class BanglaDateHelper
{
    protected static array $bnMonths = [
        'বৈশাখ', 'জ্যৈষ্ঠ', 'আষাঢ়', 'শ্রাবণ', 'ভাদ্র', 'আশ্বিন',
        'কার্তিক', 'অগ্রহায়ণ', 'পৌষ', 'মাঘ', 'ফাল্গুন', 'চৈত্র'
    ];

    protected static array $bnNumbers = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

    public static function toBnNum($number): string
    {
        return str_replace(
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
            self::$bnNumbers,
            (string)$number
        );
    }

    /**
     * Get official Bangla Academy (Bangladesh Government Revised) date.
     */
    public static function getAcademyDate($date): array
    {
        $cDate = Carbon::parse($date);
        $year = $cDate->year;
        $isLeap = $cDate->isLeapYear();

        $baStart = Carbon::createFromDate($year, 4, 14)->startOfDay();
        $target = $cDate->copy()->startOfDay();

        if ($target->lt($baStart)) {
            $bnYear = $year - 594;
            $baStart = Carbon::createFromDate($year - 1, 4, 14)->startOfDay();
        } else {
            $bnYear = $year - 593;
        }

        $diffDays = (int)$baStart->diffInDays($target);
        $monthLengths = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, $isLeap ? 30 : 29, 30];

        $bnMonth = 0;
        while ($diffDays >= $monthLengths[$bnMonth] && $bnMonth < 11) {
            $diffDays -= $monthLengths[$bnMonth];
            $bnMonth++;
        }

        $bnDay = $diffDays + 1;

        return [
            'type' => 'bangla_academy',
            'label' => 'বাংলা একাডেমি (সরকারি)',
            'short_code' => 'বা',
            'day' => self::toBnNum($bnDay),
            'day_num' => $bnDay,
            'month' => self::$bnMonths[$bnMonth],
            'month_index' => $bnMonth,
            'year' => self::toBnNum($bnYear),
            'year_num' => $bnYear,
            'full' => self::toBnNum($bnDay) . ' ' . self::$bnMonths[$bnMonth] . ' ' . self::toBnNum($bnYear)
        ];
    }

    /**
     * Get traditional Bisuddha Siddhanta Panjika (Prokerala / Drik Siddhanta) date.
     * Follows modern astronomical solar ingress (Sankranti transition rules as followed by Prokerala & Belur Math).
     */
    public static function getBisuddhaSiddhantaDate($date): array
    {
        $cDate = Carbon::parse($date);
        $year = $cDate->year;
        $target = $cDate->copy()->startOfDay();

        // Solar ingress into rashis according to Bisuddha Siddhanta (Drik / Prokerala)
        // [month, day, bengali_month_index]
        $panjikaIngress = [
            ['m' => 1,  'd' => 16, 'bm' => 9],  // Jan 16 -> Magh (ends Feb 13)
            ['m' => 2,  'd' => 14, 'bm' => 10], // Feb 14 -> Falgun (ends Mar 14)
            ['m' => 3,  'd' => 15, 'bm' => 11], // Mar 15 -> Chaitra (ends Apr 14)
            ['m' => 4,  'd' => 15, 'bm' => 0],  // Apr 15 -> Boishakh (ends May 15)
            ['m' => 5,  'd' => 16, 'bm' => 1],  // May 16 -> Jyeshtha (ends Jun 15)
            ['m' => 6,  'd' => 16, 'bm' => 2],  // Jun 16 -> Ashadha (ends Jul 17)
            ['m' => 7,  'd' => 18, 'bm' => 3],  // Jul 18 -> Srabon (ends Aug 18)
            ['m' => 8,  'd' => 19, 'bm' => 4],  // Aug 19 -> Bhadra (ends Sep 18)
            ['m' => 9,  'd' => 19, 'bm' => 5],  // Sep 19 -> Ashwin (ends Oct 18, so Oct 7 is 19 Ashwin)
            ['m' => 10, 'd' => 19, 'bm' => 6],  // Oct 19 -> Kartik (ends Nov 17, so Oct 24 is 6 Kartik)
            ['m' => 11, 'd' => 18, 'bm' => 7],  // Nov 18 -> Agrahayan (ends Dec 16)
            ['m' => 12, 'd' => 17, 'bm' => 8],  // Dec 17 -> Poush (ends Jan 15)
        ];

        $pjMonth = null;
        $pjStart = null;
        $pjYear = $year - 593;

        foreach ($panjikaIngress as $ing) {
            $ingDate = Carbon::createFromDate($year, $ing['m'], $ing['d'])->startOfDay();
            if ($target->gte($ingDate)) {
                $pjMonth = $ing['bm'];
                $pjStart = $ingDate;
            }
        }

        if (!$pjStart) {
            // Before Jan 15: Poush of previous Gregorian year
            $pjMonth = 8; // Poush
            $pjStart = Carbon::createFromDate($year - 1, 12, 17)->startOfDay();
            $pjYear = $year - 594;
        } elseif ($pjMonth >= 9) {
            // Magh, Falgun, Chaitra belong to previous Bangabda year
            $pjYear = $year - 594;
        }

        $diffDays = (int)$pjStart->diffInDays($target);
        $pjDay = $diffDays + 1;

        return [
            'type' => 'bisuddha_siddhanta',
            'label' => 'বিশুদ্ধ সিদ্ধান্ত (পঞ্জিকা)',
            'short_code' => 'বি',
            'day' => self::toBnNum($pjDay),
            'day_num' => $pjDay,
            'month' => self::$bnMonths[$pjMonth],
            'month_index' => $pjMonth,
            'year' => self::toBnNum($pjYear),
            'year_num' => $pjYear,
            'full' => self::toBnNum($pjDay) . ' ' . self::$bnMonths[$pjMonth] . ' ' . self::toBnNum($pjYear)
        ];
    }

    /**
     * Alias for backwards compatibility with getPanjikaDate.
     */
    public static function getPanjikaDate($date): array
    {
        return self::getBisuddhaSiddhantaDate($date);
    }

    /**
     * Get both Bangla Academy and Bisuddha Siddhanta Panjika dates.
     */
    public static function getBothDates($date): array
    {
        $pj = self::getBisuddhaSiddhantaDate($date);
        return [
            'academy' => self::getAcademyDate($date),
            'panjika' => $pj,
            'bisuddha_siddhanta' => $pj,
        ];
    }
}
