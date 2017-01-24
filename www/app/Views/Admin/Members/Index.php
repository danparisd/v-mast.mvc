<?php
/**
 * Created by PhpStorm.
 * User: mXaln
 * Date: 03.11.2016
 * Time: 16:19
 */

?>
<ul class="nav nav-tabs">
    <li role="presentation" id="verify_members" class="mems_tab active">
        <a href="#"><?php echo __("new_members_title") ?>
            <span>(<?php echo sizeof($data["newMembers"]) ?>)</span>
        </a>
    </li>
    <li role="presentation" id="all_members" class="mems_tab">
        <a href="#"><?php echo __("all_members") ?>
            <span></span>
        </a>
    </li>
</ul>

<div class="members_content shown" id="verify_members_content">
    <div class="panel panel-default">
        <div class="dt-bootstrap no-footer">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-hover" role="grid">
                        <thead>
                        <tr>
                            <th><?php echo __("userName") ?></th>
                            <th><?php echo __("name") ?></th>
                            <th><?php echo __("Email") ?></th>
                            <th><?php echo __("prefered_roles") ?></th>
                            <th><?php echo __("activated") ?></th>
                            <th colspan="2"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data["newMembers"] as $member):?>
                            <tr>
                                <td><a href="/members/profile/<?php echo $member->memberID ?>"><?php echo $member->userName ?></a></td>
                                <td><?php echo $member->firstName . " " . $member->lastName ?></td>
                                <td><?php echo $member->email ?></td>
                                <td>
                                    <?php
                                    if(isset($member->prefered_roles) && $member->prefered_roles != "")
                                    {
                                        $preferedRoles = array_map(function($value) {
                                            return __($value);
                                        }, (array)json_decode($member->prefered_roles, true));
                                        echo join(", ", $preferedRoles);
                                    }
                                    else
                                    {
                                        echo "<span style='color: #f00;'>".__("empty_profile_error")."</span>";
                                    }
                                    ?>
                                </td>
                                <td><input type="checkbox" class="activateMember" data="<?php echo $member->memberID; ?>" <?php echo $member->active ? "checked='checked'" : "" ?> disabled='disabled'></td>
                                <td><button class="btn btn-primary verifyMember" data="<?php echo $member->memberID; ?>"><?php echo __("verify") ?></button></td>
                                <td><button class="blockMember btn <?php echo $member->blocked ? "btn-primary" : "btn-danger" ?>" data="<?php echo $member->memberID ?>">
                                        <?php echo $member->blocked ? __("unblock") : __("block") ?>
                                    </button></td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="members_content" id="all_members_content">
    <div class="members_filter">
        <form id="membersFilter">
            <div class="mems_filter_item filter_title"><?php echo __("filter") ?>:</div>
            <div class="mems_filter_item filter_search">
                <input name="name" class="form-control" type="text" value="" placeholder="<?php echo __("search_name_filter") ?>" size="28">
            </div>
            <div class="mems_filter_item filter_roles">
                <div><label for="filterTr"><input name="role" id="filterTr" type="radio" value="translators"> <?php echo __("translators") ?></label></div>
                <div><label for="filterFc"><input name="role" id="filterFc" type="radio" value="facilitators"> <?php echo __("facilitators") ?></label></div>
                <div><label for="filterAll"><input name="role" id="filterAll" type="radio" value="all" checked> <?php echo __("all_mems") ?></label></div>
            </div>
            <div class="mems_filter_item filter_lang">
                <select class="mems_language" name="language" data-placeholder="<?php echo __('select_lang_option'); ?>">
                    <option></option>
                    <?php foreach ($data["languages"] as $lang):?>
                        <option value="<?php echo $lang->langID; ?>"><?php echo "[".$lang->langID."] " . $lang->langName . ($lang->langName != $lang->angName ? " ( ".$lang->angName." )" : ""); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mems_filter_item filter_apply">
                <button class="btn btn-success"><?php echo __("apply_filter") ?></button>
            </div>
            <div class="mems_filter_item filter_clear">
                <button class="btn btn-primary"><?php echo __("clear_filter") ?></button>
            </div>
            <div class="mems_filter_item filter_loader">
                <img src="<?php echo template_url("img/loader.gif") ?>">
            </div>
            <input type="hidden" class="filter_page" autocomplete="off" name="page" value="2">
            <div class="clear"></div>
        </form>
    </div>

    <div class="panel panel-default" id="all_members_table" class="<?php echo $data["count"] <= 0 ? "hidden" : "" ?>">
        <div class="dt-bootstrap no-footer">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-hover" role="grid">
                        <thead>
                            <tr>
                                <th><?php echo __("userName") ?></th>
                                <th><?php echo __("name") ?></th>
                                <th><?php echo __("Email") ?></th>
                                <th><?php echo __("prefered_roles") ?></th>
                                <th><?php echo __("facilitator") ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($data["members"] as $member):?>
                            <tr>
                                <td><a href="/members/profile/<?php echo $member->memberID ?>"><?php echo $member->userName ?></a></td>
                                <td><?php echo $member->firstName . " " . $member->lastName ?></td>
                                <td><?php echo $member->email ?></td>
                                <td>
                                    <?php
                                    if(isset($member->prefered_roles) && $member->prefered_roles != "")
                                    {
                                        $preferedRoles = array_map(function($value) {
                                            return __($value);
                                        }, (array)json_decode($member->prefered_roles, true));
                                        echo join(", ", $preferedRoles);
                                    }
                                    else
                                    {
                                        echo "<span style='color: #f00;'>".__("empty_profile_error")."</span>";
                                    }
                                    ?>
                                </td>
                                <td><input type='checkbox' <?php echo $member->isAdmin ? "checked" : "" ?> disabled></td>
                                <td>
                                    <button class="blockMember btn <?php echo $member->blocked ? "btn-primary" : "btn-danger" ?>" data="<?php echo $member->memberID ?>">
                                        <?php echo $member->blocked ? __("unblock") : __("block") ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Show "more" button if more than one page with 50 rows -->
    <?php if($data["count"] > sizeof($data["members"])): ?>
        <div id="search_more"><?php echo __("search_more"); ?></div>
    <?php endif; ?>
</div>

<link href="<?php echo template_url("css/chosen.min.css")?>" type="text/css" rel="stylesheet" />
<script src="<?php echo template_url("js/chosen.jquery.min.js")?>"></script>