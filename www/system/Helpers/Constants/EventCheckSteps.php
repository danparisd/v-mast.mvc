<?php
/**
 * Created by PhpStorm.
 * User: Maxim
 * Date: 29 Feb 2016
 * Time: 19:41
 */

namespace Helpers\Constants;

class EventCheckSteps
{
    const NONE                  = "none";
    const PRAY                  = "pray";
    const CONSUME               = "consume";
    const FST_CHECK             = "fst-check";
    const SND_CHECK             = "snd-check";
    const KEYWORD_CHECK_L2      = "keyword-check-l2";
    const PEER_REVIEW_L2        = "peer-review-l2";
    const PEER_REVIEW_L3        = "peer-review-l3";
    const PEER_EDIT_L3          = "peer-edit-l3";

    private static $enum = [
        "none" => 0,
        "pray" => 1,
        "consume" => 2,
        "fst-check" => 3,
        "snd-check" => 4,
        "keyword-check-l2" => 5,
        "peer-review-l2" => 6
        ];

    private static $enumL3 = [
        "none" => 0,
        "pray" => 1,
        "peer-review-l3" => 2,
        "peer-edit-l3" => 3
    ];

    public static function enum($step, $mode = null)
    {
        switch($mode)
        {
            case "l3":
                return self::$enumL3[$step];

            default:
                return self::$enum[$step];
        }
    }

    public static function enumArray($mode = null)
    {
        switch($mode)
        {
            case "l3":
                return self::$enumL3;

            default:
                return self::$enum;
        }
    }
}