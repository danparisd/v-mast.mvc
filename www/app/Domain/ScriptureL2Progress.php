<?php


namespace App\Domain;


use Helpers\Constants\EventCheckSteps;
use Helpers\Constants\StepsStates;

class ScriptureL2Progress
{
    public static function calculateEventProgress(
        $event,
        $progressOnly = false
    ) {
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
                $tmp["peer1Check"] = (array)json_decode($chapter->checkerL2->peer1Check, true);
                $tmp["peer2Check"] = (array)json_decode($chapter->checkerL2->peer2Check, true);
            }

            $data["chapters"][$chapter["chapter"]] = $tmp;
        }

        $overallProgress = 0;
        $members = [];

        foreach ($data["chapters"] as $key => $chapter) {
            if ($chapter["l2memberID"] == 0) continue;

            $snd = !empty($chapter["sndCheck"])
                && array_key_exists($key, $chapter["sndCheck"]);
            $p1 = !empty($chapter["peer1Check"])
                && array_key_exists($key, $chapter["peer1Check"])
                && $chapter["peer1Check"][$key]["memberID"] > 0;
            $p2 = !empty($chapter["peer2Check"])
                && array_key_exists($key, $chapter["peer2Check"])
                && $chapter["peer2Check"][$key]["memberID"] > 0;

            $members[$chapter["l2memberID"]] = "";
            $data["chapters"][$key]["progress"] = 0;

            // Set default values
            $data["chapters"][$key]["consume"]["state"] = StepsStates::NOT_STARTED;
            $data["chapters"][$key]["fstChk"]["state"] = StepsStates::NOT_STARTED;

            $data["chapters"][$key]["sndChk"]["state"] = StepsStates::NOT_STARTED;
            $data["chapters"][$key]["keywordsChk"]["state"] = StepsStates::NOT_STARTED;
            $data["chapters"][$key]["sndChk"]["checkerID"] = 'na';

            $data["chapters"][$key]["peerChk"]["state"] = StepsStates::NOT_STARTED;
            $data["chapters"][$key]["peerChk"]["checkerID1"] = 'na';
            $data["chapters"][$key]["peerChk"]["checkerID2"] = 'na';

            $currentChapter = $chapter["currentChapter"];
            $currentStep = $chapter["step"];

            if ($snd) {
                // First check
                $data["chapters"][$key]["consume"]["state"] = StepsStates::FINISHED;
                $data["chapters"][$key]["fstChk"]["state"] = StepsStates::FINISHED;

                if ($chapter["sndCheck"][$key]["memberID"] > 0) {
                    $members[$chapter["sndCheck"][$key]["memberID"]] = "";
                    $data["chapters"][$key]["sndChk"]["checkerID"] = $chapter["sndCheck"][$key]["memberID"];

                    if ($chapter["sndCheck"][$key]["done"] == 2) {
                        // Second check
                        $data["chapters"][$key]["sndChk"]["state"] = StepsStates::FINISHED;
                        $data["chapters"][$key]["keywordsChk"]["state"] = StepsStates::FINISHED;

                        // Peer check
                        if ($p1 && $p2) {
                            $members[$chapter["peer1Check"][$key]["memberID"]] = "";
                            $members[$chapter["peer2Check"][$key]["memberID"]] = "";

                            $data["chapters"][$key]["peerChk"]["checkerID1"] = $chapter["peer1Check"][$key]["memberID"];
                            $data["chapters"][$key]["peerChk"]["checkerID2"] = $chapter["peer2Check"][$key]["memberID"];

                            if ($chapter["peer2Check"][$key]["done"] == 1) {
                                if ($chapter["peer1Check"][$key]["done"] == 1) {
                                    $data["chapters"][$key]["peerChk"]["state"] = StepsStates::FINISHED;
                                } else {
                                    $data["chapters"][$key]["peerChk"]["state"] = StepsStates::CHECKED;
                                }
                            } else {
                                $data["chapters"][$key]["peerChk"]["state"] = StepsStates::IN_PROGRESS;
                            }
                        } else if ($p1 && !$p2) {
                            $members[$chapter["peer1Check"][$key]["memberID"]] = "";
                            $data["chapters"][$key]["peerChk"]["checkerID1"] = $chapter["peer1Check"][$key]["memberID"];
                            $data["chapters"][$key]["peerChk"]["state"] = StepsStates::WAITING;
                        } else {
                            $data["chapters"][$key]["peerChk"]["state"] = StepsStates::WAITING;
                        }
                    } else if ($chapter["sndCheck"][$key]["done"] == 1) {
                        $data["chapters"][$key]["sndChk"]["state"] = StepsStates::FINISHED;
                        $data["chapters"][$key]["keywordsChk"]["state"] = StepsStates::IN_PROGRESS;
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
                    } else if ($currentStep == EventCheckSteps::FST_CHECK) {
                        $data["chapters"][$key]["consume"]["state"] = StepsStates::FINISHED;
                        $data["chapters"][$key]["fstChk"]["state"] = StepsStates::IN_PROGRESS;
                    }
                }
            }

            // Progress checks
            if ($data["chapters"][$key]["consume"]["state"] == StepsStates::FINISHED)
                $data["chapters"][$key]["progress"] += 20;
            if ($data["chapters"][$key]["fstChk"]["state"] == StepsStates::FINISHED)
                $data["chapters"][$key]["progress"] += 20;
            if ($data["chapters"][$key]["sndChk"]["state"] == StepsStates::FINISHED)
                $data["chapters"][$key]["progress"] += 20;
            if ($data["chapters"][$key]["keywordsChk"]["state"] == StepsStates::FINISHED)
                $data["chapters"][$key]["progress"] += 20;
            if ($data["chapters"][$key]["peerChk"]["state"] == StepsStates::CHECKED)
                $data["chapters"][$key]["progress"] += 10;
            if ($data["chapters"][$key]["peerChk"]["state"] == StepsStates::FINISHED)
                $data["chapters"][$key]["progress"] += 20;

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