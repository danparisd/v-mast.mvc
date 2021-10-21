<?php


namespace App\Domain;


use Helpers\Constants\EventCheckSteps;
use Helpers\Constants\StepsStates;

class SunRevisionProgress
{
    public static function calculateEventProgress($event, $progressOnly = false) {
        $data = [];
        $data["overall_progress"] = 0;
        $data["chapters"] = [];
        for ($i = 1; $i <= $event->bookInfo->chaptersNum; $i++) {
            $data["chapters"][$i] = [];
        }

        foreach ($event->chapters as $chapter) {
            $tmp["l2chID"] = $chapter->l2chID;
            $tmp["l2memberID"] = $chapter->l2memberID;
            $tmp["l2checked"] = $chapter->l2checked;

            if ($chapter->checkerL2) {
                $tmp["currentChapter"] = $chapter->checkerL2->currentChapter;
                $tmp["step"] = $chapter->checkerL2->step;
                $tmp["sndCheck"] = (array)json_decode($chapter->checkerL2->sndCheck, true);
            }

            $data["chapters"][$chapter->chapter] = $tmp;
        }

        $overallProgress = 0;
        $members = [];

        foreach ($data["chapters"] as $key => $chapter) {
            if (empty($chapter)) continue;
            if ($chapter["l2memberID"] == 0) continue;

            $snd = !empty($chapter["sndCheck"])
                && array_key_exists($key, $chapter["sndCheck"]);

            $members[$chapter["l2memberID"]] = "";
            $data["chapters"][$key]["progress"] = 0;

            // Set default values
            $data["chapters"][$key]["consume"]["state"] = StepsStates::NOT_STARTED;
            $data["chapters"][$key]["fstChk"]["state"] = StepsStates::NOT_STARTED;

            $data["chapters"][$key]["sndChk"]["state"] = StepsStates::NOT_STARTED;
            $data["chapters"][$key]["sndChk"]["checkerID"] = 'na';

            $currentChapter = $chapter["currentChapter"];
            $currentStep = $chapter["step"];

            if ($snd) {
                // Peer check
                $data["chapters"][$key]["consume"]["state"] = StepsStates::FINISHED;
                $data["chapters"][$key]["fstChk"]["state"] = StepsStates::FINISHED;

                if ($chapter["sndCheck"][$key]["memberID"] > 0) {
                    $members[$chapter["sndCheck"][$key]["memberID"]] = "";
                    $data["chapters"][$key]["sndChk"]["checkerID"] = $chapter["sndCheck"][$key]["memberID"];

                    if ($chapter["sndCheck"][$key]["done"] == 1) {
                        // Theological check
                        $data["chapters"][$key]["sndChk"]["state"] = StepsStates::FINISHED;
                    } else {
                        $data["chapters"][$key]["sndChk"]["state"] = StepsStates::IN_PROGRESS;
                    }
                } else {
                    $data["chapters"][$key]["sndChk"]["state"] = StepsStates::WAITING;
                }
            } else {
                if ($currentChapter == $key) {
                    if ($currentStep == EventCheckSteps::CONSUME) {
                        $data["chapters"][$key]["consume"]["state"] = StepsStates::IN_PROGRESS;
                    } else if ($currentStep == EventCheckSteps::SELF_CHECK) {
                        $data["chapters"][$key]["consume"]["state"] = StepsStates::FINISHED;
                        $data["chapters"][$key]["fstChk"]["state"] = StepsStates::IN_PROGRESS;
                    }
                }
            }

            // Progress checks
            if ($data["chapters"][$key]["consume"]["state"] == StepsStates::FINISHED)
                $data["chapters"][$key]["progress"] += 33;
            if ($data["chapters"][$key]["fstChk"]["state"] == StepsStates::FINISHED)
                $data["chapters"][$key]["progress"] += 33;
            if ($data["chapters"][$key]["sndChk"]["state"] == StepsStates::FINISHED)
                $data["chapters"][$key]["progress"] += 34;

            $overallProgress += $data["chapters"][$key]["progress"];
        }

        $data["overall_progress"] = $overallProgress / sizeof($data["chapters"]);
        $data["members"] = $members;

        if ($progressOnly) {
            return $data["overall_progress"];
        } else {
            return $data;
        }
    }
}