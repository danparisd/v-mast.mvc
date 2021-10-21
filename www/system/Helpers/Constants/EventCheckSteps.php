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
    const SELF_CHECK            = "self-check";
    const PEER_REVIEW           = "peer-review";
    const KEYWORD_CHECK         = "keyword-check";
    const CONTENT_REVIEW        = "content-review";
    const PEER_REVIEW_L3        = "peer-review-l3";
    const PEER_EDIT_L3          = "peer-edit-l3";

    private static $enum = [
        "none" => 0,
        "pray" => 1,
        "consume" => 2,
        "self-check" => 3,
        "peer-review" => 4,
        "keyword-check" => 5,
        "content-review" => 6
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